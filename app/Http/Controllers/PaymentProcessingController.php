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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PaymentProcessingController extends Controller{
    use ListTrait;
    public $table = "t_billing_history_headers";
    public $allNullAble = false;
    public $beforeItem = null;
    public $add_log = false;

    public $ruleValid = [
    ];

    public $labels = [];

    public $csvColumn = [
        'mst_suppliers_cd' => '仕入先コード',
        'supplier_nm' => '仕入先名',
        'purchases_tax_included_amount' => '請求金額',
        'saleses_tax_included_amount' => '売上金額',
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
        $items = $this->search($data);
        $response = [
            'success'=>true,
            'data' => $items,
            'fieldSearch' => $data['fieldSearch'],
        ];
        return response()->json($response);

    }

    protected function search($data){
        $dataSearch=$data['fieldSearch'];
        $querySearch = "\n";
        $paramsSearch = [];
        if ($dataSearch['mst_business_office_id'] != '') {
            $querySearch .= "AND ts.mst_business_office_id = :mst_business_office_id "."\n";
            $paramsSearch['mst_business_office_id'] = $dataSearch['mst_business_office_id'];
        };
        if ($dataSearch['customer_cd'] != '') {
            $querySearch .= "AND c.bill_cus_cd = :customer_cd "."\n";
            $paramsSearch['customer_cd'] = $dataSearch['customer_cd'];
        }
        if ($dataSearch['billing_year'] != '' && $dataSearch['billing_month'] != '' && ($dataSearch['closed_date_input'] !='' || $dataSearch['closed_date'])) {
            $date = date("Y-m-d",strtotime($dataSearch['billing_year'].'/'.$dataSearch['billing_month'].'/'.($dataSearch['special_closing_date'] ? $dataSearch['closed_date_input'] : $dataSearch['closed_date'])));
            $querySearch .= "AND ts.daily_report_date <= :date "."\n";
            $paramsSearch['date'] = $date;
        }
//        $this->query->select()->whereNull('deleted_at');

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
        $listBillCustomersCd = $mCustomer->select(DB::raw('IFNULL(bill_mst_customers_cd,mst_customers_cd) as bill_mst_customers_cd'))->distinct()->whereNull('deleted_at')->get();

        $query = $mCustomer->select('mst_customers_cd','customer_nm');
        if($listBillCustomersCd){
            $listBillCustomersCd = $listBillCustomersCd->toArray();
            $query = $query->whereIn('mst_customers_cd',$listBillCustomersCd);

        }
        $data = $query->whereNull('deleted_at')->get();
        return Response()->json(array('success'=>true,'data'=>$data));
    }

}