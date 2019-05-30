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
    use ListTrait;
    public $table = "t_billing_history_headers";
    public $allNullAble = false;
    public $beforeItem = null;
    public $add_log = false;

    public $ruleValid = [
        'customer_cd' => 'required'
    ];

    public $labels = [
        'customer_cd' => '得意先'
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
        $validator = Validator::make( $fieldSearch, $this->ruleValid ,['required' => Lang::get('messages.MSG10029')] ,$this->labels );
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
                "classTH" => "wd-60",
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

}