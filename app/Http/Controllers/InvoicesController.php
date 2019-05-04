<?php

namespace App\Http\Controllers;


use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MCustomers;
use App\Models\MGeneralPurposes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class InvoicesController extends Controller {
    use ListTrait,FormTrait;
    public $table = "t_saleses";
    public $allNullAble = false;
    public $beforeItem = null;
    public $add_log = false;

    public $ruleValid = [
    ];

    public $labels = [
        "closed_date" => "締め日",
        "closed_date_input" => "特例締め日",
    ];

    public function __construct(){
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
        if($fieldSearch['special_closing_date']==1){
            $this->ruleValid['closed_date_input'] = 'required';
        }else{
            $this->ruleValid['closed_date'] = 'required';
        }
        $validator = Validator::make( $fieldSearch, $this->ruleValid ,[] ,$this->labels );
        if ( $validator->fails() ) {
            return response()->json([
                'success'=>FALSE,
                'message'=> $validator->errors()
            ]);
        }else {
            $this->getQuery();
            $this->search($data);
            $items = $this->query->get();
            $response = [
                'success'=>true,
                'data' => $items,
                'fieldSearch' => $data['fieldSearch'],
            ];
            return response()->json($response);
        }
    }

    protected function search($data){
        $dataSearch=$data['fieldSearch'];
        $this->query->select(
            't_saleses.mst_business_office_id',
            't_saleses.mst_customers_cd as customer_cd',
            'mst_business_offices.business_office_nm as regist_office',
            'mst_customers.customer_nm_formal as customer_nm',
            DB::raw("sum(t_saleses.total_fee) as total_fee")

        );
        $this->query->leftJoin('mst_business_offices', function ($join) {
            $join->on('mst_business_offices.id', '=', 't_saleses.mst_business_office_id');
        })->leftjoin('mst_customers', function ($join) {
            $join->on('mst_customers.mst_customers_cd', '=', 't_saleses.mst_customers_cd')
                ->whereNull('mst_customers.deleted_at');
        });
        if ($dataSearch['mst_business_office_id'] != '') {
            $this->query->where('t_saleses.mst_business_office_id', '=', $dataSearch['mst_business_office_id'] );
        };
        if ($dataSearch['customer_cd'] != '') {
            $this->query->where('t_saleses.mst_customers_cd', '=',  $dataSearch['customer_cd']);
        }
        if ($dataSearch['billing_year'] != '' && $dataSearch['billing_month'] != '' && ($dataSearch['closed_date_input'] !='' || $dataSearch['closed_date'])) {
            $date = date("Y-m-d",strtotime($dataSearch['billing_year'].'/'.$dataSearch['billing_month'].'/'.($dataSearch['closed_date'] ? $dataSearch['closed_date'] : $dataSearch['closed_date_input'])));
            $this->query->where('t_saleses.daily_report_date', '<=', $date);
        }
        $this->query->where('t_saleses.invoicing_flag',0);
        $this->query->where('t_saleses.deleted_at',null);

        $this->query->orderBy('t_saleses.mst_business_office_id','asc')
                ->orderBy('t_saleses.mst_customers_cd','asc');

        $this->query->groupBy('t_saleses.mst_customers_cd','t_saleses.mst_business_office_id','mst_business_offices.business_office_nm','mst_customers.customer_nm_formal');
    }

    public function index(Request $request){
        $fieldShowTable = [
            'regist_office' => [
                "classTH" => "wd-60",
            ],
            'customer_cd'=> [
                "classTH" => "wd-60",
            ],
            'customer_nm'=> [
                "classTH" => "wd-120",
            ],
            'total_fee'=> [
                "classTH" => "wd-100",
            ],
            'sale_tax'=> [
                "classTH" => "wd-100",
            ],
            'total'=> [
                "classTH" => "wd-120",
            ],

        ];
        $fieldShowTableDetails = [
            'daily_report_date' => [
                "classTH" => "wd-60",
            ],
            'departure_point_name'=> [
                "classTH" => "wd-60",
            ],
            'landing_name'=> [
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

        ];
        $mBussinessOffice = new MBusinessOffices();
        $mCustomer = new MCustomers();
        $listBundleDt= $mCustomer->getListBundleDt();
        $businessOffices = $mBussinessOffice->getAllData();
        $listMonth = [1,2,3,4,5,6,7,8,9,10,11,12];
        $currentMonth = date('m');
        $currentYear = (int)date('Y');
        if($currentMonth=='12'){
            $currentYear--;
        }
        $listYear = [$currentYear-1, $currentYear, $currentYear+1];
        return view('invoices.index',[
            'fieldShowTable'=>$fieldShowTable,
            'fieldShowTableDetails'=>$fieldShowTableDetails,
            'businessOffices'=> $businessOffices,
            'listMonth'=>$listMonth,
            'listYear'=>$listYear,
            'listBundleDt' => $listBundleDt,
        ]);
    }

    public function getListCustomers(){
        $mCustomer = new MCustomers();
        $listBillCustomersCd = $mCustomer->select('bill_mst_customers_cd')->distinct()->whereNull('deleted_at')->get();

        $query = $mCustomer->select('mst_customers_cd','customer_nm');
        if($listBillCustomersCd){
            $listBillCustomersCd = $listBillCustomersCd->toArray();
            $query = $query->whereIn('mst_customers_cd',$listBillCustomersCd);

        }
        $data = $query->whereNull('deleted_at')->get();
        return Response()->json(array('success'=>true,'data'=>$data));
    }

    public function loadListBundleDt(Request $request){
        $input = $request->all();
        $mCustomer = new MCustomers();
        $listBundleDt= $mCustomer->getListBundleDt($input['customer_cd']);
        return response()->json([
            'success'=>true,
            'info'=> $listBundleDt,
        ]);
    }

    public function getDetailsInvoice(Request $request){
        $input = $request->all();
        $listDetail =  DB::table('t_saleses')->select(
            DB::raw("DATE_FORMAT(t_saleses.daily_report_date, '%Y/%m/%d') as daily_report_date"),
            't_saleses.departure_point_name',
            't_saleses.landing_name',
            't_saleses.total_fee',
            't_saleses.consumption_tax',
            't_saleses.tax_included_amount'
        )->where('mst_customers_cd',$input['mst_customers_cd'])
        ->whereNull('deleted_at')->get();
        return response()->json([
            'success'=>true,
            'info'=> $listDetail,
        ]);
    }
}