<?php

namespace App\Http\Controllers;

use App\Models\MBusinessOffices;
use App\Models\MNumberings;
use App\Models\MSupplier;
use App\Models\TPaymentHistoryHeaderDetails;
use App\Models\TPaymentHistoryHeaders;
use Illuminate\Http\Request;
use App\Http\Controllers\TraitRepositories\ListTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\TPurchases;

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
//            't_purchases.daily_report_date',
            'mbo.business_office_nm',
            't_purchases.mst_suppliers_cd',
            'ms.supplier_nm_formal'
        );
        $this->query->selectRaw('IF(SUM(t_purchases.total_fee) IS NULL,0,SUM(t_purchases.total_fee)) AS billing_amount');
        $this->query->selectRaw('CASE
           WHEN ms.consumption_tax_calc_unit_id = 1 THEN
           IF(SUM( t_purchases.consumption_tax ) IS NULL,0,SUM( t_purchases.consumption_tax ))
               ELSE
							 CASE WHEN ms.rounding_method_id = 1 THEN
								FLOOR(
                   SUM( IF(t_purchases.tax_classification_flg = 1,
                           IF(t_purchases.total_fee IS NULL,0,t_purchases.total_fee)*IF((select rate from consumption_taxs where start_date <= t_purchases.daily_report_date and t_purchases.daily_report_date<=end_date limit 1) IS NULL,0,(select rate from consumption_taxs where start_date <= t_purchases.daily_report_date and t_purchases.daily_report_date<=end_date limit 1)) ,0)
)) WHEN ms.rounding_method_id = 2 THEN
					 ROUND(
                   SUM( IF(t_purchases.tax_classification_flg = 1,
                           IF(t_purchases.total_fee IS NULL,0,t_purchases.total_fee)*IF((select rate from consumption_taxs where start_date <= t_purchases.daily_report_date and t_purchases.daily_report_date<=end_date limit 1) IS NULL,0,(select rate from consumption_taxs where start_date <= t_purchases.daily_report_date and t_purchases.daily_report_date<=end_date limit 1))  ,0)
													 ))
							ELSE
							CEIL(
                   SUM( IF(t_purchases.tax_classification_flg = 1,
                           IF(t_purchases.total_fee IS NULL,0,t_purchases.total_fee)*IF((select rate from consumption_taxs where start_date <= t_purchases.daily_report_date and t_purchases.daily_report_date<=end_date limit 1) IS NULL,0,(select rate from consumption_taxs where start_date <= t_purchases.daily_report_date and t_purchases.daily_report_date<=end_date limit 1))  ,0)
													 ))
					 END
       END AS consumption_tax
        ');
        //
        $this->query->join('mst_business_offices as mbo', function ($join) {
            $join->on('mbo.id', '=', 't_purchases.mst_business_office_id')
            ->whereNull('mbo.deleted_at');
        });
        $this->query->join('mst_suppliers as ms', function ($join) {
            $join->on('ms.mst_suppliers_cd', '=', 't_purchases.mst_suppliers_cd')
            ->whereNull('ms.deleted_at');
        });
        if ($where['mst_business_office_id'] != '') {
            $this->query->where('t_purchases.mst_business_office_id', '=', $where['mst_business_office_id']);
        }
        //
        if($where['mst_suppliers_cd'] != ''){
            $this->query->where('t_purchases.mst_suppliers_cd', '=', $where['mst_suppliers_cd']);
        }
        //
        $this->query->where('t_purchases.daily_report_date', '<=', $date);
        $this->query->whereRaw('t_purchases.deleted_at IS NULL');
        $this->query->whereRaw('t_purchases.invoicing_flag = 0');
        //group by
        $this->query->groupBy(
            't_purchases.mst_business_office_id',
//            't_purchases.daily_report_date',
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
            'daily_report_date_formatted' => [
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
        $lstYear = $this->calculateYear();
        $lstMonth = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        return view('payments.index',
            [
                'fieldShowTable' => $fieldShowTable,
                'fieldShowTableDetails'=>$fieldShowTableDetails,
                'businessOffices' => $businessOffices,
                'lstYear' => $lstYear,
                'lstMonth' => $lstMonth,
            ]);
    }
    public function getCurrentYearMonth(){
        $currentYear = (int)date("Y");
        $currentMonth = (int)date("m");;
        if($currentMonth == 1){
            $currentYear--;
        }
        if($currentMonth == 1){
            $currentMonth = 12;
        }
        else{
            $currentMonth--;
        }
        return response()->json([
            'current_year'=> $currentYear,
            'current_month'=> $currentMonth
        ]);
    }
    protected function calculateYear()
    {
        $currentYear = (int)date("Y");
        $lstYear[] = $currentYear - 1;
        $lstYear[] = $currentYear;
        $lstYear[] = $currentYear + 1;
        return $lstYear;
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
        $tPurchases = new TPurchases();
        $lstDetail = $tPurchases->getListBySupplierCdAndBusinessOfficeIdDailyReportDate($input['mst_suppliers_cd'],$input['mst_business_office_id'],$input['daily_report_date']);
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
    public function execution(Request $request){
        $return = ['success'=>true];
        $data = $request->all();
        $daily_report_date = $data['daily_report_date'];
        $data = $data['data'];
        foreach($data as $item){
            $return = $this->createHistory($item,$daily_report_date);
            if($return['success'] == false){
                break;
            }
        }
        return response()->json($return);
    }
    private function createHistory($item,$daily_report_date){
        $currentTime = date("Y-m-d H:i:s",time());
        $tPurchases = new TPurchases();
        $tPaymentHistoryHeaders = new TPaymentHistoryHeaders();
        //$tPaymentHistoryHeaderDetails = new TPaymentHistoryHeaderDetails();
        $mNumberings = new MNumberings();
        DB::beginTransaction();
        try{
            $serial_number = $mNumberings->getSerialNumberByTargetID('3001');
            $tPaymentHistoryHeaders->invoice_number = $serial_number->serial_number;
            $tPaymentHistoryHeaders->mst_suppliers_cd =$item['mst_suppliers_cd'];
            $tPaymentHistoryHeaders->mst_business_office_id =$item['mst_business_office_id'];
            $tPaymentHistoryHeaders->publication_date =date('Y-m-d');
            $tPaymentHistoryHeaders->total_fee =$item['billing_amount'];
            $tPaymentHistoryHeaders->consumption_tax =$item['consumption_tax'];
            $tPaymentHistoryHeaders->tax_included_amount =$item['billing_amount']+$item['consumption_tax'];
            $tPaymentHistoryHeaders->add_mst_staff_id = Auth::user()->id;
            $tPaymentHistoryHeaders->upd_mst_staff_id = Auth::user()->id;
            if($tPaymentHistoryHeaders->save()){
                $historyDetails = $tPurchases->getListBySupplierCdAndBusinessOfficeIdDailyReportDate($item['mst_suppliers_cd'],$item['mst_business_office_id'],$daily_report_date);
                $branch_number = 1;
                foreach($historyDetails as $detail){
                    $arrayInsert = json_decode(json_encode($detail),true);
                    $arrayInsert['invoice_number'] = $tPaymentHistoryHeaders->invoice_number;
                    $arrayInsert['branch_number'] = $branch_number++;
                    $arrayInsert['add_mst_staff_id'] = Auth::user()->id;
                    $arrayInsert['upd_mst_staff_id'] = Auth::user()->id;
                    $arrayInsert['created_at'] = $currentTime;
                    $arrayInsert['modified_at'] = $currentTime;
                    unset($arrayInsert['invoicing_flag']);
                    unset($arrayInsert['id']);
                    unset($arrayInsert['daily_report_date_formatted']);
                    $id = TPaymentHistoryHeaderDetails::query()->insertGetId($arrayInsert);
                }
                //仕入データを支払締処理済みにする
                $tPurchases = new TPurchases();
                $tPurchases->updateInvoicingFlag($item['mst_suppliers_cd'],$item['mst_business_office_id'],$daily_report_date);
            }
            DB::commit();
            return [
                'success'=>true,
            ];
        }catch (\Exception $e) {
            DB::rollback();
            return [
                'success'=>false,
                'message'=> ['execution' => $e->getMessage()]
            ];
        }
    }
}