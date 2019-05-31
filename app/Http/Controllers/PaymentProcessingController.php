<?php


namespace App\Http\Controllers;
use App\Helpers\InvoicePDF;
use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBillingHistoryHeaderDetails;
use App\Models\MBillingHistoryHeaders;
use App\Models\MBillingHistoryHeadersDetails;
use App\Models\MBusinessOffices;
use App\Models\MCustomers;
use App\Models\MGeneralPurposes;
use App\Models\MNumberings;
use App\Models\MSaleses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PaymentProcessingController extends Controller{
    use ListTrait, FormTrait;
    public $table = "t_billing_history_headers";
    public $allNullAble = false;
    public $beforeItem = null;
    public $add_log = false;

    public $ruleValid = [
        'dw_day' => 'required',
        'invoice_balance_total' => 'required|one_byte_number|length:13',
        'dw_classification' => 'required|one_byte_number|length:13',
        'fee' => 'required|one_byte_number|length:13',
        'discount' => 'required|one_byte_number|length:13',
        'total_payment_amount' => 'required|one_byte_number|length:13',
        'item_payment_total' => 'required|one_byte_number|length:13',
        'note' => 'nullable|length:200',
    ];

    public $labels = [
        'customer_cd' => '得意先',
        'dw_day' => '入金日',
        'invoice_balance_total' => '請求残合計',
        'dw_classification' => '入金区分',
        'payment_amount' => '入金額',
        'fee' => '手数料',
        'discount' => '値引き',
        'total_payment_amount' => '入金額合計',
        'item_payment_total' => '明細入金合計',
        'note' => '備考',
    ];


    public function __construct(){
        date_default_timezone_set("Asia/Tokyo");
        parent::__construct();
    }

    public function getItems(Request $request)
    {

        if(Session::exists('backQueryFlag') && Session::get('backQueryFlag')){
            if(Session::exists('backQueryFlag') ){
                $data = Session::get('requestHistory');
            }
            Session::put('backQueryFlag', false);
        }else{
            $data = $request->all();
            Session::put('requestHistory', $data);
        }
        $fieldSearch = $data['fieldSearch'];
        $validator = Validator::make( $fieldSearch, ['customer_cd' => 'required'] ,['required' => Lang::get('messages.MSG10029')] ,$this->labels );
        if ( $validator->fails() ) {
            return response()->json([
                'success'=>FALSE,
                'message'=> $validator->errors()
            ]);
        }else {
            $items = $this->search($data);
            $response = [
                'success' => true,
                'data' => $items,
                'fieldSearch' => $data['fieldSearch'],
            ];
            return response()->json($response);
        }
    }

    protected function search($data){
        $dataSearch=$data['fieldSearch'];
        $this->query = DB::table(DB::raw($this->table.' as billing') )->select(
                'billing.invoice_number',
                'billing.mst_business_office_id',
                DB::raw("office.business_office_nm as office_nm"),
                DB::raw("DATE_FORMAT(publication_date, '%Y/%m/%d') as publication_date"),
                DB::raw("IFNULL(billing.total_fee,0) as total_fee"),
                DB::raw("IFNULL(billing.consumption_tax,0) as consumption_tax"),
                DB::raw("IFNULL(billing.tax_included_amount,0) as tax_included_amount"),
                DB::raw("IFNULL(payment.total_dw_amount,0) as last_payment_amount"),
                DB::raw("0 as fee"),
                DB::raw("0 as discount"),
                DB::raw("0 as total_dw_amount"),
                DB::raw("IFNULL(IFNULL(billing.tax_included_amount,0)- IFNULL(payment.total_dw_amount,0),0)  as payment_remaining")
            )
            ->whereNull('billing.deleted_at')
            ->leftJoin(DB::raw('( SELECT invoice_number, IFNULL( SUM( total_dw_amount ), 0 ) AS total_dw_amount FROM t_payment_histories WHERE deleted_at IS NULL GROUP BY invoice_number ) payment'),
                function($join)
                {
                    $join->on('billing.invoice_number', '=', 'payment.invoice_number')
                    ->whereRaw('billing.tax_included_amount - payment.total_dw_amount > 0');
                })
            ->join(DB::raw('mst_business_offices office'), function ($join) {
                $join->on('office.id', '=', 'billing.mst_business_office_id')
                    ->whereNull('office.deleted_at');
            });
        if ($dataSearch['customer_cd'] != '') {
            $this->query->where('billing.mst_customers_cd','=',$dataSearch['customer_cd']);
        }
        $this->query->orderBy('mst_business_office_id')
        ->orderBy('invoice_number');
        return $this->query->get();
    }

    public function index(Request $request){
        $fieldShowTable = [
            'invoice_number' => [
                "classTH" => "wd-60",
            ],
            'publication_date'=> [
                "classTH" => "wd-60",
            ],
            'office_nm'=> [
                "classTH" => "wd-120",
            ],
            'total_fee'=> [
                "classTH" => "wd-100",
            ],
            'consumption_tax'=> [
                "classTH" => "wd-100",
            ],
            'tax_included_amount'=> [
                "classTH" => "wd-120",
            ],
            'last_payment_amount'=> [
                "classTH" => "wd-60",
            ],
            'total_dw_amount'=> [
                "classTH" => "wd-120",
            ],
            'fee'=> [
                "classTH" => "wd-60",
            ],
            'discount'=> [
                "classTH" => "wd-60",
            ],
            'payment_remaining'=> [
                "classTH" => "wd-60",
            ],
        ];
        $currentDate = date("Y/m/d",time());
        $mGeneralPurposes = new MGeneralPurposes();
        $listDepositMethod= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.deposit_method'),'Empty');
        return view('payment_processing.index',[
            'fieldShowTable'=>$fieldShowTable,
            'listDepositMethod'=>$listDepositMethod,
            'currentDate'=>$currentDate,
        ]);
    }

    public function getListCustomers(){
        $mCustomer = new MCustomers();
        $data = $mCustomer->select('mst_customers_cd','customer_nm')->whereNull('deleted_at')->orderBy('customer_nm_kana')->get();
        return Response()->json(array('success'=>true,'data'=>$data));
    }

    public function save(Request $request){
        $data = $request->all();
        $dataPayment = $data['dataPayment'];
        $listInvoice = $data['listInvoice'];
        $error = [];
        $validator = Validator::make( $dataPayment, $this->ruleValid ,[] ,$this->labels );
        $validator->after(function($validator) use ($listInvoice) {

            foreach ($listInvoice as $key => $value) {
                $validatorEx = Validator::make( $value, [
                    'total_dw_amount' => 'nullable|length:13',
                    'discount' => 'nullable|length:13'
                ] ,[] ,$this->labels );
                dd($validatorEx->errors()->first());
                if ( $validatorEx->fails() ) {
                    $error['total_dw_amount']['message'] = $validatorEx->errors()->first();
                    dd($validatorEx->errors()->first());
                }
            }
        });
        if ( $validator->fails() ) {
            return response()->json([
                'success'=>FALSE,
                'errorValidate'=> $validator->errors()
            ]);
        }else {
            if (is_null($dataPayment['payment_amount']) || $dataPayment['payment_amount'] == 0) {
                $error['payment_amount'] = Lang::get('messages.MSG10032');
            } else {
                if ($dataPayment['payment_amount'] != $dataPayment['item_payment_total']) {
                    $error['payment_amount'] = Lang::get('messages.MSG10031');
                }
            }
            $error['total_dw_amount'] = [
                'message' => [],
                'indexError' => [],
            ];
            foreach ($listInvoice as $key => $value) {
                if ($value['payment_remaining'] < 0) {
                    if (!in_array(Lang::get('messages.MSG10030'), $error['total_dw_amount']['message'])) {
                        array_push($error['total_dw_amount']['message'], Lang::get('messages.MSG10030'));
                    }
                    if (!in_array($key, $error['total_dw_amount']['indexError'])) {
                        array_push($error['total_dw_amount']['indexError'], $key);
                    }
                }
                if ($value['total_dw_amount'] <= 0) {
                    if (!in_array(Lang::get('messages.MSG10032'), $error['total_dw_amount']['message']) && (!isset($error['payment_amount']) || isset($error['payment_amount']) && $error['payment_amount'] != Lang::get('messages.MSG10032'))) {
                        array_push($error['total_dw_amount']['message'], Lang::get('messages.MSG10032'));
                    }
                    if (!in_array($key, $error['total_dw_amount']['indexError'])) {
                        array_push($error['total_dw_amount']['indexError'], $key);
                    }
                }
            }
            return response()->json([
                'success'=>FALSE,
                'error'=> $error
            ]);
        }


    }

}