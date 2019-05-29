<?php

namespace App\Http\Controllers;


use App\Helpers\InvoicePDF;
use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBillingHistoryHeaderDetails;
use App\Models\MBillingHistoryHeaders;
use App\Models\MBusinessOffices;
use App\Models\MCustomers;
use App\Models\MGeneralPurposes;
use App\Models\MNumberings;
use App\Models\MSaleses;
use App\Models\TPaymentHistories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class PaymentHistoriesController extends Controller {
    use ListTrait,FormTrait;
    public $table = "t_payment_histories";
    public $allNullAble = false;
    public $beforeItem = null;
    public $add_log = false;

    public $ruleValid = [
    ];

    public $labels = [

    ];
    public function __construct(){
        date_default_timezone_set("Asia/Tokyo");
        parent::__construct();
    }
    protected function getPaging(){
        return config('params.page_size_sale_lists');
    }
    protected function search($data){
        $where = array(
            'from_date' =>date('Y-m-d', strtotime($data['fieldSearch']['from_date'])),
            'to_date'=>date('Y-m-d', strtotime($data['fieldSearch']['to_date'])),
            'mst_customers_cd'=>$data['fieldSearch']['mst_customers_cd']
        );
        $this->query->select(
            DB::raw("DATE_FORMAT(t_payment_histories.dw_day, '%Y/%m/%d') as dw_day"),
            "t_payment_histories.mst_customers_cd",
            DB::raw("mst_customers.customer_nm_formal as customer_nm"),
            "t_payment_histories.dw_classification",
            DB::raw('SUM(t_payment_histories.actual_dw) as actual_dw'),
            DB::raw('SUM(t_payment_histories.fee) as fee'),
            DB::raw('SUM(t_payment_histories.discount) as discount'),
            DB::raw('SUM(t_payment_histories.total_dw_amount) as total_dw_amount'),
            't_payment_histories.dw_number',
            't_payment_histories.note');
        $this->query->join('mst_customers', function ($join) {
            $join->on('mst_customers.mst_customers_cd', '=', 't_payment_histories.mst_customers_cd')
                ->whereRaw('mst_customers.deleted_at IS NULL');
        });
        if ($where['from_date'] != '' && $where['to_date'] != '' ) {
            $this->query->where('t_payment_histories.dw_day', '>=',$where['from_date'])
                ->where('t_payment_histories.dw_day','<=',$where['to_date']);
        }
        if ($where['mst_customers_cd'] != '' ) {
            $this->query->where('t_payment_histories.mst_customers_cd', '=',  $where['mst_customers_cd']);
        }
        $this->query->where('t_payment_histories.deleted_at',null);
        $this->query->groupBy(
                            't_payment_histories.dw_number',
                            't_payment_histories.dw_day',
                            't_payment_histories.mst_customers_cd',
                            'mst_customers.customer_nm_formal',
                            't_payment_histories.dw_classification',
                            't_payment_histories.note'
        );
        $this->query->orderBy('t_payment_histories.dw_number','desc');

    }

    public function index(Request $request){
        $fieldShowTable = [
            'dw_day' => [
                "classTH" => "wd-100",
            ],
            'mst_customers_cd'=> [
                "classTH" => "wd-100",
            ],
            'customer_nm'=> [
                "classTH" => "wd-120",
            ],
            'dw_classification'=> [
                "classTH" => "wd-100",
            ],
            'actual_dw'=> [
                "classTH" => "wd-60",
            ],
            'fee'=> [
                "classTH" => "wd-60",
            ],
            'discount'=> [
                "classTH" => "wd-100",
            ],
            'total_dw_amount'=> [
                "classTH" => "wd-100",
            ],
            'note'=> [
                "classTH" => "wd-120",
            ],

        ];
        $fieldShowTableDetails = [
            'invoice_number' => [
                "classTH" => "wd-60",
            ],
            'publication_date'=> [
                "classTH" => "wd-60",
            ],
            'total_fee'=> [
                "classTH" => "wd-120",
            ],
            'consumption_tax'=> [
                "classTH" => "wd-100",
            ],
            'tax_included_amount'=> [
                "classTH" => "wd-100",
            ],
            'last_payment_amount'=> [
                "classTH" => "wd-120",
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
            'deposit_balance'=> [
                "classTH" => "wd-60",
            ],

        ];
        return view('payment_histories.index',[
            'fieldShowTable'=>$fieldShowTable,
            'fieldShowTableDetails'=>$fieldShowTableDetails,
        ]);
    }
    public function getDetailsPaymentHistories(Request $request)
    {
        $input = $request->all();
        $fieldSearch = $input['fieldSearch'];
        $mPaymentHistories = new TPaymentHistories();
        $listDetail =  $mPaymentHistories->getListByCustomerCd($input['dw_number'], $fieldSearch);
        return response()->json([
            'success'=>true,
            'info'=> $listDetail,
        ]);
    }



}