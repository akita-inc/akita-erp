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
use App\Models\TPaymentHistories;
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
        'total_discount' => 'required|one_byte_number|length:13',
        'total_payment_amount' => 'required|one_byte_number|length:13',
        'item_payment_total' => 'required|one_byte_number|length:13',
        'note' => 'nullable|length:200',
    ];

    public $labels = [
        'total_dw_amount' => '入金額',
        'customer_cd' => '得意先',
        'dw_day' => '入金日',
        'invoice_balance_total' => '請求残合計',
        'dw_classification' => '入金区分',
        'payment_amount' => '入金額',
        'fee' => '手数料',
        'discount' => '値引き',
        'total_discount' => '値引き',
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
            ->join(DB::raw('( SELECT invoice_number, IFNULL( SUM( total_dw_amount ), 0 ) AS total_dw_amount FROM t_payment_histories WHERE deleted_at IS NULL GROUP BY invoice_number ) payment'),
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
                "classTH" => "wd-100",
            ],
            'publication_date'=> [
                "classTH" => "wd-80",
            ],
            'office_nm'=> [
                "classTH" => "wd-100",
            ],
            'total_fee'=> [
                "classTH" => "wd-120",
            ],
            'consumption_tax'=> [
                "classTH" => "wd-120",
            ],
            'tax_included_amount'=> [
                "classTH" => "wd-120",
            ],
            'last_payment_amount'=> [
                "classTH" => "wd-120",
            ],
            'total_dw_amount'=> [
                "classTH" => "wd-80",
            ],
            'fee'=> [
                "classTH" => "wd-120",
            ],
            'discount'=> [
                "classTH" => "wd-80",
            ],
            'payment_remaining'=> [
                "classTH" => "wd-120",
            ],
        ];
        $currentDate = date("Y/m/d",time());
        $mGeneralPurposes = new MGeneralPurposes();
        $listDepositMethod= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.payment_method_id'),'Empty');
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

    public function submit(Request $request){
        $data = $request->all();
        $dataPayment = $data['dataPayment'];
        $dataPayment['total_discount']= number_format($dataPayment['total_discount'],0,null,'');
        $dataPayment['fee']= number_format($dataPayment['fee'],0,null,'');
        $dataPayment['invoice_balance_total']= number_format($dataPayment['invoice_balance_total'],0,null,'');
        $dataPayment['item_payment_total']= number_format($dataPayment['item_payment_total'],0,null,'');
        $dataPayment['payment_amount']= number_format($dataPayment['payment_amount'],0,null,'');
        $dataPayment['total_payment_amount']= number_format($dataPayment['total_payment_amount'],0,null,'');
        $listInvoice = $data['listInvoice'];
        $error = [];
        $validator = Validator::make( $dataPayment, $this->ruleValid ,[] ,$this->labels );
        $validator->after(function($validator) use ($listInvoice,$error) {
            foreach ($listInvoice as $key => $value) {
                if(!$value) continue;
                $value['total_dw_amount'] = number_format($value['total_dw_amount'],0,null,'');
                $value['discount'] = number_format($value['discount'],0,null,'');
                $validatorEx = Validator::make( $value, [
                    'total_dw_amount' => 'nullable|one_byte_number|length:13',
                    'discount' => 'nullable|one_byte_number|length:13'
                ] ,[] ,$this->labels );
                if ( $validatorEx->fails() ) {
                    $failedRules = $validatorEx->failed();
                    foreach ($failedRules as $field => $errors) {
                        if(!isset($error[$field])){
                            $error[$field] = [
                                'message' => "",
                                'indexError' => [],
                            ];
                        }
                        if($error[$field]['message']==''){
                            $error[$field]['message'] = $validatorEx->errors()->first($field);
                        }
                        array_push($error[$field]['indexError'], $key);
                    }
                }
            }
            if(count($error) > 0){
                $validator->errors()
                    ->add("listInvoice", $error);
            }
        });

        if ( $validator->fails() ) {
            return response()->json([
                'success'=>false,
                'errorValidate'=> $validator->errors(),
                'error'=> []
            ]);
        }else {
            if (is_null($dataPayment['payment_amount']) || $dataPayment['payment_amount'] == 0) {
                $error['payment_amount'] = Lang::get('messages.MSG10032');
            } else {
                if ($dataPayment['payment_amount'] != $dataPayment['item_payment_total']) {
                    $error['payment_amount'] = Lang::get('messages.MSG10031');
                }
            }

            foreach ($listInvoice as $key => $value) {
                if(!$value) continue;
                if ($value['payment_remaining'] < 0) {
                    if(!isset($error['total_dw_amount'])){
                        $error['total_dw_amount'] = [
                            'message' => [],
                            'indexError' => [],
                        ];
                    }
                    if (!in_array(Lang::get('messages.MSG10030'), $error['total_dw_amount']['message'])) {
                        array_push($error['total_dw_amount']['message'], Lang::get('messages.MSG10030'));
                    }
                    if (!in_array($key, $error['total_dw_amount']['indexError'])) {
                        array_push($error['total_dw_amount']['indexError'], $key);
                    }
                }
                if ($value['total_dw_amount'] <= 0) {
                    if(!isset($error['total_dw_amount'])){
                        $error['total_dw_amount'] = [
                            'message' => [],
                            'indexError' => [],
                        ];
                    }
                    if (!in_array(Lang::get('messages.MSG10032'), $error['total_dw_amount']['message']) && (!isset($error['payment_amount']) || isset($error['payment_amount']) && $error['payment_amount'] != Lang::get('messages.MSG10032'))) {
                        array_push($error['total_dw_amount']['message'], Lang::get('messages.MSG10032'));
                    }
                    if (!in_array($key, $error['total_dw_amount']['indexError'])) {
                        array_push($error['total_dw_amount']['indexError'], $key);
                    }
                }
            }
            if(count($error) > 0){
                return response()->json([
                    'success'=>FALSE,
                    'error'=> $error,
                    'errorValidate'=> [],
                ]);
            }
        }
        $this->save($data);

    }

    public function save($data){
        $dataPayment = $data['dataPayment'];
        $listInvoice = $data['listInvoice'];
        $dataSearch = $data['dataSearch'];
        $currentTime = date("Y-m-d H:i:s",time());
        $listPayment = [];
        $mNumberings =  new MNumberings();
        $serial_number = $mNumberings->getSerialNumberByTargetID('4001');
        $branch_number = 1;
        foreach ($listInvoice as $key => $value) {
            $row = [];
            $row['dw_number'] = $serial_number->serial_number;
            $row['branch_number'] = $branch_number++;
            $row['mst_customers_cd'] = $dataSearch['customer_cd'];
            $row['dw_day'] = $dataPayment['dw_day'];
            $row['dw_classification'] = $dataPayment['dw_classification'];
            $row['total_dw_amount'] = $value['total_dw_amount'] + $value['fee'] + $value['discount'];
            $row['actual_dw'] = $value['total_dw_amount'];
            $row['fee'] = $value['fee'];
            $row['discount'] = $value['discount'];
            $row['invoice_number'] = $value['invoice_number'];
            $row['note'] = $dataPayment['note'];
            $row['created_at'] = $currentTime;
            $row['add_mst_staff_id'] = Auth::user()->id;
            $row['modified_at'] = $currentTime;
            $row['upd_mst_staff_id'] = Auth::user()->id;
            array_push($listPayment,$row);
        }
        TPaymentHistories::query()->insert( $listPayment );
        return response()->json([
            'success'=>true,
            'message'=> ''
        ]);
    }
}