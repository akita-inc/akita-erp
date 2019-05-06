<?php

namespace App\Http\Controllers;


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

    public $csvColumn = [
            'daily_report_date' => '日報日付',
            'branch_office_cd' => '支店CD',
            'document_no' => '伝票NO',
            'registration_numbers' => '登録番号',
            'staff_cd' => '社員CD',
            'staff_nm' => '社員名',
            'mst_customers_cd' => '得意先CD',
            'customer_nm' => '得意先名',
            'goods' => '品物',
            'departure_point_name' => '発地名',
            'landing_name' => '着地名',
            'delivery_destination' => '納入先',
            'quantity' => '数量',
            'unit_price' => '単価',
            'total_fee' => '便請求金額',
            'insurance_fee' => '保険料',
            'billing_fast_charge' => '請求高速料',
            'discount_amount' => '値引金額',
            'tax_included_amount' => '請求金額',
            'loading_fee' => '積込料',
            'wholesale_fee' => '取卸料',
            'waiting_fee' => '待機料',
            'incidental_fee' => '附帯料',
            'surcharge_fee' => 'サーチャージ料',
        ];

    public $csvContent = [];

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
//            $this->getQuery();
//            $this->search($data);
//            $items = $this->query->get();

            $items = $this->search($data);
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
//        $this->query->select(
//            't_saleses.mst_business_office_id',
//            't_saleses.mst_customers_cd as customer_cd',
//            'mst_business_offices.business_office_nm as regist_office',
//            'mst_customers.customer_nm_formal as customer_nm',
//            DB::raw("sum(t_saleses.total_fee) as total_fee"),
//            DB::raw("sum(t_saleses.total_fee) as consumption_tax"),
//            DB::raw("sum(t_saleses.total_fee) as tax_included_amount")
//
//        );
//        $this->query->leftJoin('mst_business_offices', function ($join) {
//            $join->on('mst_business_offices.id', '=', 't_saleses.mst_business_office_id');
//        })->leftjoin('mst_customers', function ($join) {
//            $join->on('mst_customers.mst_customers_cd', '=', 't_saleses.mst_customers_cd')
//                ->whereNull('mst_customers.deleted_at');
//        });
//        if ($dataSearch['mst_business_office_id'] != '') {
//            $this->query->where('t_saleses.mst_business_office_id', '=', $dataSearch['mst_business_office_id'] );
//        };
//        if ($dataSearch['customer_cd'] != '') {
//            $this->query->where('t_saleses.mst_customers_cd', '=',  $dataSearch['customer_cd']);
//        }
//        if ($dataSearch['billing_year'] != '' && $dataSearch['billing_month'] != '' && ($dataSearch['closed_date_input'] !='' || $dataSearch['closed_date'])) {
//            $date = date("Y-m-d",strtotime($dataSearch['billing_year'].'/'.$dataSearch['billing_month'].'/'.($dataSearch['closed_date'] ? $dataSearch['closed_date'] : $dataSearch['closed_date_input'])));
//            $this->query->where('t_saleses.daily_report_date', '<=', $date);
//        }
//        $this->query->where('t_saleses.invoicing_flag',0);
//        $this->query->where('t_saleses.deleted_at',null);
//
//        $this->query->orderBy('t_saleses.mst_business_office_id','asc')
//                ->orderBy('t_saleses.mst_customers_cd','asc');
//
//        $this->query->groupBy('t_saleses.mst_customers_cd','t_saleses.mst_business_office_id','mst_business_offices.business_office_nm','mst_customers.customer_nm_formal');


        $querySearch = "\n";
        $paramsSearch = [];
        if ($dataSearch['mst_business_office_id'] != '') {
            $querySearch .= "AND ts.mst_business_office_id = mst_business_office_id "."\n";
            $paramsSearch['mst_business_office_id'] = $dataSearch['mst_business_office_id'];
        };
        if ($dataSearch['customer_cd'] != '') {
            $querySearch .= "AND c.bill_cus_cd = customer_cd "."\n";
            $paramsSearch['customer_cd'] = $dataSearch['customer_cd'];
        }
        if ($dataSearch['billing_year'] != '' && $dataSearch['billing_month'] != '' && ($dataSearch['closed_date_input'] !='' || $dataSearch['closed_date'])) {
            $date = date("Y-m-d",strtotime($dataSearch['billing_year'].'/'.$dataSearch['billing_month'].'/'.($dataSearch['closed_date'] ? $dataSearch['closed_date'] : $dataSearch['closed_date_input'])));
            $querySearch .= "AND ts.daily_report_date <= date "."\n";
            $paramsSearch['date'] = "'".$date."'";
        }
        $this->query = "
                    SELECT
                ts.mst_business_office_id,
                office.business_office_nm,
                -- ts.mst_customers_cd \"sales_cus_cd売上.得意先CD\",
                c.`bill_cus_cd`AS `customer_cd`,
                -- c.sales_cus_nm, -- <== trong modal
                c.bill_cus_nm AS `customer_nm`,
                SUM(ts.total_fee)  AS billing_amount,
                CASE
            WHEN c.consumption_tax_calc_unit_id = 1 THEN
                SUM(ts.consumption_tax)
            ELSE
                SUM(
            
                    IF (
                        ts.tax_classification_flg = 1,
                        (
                            IFNULL(ts.unit_price,0) * IFNULL(ts.quantity,0) +IFNULL(ts.insurance_fee,0) + IFNULL(ts.loading_fee,0) + IFNULL(ts.wholesale_fee,0) + IFNULL(ts.waiting_fee,0) + IFNULL(ts.incidental_fee,0) + IFNULL(ts.surcharge_fee,0)
                        ) * (
                            SELECT
                                rate
                            FROM
                                consumption_taxs
                            WHERE
                                start_date <= ts.daily_report_date
                            AND ts.daily_report_date <= end_date
                            LIMIT 1
                        ),
                        0
                    )
                )
            END AS consumption_tax_cal,
            c.consumption_tax_calc_unit_id,
            c.rounding_method_id
            FROM
                t_saleses ts
            JOIN (
                SELECT
                    connect_sales.id,
                    connect_sales.mst_customers_cd sales_cus_cd,
                    connect_sales.customer_nm_formal sales_cus_nm,
                    bill_info.mst_customers_cd bill_cus_cd,
                    bill_info.customer_nm_formal bill_cus_nm, -- ↓
                    bill_info.consumption_tax_calc_unit_id,
                    bill_info.rounding_method_id
                FROM
                    mst_customers connect_sales
                JOIN mst_customers bill_info ON IFNULL(
                    connect_sales.bill_mst_customers_cd,
                    connect_sales.mst_customers_cd
                ) = bill_info.mst_customers_cd
            WHERE
                connect_sales.deleted_at IS NULL
                AND bill_info.deleted_at IS NULL
            ) c ON ts.mst_customers_cd = c.sales_cus_cd
            LEFT JOIN mst_business_offices office ON ts.mst_business_office_id = office.id
            AND office.deleted_at IS NULL
            WHERE
                ts.deleted_at IS NULL
            -- AND ts.mst_business_office_id = \"1\"
            --AND ts.daily_report_date <= \"2019-05-04\"
            -- AND c.bill_cus_cd = \"10119\"
            $querySearch
            GROUP BY
                ts.mst_business_office_id,
                office.business_office_nm,
                c.bill_cus_cd,
                c.bill_cus_nm,
                c.consumption_tax_calc_unit_id,
                c.rounding_method_id 
            ORDER BY
                ts.mst_business_office_id ASC,
                 c.bill_cus_cd ASC
        ";
        return DB::select($this->query,$paramsSearch);

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
            'consumption_tax'=> [
                "classTH" => "wd-100",
            ],
            'tax_included_amount'=> [
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
        $mSaleses = new MSaleses();
        $listDetail =  $mSaleses->getListByCustomerCd($input['mst_customers_cd'],$input['mst_business_office_id']);
        return response()->json([
            'success'=>true,
            'info'=> $listDetail,
        ]);
    }

    public function createPDF(){
        $file = storage_path() . "/pdf_template/請求書無地P1.pdf";

        $headers = [
            'Content-Type' => 'application/pdf',
        ];
        return response()->download($file, '請求書無地P1.pdf', $headers);
    }

    public function createCSV(Request $request){
        $zip_name = 'csv.zip';
        $zip = new \ZipArchive();
        $zip->open($zip_name, \ZipArchive::CREATE);
        $headers = array(
            "Content-type" => "application/zip",
            "Content-Disposition" => "attachment; filename=$zip_name",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $data = $request->all();
        $data = $data['data'];
        $keys = array_keys($this->csvColumn);
        $temp_directory = public_path();
        foreach ($data as $item){
            $this->createHistory($item);
            $fileName = 'seikyu_'.$this->csvContent[$item['customer_cd']][0]['branch_office_cd'].'_'.date('YmdHis', time()).'.csv';
            echo date('YmdHis', time());
            $file = fopen($temp_directory.'/'.$fileName, 'w');
            fputcsv($file, array_values($this->csvColumn));
            foreach($this->csvContent[$item['customer_cd']] as $content) {
                $row = [];
                foreach ($keys as $key){
                    $row[$key] = $content[$key];
                }
                fputcsv($file, $row,config('params.csv.delimiter'), config('params.csv.enclosure'));
            }
            fclose($file);
            $contentCSV = file_get_contents($temp_directory.'/'.$fileName);
            $zip->addFromString($fileName, mb_convert_encoding($contentCSV, "SJIS", "UTF-8"));
            if(is_file($temp_directory.'/'.$fileName)) {
                unlink($temp_directory.'/'.$fileName);
            }
            sleep(1);
        }
        $zip->close();
        readfile($zip_name);
        unlink($zip_name);
        return response()->download($zip_name, $zip_name,$headers);
    }

    public function handleCsv(){

    }

    public function createHistory($item){
        $currentTime = date("Y-m-d H:i:s",time());
        $mSaleses = new MSaleses();
        $mBillingHistoryHeaders =  new MBillingHistoryHeaders();
        $mBillingHistoryHeaderDetails =  new MBillingHistoryHeaderDetails();
        $mNumberings =  new MNumberings();
        DB::beginTransaction();
        try
        {
            $this->csvContent[$item['customer_cd']] = [];
            $serial_number = $mNumberings->getSerialNumberByTargetID('2001');
            $mBillingHistoryHeaders->invoice_number = $serial_number->serial_number;
            $mBillingHistoryHeaders->mst_customers_cd = $item['customer_cd'];
            $mBillingHistoryHeaders->mst_business_office_id = $item['mst_business_office_id'];
            $mBillingHistoryHeaders->publication_date = date('Y-m-d');
            $mBillingHistoryHeaders->total_fee = $item['total_fee'];
            $mBillingHistoryHeaders->consumption_tax = $item['consumption_tax'];
            $mBillingHistoryHeaders->tax_included_amount = $item['tax_included_amount'];
            $mBillingHistoryHeaders->add_mst_staff_id =  Auth::user()->id;
            $mBillingHistoryHeaders->upd_mst_staff_id = Auth::user()->id;
            if($mBillingHistoryHeaders->save()){
                $history_details =  $mSaleses->getListByCustomerCd($item['customer_cd'],$item['mst_business_office_id']);
                $branch_number = 0;
                foreach ($history_details as $detail){
                    $arrayInsert = json_decode(json_encode($detail),true);
                    array_push( $this->csvContent[$item['customer_cd']], $arrayInsert);
                    $arrayInsert['invoice_number'] = $mBillingHistoryHeaders->invoice_number;
                    $arrayInsert['branch_number'] = $branch_number++;
                    $arrayInsert['add_mst_staff_id'] = Auth::user()->id;
                    $arrayInsert['upd_mst_staff_id'] = Auth::user()->id;
                    $arrayInsert['created_at'] = $currentTime;
                    $arrayInsert['modified_at'] = $currentTime;
                    unset($arrayInsert['invoicing_flag']);
                    unset($arrayInsert['customer_nm']);
                    unset($arrayInsert['registration_numbers']);
                    unset($arrayInsert['staff_nm']);
                    unset($arrayInsert['id']);
                    $id =  MBillingHistoryHeaderDetails::query()->insertGetId( $arrayInsert );
                }

            }
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }

    }
}