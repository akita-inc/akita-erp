<?php

namespace App\Http\Controllers;

use App\Models\MBusinessOffices;
use App\Models\MSupplier;
use Illuminate\Http\Request;
use App\Http\Controllers\TraitRepositories\ListTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{
    use ListTrait;
    public $table = 't_purchases';
    public $ruleValid = [
    ];

    public $labels = [
        "closed_date" => "締め日"
    ];
    public function __construct()
    {
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
        $this->ruleValid['closed_date'] = 'required';

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

    protected function search($data)
    {
        $billing_year = $data['fieldSearch']['billing_year'];
        $billing_month = $data['fieldSearch']['billing_month'];
        $closed_date = $data['fieldSearch']['closed_date'];
        $date = date("Y-m-d",strtotime($billing_year.'/'.$billing_month.'/'.$closed_date));
        $where = array(
            'mst_business_office_id' => $data['fieldSearch']['mst_business_office_id'],
            'mst_suppliers_cd' => $data['fieldSearch']['supplier_cd'],
            'daily_report_date' => $date
        );
        //select
        $this->query->select(
            't_purchases.mst_business_office_id',
            'mbo.business_office_nm',
            't_purchases.mst_suppliers_cd',
            'ms.supplier_nm_formal'
        );
        $this->query->selectRaw('sum(t_purchases.total_fee) as billing_amount');
        $this->query->selectRaw('
        CASE
           WHEN ms.consumption_tax_calc_unit_id = 1 THEN
           SUM( t_purchases.consumption_tax )
               ELSE
							 CASE WHEN ms.rounding_method_id = 1 THEN
								TRUNCATE(
                   SUM( IF(t_purchases.tax_classification_flg = 1,
                           t_purchases.total_fee*(select rate from consumption_taxs where start_date <= t_purchases.daily_report_date and t_purchases.daily_report_date<=end_date limit 1) ,0)
            ),0) WHEN ms.rounding_method_id = 2 THEN
					 ROUND(
                   SUM( IF(t_purchases.tax_classification_flg = 1,
                           t_purchases.total_fee*(select rate from consumption_taxs where start_date <= t_purchases.daily_report_date and t_purchases.daily_report_date<=end_date limit 1) ,0)
													 ))
							ELSE
							TRUNCATE(
                   SUM( IF(t_purchases.tax_classification_flg = 1,
                           t_purchases.total_fee*(select rate from consumption_taxs where start_date <= t_purchases.daily_report_date and t_purchases.daily_report_date<=end_date limit 1) ,0)
													 )+.9,0)
					 END
       END AS consumption_tax
        ');
        //
        $this->query->leftJoin('mst_business_offices as mbo', function ($join) {
            $join->on('mbo.id', '=', 't_purchases.mst_business_office_id');
        });
        $this->query->leftJoin('mst_suppliers as ms', function ($join) {
            $join->on('ms.mst_suppliers_cd', '=', 't_purchases.mst_suppliers_cd');
        });
        if ($where['mst_business_office_id'] != '') {
            $this->query->where('t_purchases.mst_business_office_id', '=', $where['mst_business_office_id']);
            //
            if($where['mst_suppliers_cd'] != ''){
                $this->query->where('t_purchases.mst_suppliers_cd', '=', $where['mst_suppliers_cd']);
            }
        }
        //
        $this->query->where('t_purchases.daily_report_date', '<=', $date);
        $this->query->whereRaw('t_purchases.deleted_at IS NULL');
        $this->query->whereRaw('t_purchases.invoicing_flag = 0');
        //group by
        $this->query->groupBy(
            't_purchases.mst_business_office_id',
            'mbo.business_office_nm',
            't_purchases.mst_suppliers_cd',
            'ms.supplier_nm_formal',
            'ms.consumption_tax_calc_unit_id',
            'ms.rounding_method_id'
            );
        //order by
        if ($data["order"]["col"] != '') {

        } else {
            $this->query->orderby('t_purchases.mst_business_office_id')->orderby('t_purchases.mst_suppliers_cd');
        }
    }

    public function index(Request $request)
    {
        $fieldShowTable =[
            'business_office_nm' => [
                "classTH" => "wd-120",
                "sortBy" => "business_office_nm"
            ],
            'mst_suppliers_cd' => [
                "classTH" => "wd-120",
                "sortBy" => "supplier_cd"
            ],
            'supplier_nm_formal' => [
                "classTH" => "wd-120",
                "sortBy" => "supplier_nm_formal"
            ],
            'billing_amount' => [
                "classTH" => "wd-120",
                "sortBy" => "billing_amount"
            ],
            'consumption_tax' => [
                "classTH" => "wd-120",
                "sortBy" => "consumption_tax"
            ],
            'total_amount' => [
                "classTH" => "wd-120",
                "sortBy" => "total_amount"
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
        $mBusinessOffice = new MBusinessOffices();
        $businessOffices = $mBusinessOffice->getAllData();
        $this->calculateYear($initYear, $lstYear);
        //calculate month
        $initMonth = date('m');
        if ($initMonth == 1) {
            $initMonth = 12;
        } else {
            $initMonth = $initMonth - 1;
        }
        $lstMonth = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        return view('payments.index',
            [
                'fieldShowTable' => $fieldShowTable,
                'fieldShowTableDetails'=>$fieldShowTableDetails,
                'businessOffices' => $businessOffices,
                'initYear' => $initYear,
                'lstYear' => $lstYear,
                'initMonth' => $initMonth,
                'lstMonth' => $lstMonth,
            ]);
    }

    protected function calculateYear(&$initYear, &$lstYear)
    {
        $initYear = $currentYear = (int)date("Y");
        $currentMonth = date('m');
        if ($currentMonth == 1) {
            $initYear = $initYear - 1;
        }
        $lstYear[] = $currentYear - 1;
        $lstYear[] = $currentYear;
        $lstYear[] = $currentYear + 1;
    }
    public function loadListBundleDt(Request $request){
        $input = $request->all();
        $mSupplier = new MSupplier();
        $listBundleDt= $mSupplier->getListBundleDt($input['supplier_cd']);
        return response()->json([
            'success'=>true,
            'info'=> $listBundleDt,
        ]);
    }
    public function getDetailsPayment(Request $request){
        $input = $request->all();
        $lstDetail = DB::table('t_purchases')
            ->select(
                DB::raw("DATE_FORMAT(t_purchases.daily_report_date, '%Y/%m/%d') AS daily_report_date"),
                't_purchases.departure_point_name',
                't_purchases.landing_name',
                't_purchases.total_fee',
                't_purchases.consumption_tax',
                't_purchases.tax_included_amount'
            )->where('mst_suppliers_cd',$input['mst_suppliers_cd'])
            ->where('mst_business_office_id',$input['mst_business_office_id'])
            ->whereNull('deleted_at')->get()
            ;
        return response()->json([
            'success'=>true,
            'info' => $lstDetail
        ]);
    }
    public function getListSuppliers(){
        $mSupplier = new MSupplier();
        $query = $mSupplier->select('mst_suppliers_cd','supplier_nm');
        $data = $query->whereNull('deleted_at')->get();
        return Response()->json(array('success'=>true,'data'=>$data));
    }
}