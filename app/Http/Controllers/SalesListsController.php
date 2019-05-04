<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use App\Models\MSaleses;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
class SalesListsController extends Controller
{
    use ListTrait;
    public $table = "t_saleses";
    public $ruleValid = [
        'from_date'  => 'required',
        'to_date'=>'required'
    ];
    public $messagesCustom =[];
    public $labels=[];
    public $currentData=null;
    public function __construct(){
        parent::__construct();

    }
    protected function getPaging(){
        return config('params.page_size_sale_lists');
    }
    protected function search($data){
        $currentDate = date("Y-m-d",time());
        $where = array(
            'mst_business_office_id' => $data['fieldSearch']['mst_business_office_id'],
            'from_date' =>date('Y-m-d', strtotime($data['fieldSearch']['from_date'])),
            'to_date'=>date('Y-m-d', strtotime($data['fieldSearch']['to_date'])),
            'invoicing_flag'=>$data['fieldSearch']['invoicing_flag'],
            'mst_customers_cd'=>$data['fieldSearch']['mst_customers_cd'],
        );
        $this->query->select(
                DB::raw("DATE_FORMAT(t_saleses.daily_report_date, '%Y/%m/%d') as daily_report_date"),
                't_saleses.mst_customers_cd',
                't_saleses.document_no',
                'mst_customers.customer_nm_formal as customer_nm',
                't_saleses.departure_point_name',
                't_saleses.landing_name',
                DB::raw('format(t_saleses.total_fee, "#,##0") as total_fee'),
                DB::raw('format(t_saleses.consumption_tax, "#,##0") as consumption_tax'),
                DB::raw('format(t_saleses.tax_included_amount, "#,##0") as tax_included_amount'),
                DB::raw("DATE_FORMAT(t_billing_history_headers.publication_date, '%Y/%m/%d') as publication_date")
            );
        $this->query->leftJoin('mst_customers', function ($join) {
                $join->on('mst_customers.mst_customers_cd', '=', 't_saleses.mst_customers_cd');
        });
        $this->query->leftJoin('t_billing_history_header_details', function ($join) {
                $join->on('t_saleses.document_no', '=','t_billing_history_header_details.document_no' );
        });
        $this->query->leftJoin('t_billing_history_headers', function ($join) {
                $join->on('t_billing_history_headers.invoice_number', '=','t_billing_history_header_details.invoice_number' );
        });
        if ($where['mst_business_office_id'] != '') {
                $this->query->where('t_saleses.mst_business_office_id', '=',  $where['mst_business_office_id']);
        }
        if ($where['from_date'] != '' && $where['to_date'] != '' ) {
            $this->query->where('t_saleses.daily_report_date', '>=',$where['from_date'])
                        ->where('t_saleses.daily_report_date','<=',$where['to_date']);
        }
        if ($where['invoicing_flag'] != '' ) {
            $this->query->where('t_saleses.invoicing_flag', '=',  $where['invoicing_flag']);
        }
        if ($where['mst_customers_cd'] != '' ) {
            $this->query->where('t_saleses.mst_customers_cd', '=',  $where['mst_customers_cd']);
        }
        $this->query->where('t_saleses.deleted_at',null);

        $this->query->orderBy('t_saleses.document_no','asc')
                ->orderBy('t_saleses.daily_report_date','asc');
        $this->currentData=$this->query->get();

    }

    public function index(Request $request){
        $fieldShowTable = [
            'daily_report_date' => [
                "classTH" => "wd-100",
                "classTD" => "text-center",
            ],
            'mst_customers_cd' => [
                "classTH" => "wd-100",
                "classTD" => "text-center",
            ],
            'customer_nm' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
            ],
            'departure_point_name' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
            ],
            'landing_name' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
            ],
            'total_fee' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
            ],
            'consumption_tax' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
            ],
            'tax_included_amount' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
            ],
            'publication_date' => [
                "classTH" => "wd-120",
                "classTD" => "text-center",
            ],

        ];
        $mBussinessOffice = new MBusinessOffices();
        $businessOffices = $mBussinessOffice->getAllData();
        return view('sales_lists.index',[
            'fieldShowTable'=>$fieldShowTable,
            'businessOffices'=> $businessOffices,
           ]);
    }
    public function exportCSV()
    {
       $data=$this->currentData->toArray();
       dd($data);
        return Response()->json(array('success'=>true,"data"=>$data));
    }


}
