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
            "mst_customers.customer_nm",
            "t_payment_histories.dw_classification",
            "t_payment_histories.actual_dw",
            "t_payment_histories.fee",
            "t_payment_histories.discount",
            "t_payment_histories.total_dw_amount",
            "t_payment_histories.note"

        );
        $this->query->join('mst_customers', function ($join) {
            $join->on('mst_customers.mst_customers_cd', '=', 't_payment_histories.mst_customers_cd')
                ->whereRaw('mst_customers.deleted_at IS NULL');
        })->leftJoin('t_billing_history_headers as headers',function ($join){
            $join->on('headers.invoice_number', '=', 't_payment_histories.invoice_number')
                ->whereRaw('headers.deleted_at IS NULL');
        });
        if ($where['from_date'] != '' && $where['to_date'] != '' ) {
            $this->query->where('t_payment_histories.dw_day', '>=',$where['from_date'])
                ->where('t_payment_histories.dw_day','<=',$where['to_date']);
        }
        if ($where['mst_customers_cd'] != '' ) {
            $this->query->where('t_payment_histories.mst_customers_cd', '=',  $where['mst_customers_cd']);
        }
        $this->query->where('t_payment_histories.deleted_at',null);
        $this->query->orderBy('t_payment_histories.dw_number','asc');

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
        return view('payment_histories.index',[
            'fieldShowTable'=>$fieldShowTable,
            'fieldShowTableDetails'=>$fieldShowTableDetails,
        ]);
    }



}