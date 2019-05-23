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
    public $amazonCsvColumn = [
            'daily_report_date' => '月日',
            'goods' => '品名',
            'size' => '屯',
            'quantity' => '数量',
            'unit_price' => '単価',
            'total_fee' => '金額',
            'departure_point_name' => '発地',
            'landing_name' => '着地',
            'loading_fee' => '積込料',
            'wholesale_fee' => '取卸料',
            'incidental_fee' => '付帯業務料',
            'waiting_fee' => '待機料',
            'surcharge_fee' => 'サーチャージ料',
            'billing_fast_charge' => '通行料',
            'delivery_destination' => '備考',
        ];

    public $csvContent = [];

    public $billingHistoryHeaderID ="";
    public $listBillingHistoryDetailID = [];

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
        $this->query = "
            SELECT
                invoices.mst_business_office_id,
                invoices.regist_office,
                invoices.office_cd,
                invoices.customer_cd,
                invoices.customer_nm,
                CAST(invoices.total_fee AS DECIMAL(10,2)) as total_fee,
            IF
                (
                    invoices.consumption_tax_calc_unit_id = 0,
                CASE
                        
                        WHEN invoices.rounding_method_id = 1 THEN
                        FLOOR( invoices.consumption_tax_cal ) 
                        WHEN invoices.rounding_method_id = 2 || invoices.rounding_method_id IS NULL THEN
                        ROUND( invoices.consumption_tax_cal ) ELSE CEIL( invoices.consumption_tax_cal ) 
                    END,
                    invoices.consumption_tax_cal 
                ) AS consumption_tax
            FROM
                (
                    SELECT
                    ts.mst_business_office_id,
                    office.mst_business_office_cd as office_cd,
                    office.business_office_nm as regist_office,
                    -- ts.mst_customers_cd \"sales_cus_cd売上.得意先CD\",
                    c.`bill_cus_cd`AS `customer_cd`,
                    -- c.sales_cus_nm, -- <== trong modal
                    c.bill_cus_nm AS `customer_nm`,
                    SUM(ts.total_fee)  AS total_fee,
                    CASE
                WHEN c.consumption_tax_calc_unit_id = 1 THEN
                    SUM(ts.consumption_tax)
                ELSE
                    SUM(
                
                        IF (
                            ts.tax_classification_flg = 1,
                            (
                                IFNULL(ts.unit_price,0) * IFNULL(ts.quantity,0) +IFNULL(ts.insurance_fee,0) + IFNULL(ts.loading_fee,0) + IFNULL(ts.wholesale_fee,0) + IFNULL(ts.waiting_fee,0) + IFNULL(ts.incidental_fee,0) + IFNULL(ts.surcharge_fee,0)- IFNULL(ts.discount_amount,0) 
                            ) * IFNULL((
                                SELECT
                                    rate
                                FROM
                                    consumption_taxs
                                WHERE
                                    start_date <= ts.daily_report_date
                                AND ts.daily_report_date <= end_date
                                LIMIT 1
                            ),0),
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
                        bill_info.customer_nm_formal bill_cus_nm,
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
                JOIN mst_business_offices office ON ts.mst_business_office_id = office.id
                AND office.deleted_at IS NULL
                WHERE
                    ts.deleted_at IS NULL
                    AND ts.invoicing_flag = 0
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
             ) invoices
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
        $fieldSearch = $input['fieldSearch'];
        $mSaleses = new MSaleses();
        $listDetail =  $mSaleses->getListByCustomerCd($input['mst_customers_cd'],$input['mst_business_office_id'], $fieldSearch);
        return response()->json([
            'success'=>true,
            'info'=> $listDetail,
        ]);
    }

    public function createPDF(Request $request){
        $mBillingHistoryHeaders =  new MBillingHistoryHeaders();
        $mBillingHistoryHeaderDetails =  new MBillingHistoryHeaderDetails();
        $data = $request->all();
        $fieldSearch = $data['fieldSearch'];
        $item = $data['data'];
        $type= $data['type'];
        $publication_date = $data['date_of_issue'];
        if($type==1){
            $listBillingHistoryHeaderID = $data['listBillingHistoryHeaderID'];
            $listBillingHistoryDetailID = $data['listBillingHistoryDetailID'];
            if(!empty($listBillingHistoryHeaderID) && !empty($listBillingHistoryDetailID)){
                $this->billingHistoryHeaderID = $listBillingHistoryHeaderID;
                $this->listBillingHistoryDetailID = explode(',',$listBillingHistoryDetailID);
            }else{
                $this->createHistory($item,$fieldSearch,$publication_date);
            }
            $fileName = 'seikyu_'.$item['office_cd'].'_'.$item['customer_cd'].'_'.date('Ymd', time()).'.pdf';
            if(!empty($this->billingHistoryHeaderID)){
                $contentHeader = $mBillingHistoryHeaders->getInvoicePDFHeader($this->billingHistoryHeaderID);
                $contentDetails = $mBillingHistoryHeaderDetails->getInvoicePDFDetail($this->listBillingHistoryDetailID, $fieldSearch);
                $pdf = new InvoicePDF();
                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
                $pdf->AddPage();
                $pdf->getTotalPage($contentDetails);
                $pdf->writeHeader($contentHeader[0]);
                $pdf->writeDetails($contentDetails);
                header("listBillingHistoryHeaderID: $this->billingHistoryHeaderID");
                header("listBillingHistoryDetailID:". implode(',',$this->listBillingHistoryDetailID));
                $pdf->Output(storage_path('/pdf_template/'.$fileName),'FI');
            }
        }else{
            $oldName = storage_path('/pdf_template/'.$data['fileName']);
            chmod($oldName,0777);
            $newName = 'seikyu_hikae_'.$item['office_cd'].'_'.$item['customer_cd'].'_'.date('Ymd', time()).'.pdf';
            $pdf = new InvoicePDF();
            $pdf->SetPrintHeader(false);
            $pdf->SetPrintFooter(false);
            $pdf->createNewPdfFromExistedFile($oldName);
            $pdf->Close();
            unlink($oldName);
            $pdf->Output($newName);
        }
    }

    public function createCSV(Request $request){
        $data = $request->all();
        $fieldSearch = $data['fieldSearch'];
        $item = $data['data'];
        $keys = array_keys($this->csvColumn);
        $publication_date = $data['date_of_issue'];
        $listBillingHistoryHeaderID = $data['listBillingHistoryHeaderID'];
        $listBillingHistoryDetailID = $data['listBillingHistoryDetailID'];
        if(!empty($listBillingHistoryDetailID)){
            $this->billingHistoryHeaderID = $listBillingHistoryHeaderID;
            $this->listBillingHistoryDetailID = explode(',',$listBillingHistoryDetailID);
        }else{
            $this->createHistory($item,$fieldSearch,$publication_date);
        }
        $mBillingHistoryHeaderDetails =  new MBillingHistoryHeaderDetails();
        $csvContent = $mBillingHistoryHeaderDetails->getCSVContent($this->listBillingHistoryDetailID);
        $fileName = 'seikyu_'.$csvContent[0]->branch_office_cd.'_'.$item['customer_cd'].'_'.date('YmdHis', time()).'.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
            'listBillingHistoryHeaderID' => $this->billingHistoryHeaderID,
            'listBillingHistoryDetailID' => implode(',',$this->listBillingHistoryDetailID),
        );
        $enclosure = config('params.csv.enclosure');
        $callback = function() use ($keys,$item, $enclosure,$csvContent) {
            $file = fopen('php://output', 'w');
            fwrite ($file,implode(config('params.amazon_csv.delimiter'),mb_convert_encoding(array_values($this->csvColumn), "SJIS", "UTF-8"))."\r\n");
            foreach ($csvContent as $content) {
                $row = [];
                foreach ($keys as $key) {
                    $row[$key] = $enclosure.$content->{$key}.$enclosure;
                }
                fwrite ($file,implode(config('params.csv.delimiter'),mb_convert_encoding($row, "SJIS", "UTF-8"))."\r\n");
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function createAmazonCSV(Request $request){
        $data = $request->all();
        $fieldSearch = $data['fieldSearch'];
        $item = $data['data'];
        $keys = array_keys($this->amazonCsvColumn);
        $publication_date = $data['date_of_issue'];
        $listBillingHistoryHeaderID = $data['listBillingHistoryHeaderID'];
        $listBillingHistoryDetailID = $data['listBillingHistoryDetailID'];
        if(!empty($listBillingHistoryDetailID)){
            $this->billingHistoryHeaderID = $listBillingHistoryHeaderID;
            $this->listBillingHistoryDetailID = explode(',',$listBillingHistoryDetailID);
        }else{
            $this->createHistory($item,$fieldSearch,$publication_date);
        }

        $mBillingHistoryHeaderDetails =  new MBillingHistoryHeaderDetails();
        $amazonCSVContent = $mBillingHistoryHeaderDetails->getAmazonCSVContent($this->listBillingHistoryDetailID);
        $fileName = 'Amazon_seikyu_'.$amazonCSVContent[0]->branch_office_cd.'_'.$item['customer_cd'].'_'.date('YmdHis', time()).'.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
            'listBillingHistoryHeaderID' => $this->billingHistoryHeaderID,
            'listBillingHistoryDetailID' => implode(',',$this->listBillingHistoryDetailID),
        );

        $enclosure = config('params.amazon_csv.enclosure');
        $callback = function() use ($amazonCSVContent,$keys,$item, $enclosure) {
            $file = fopen('php://output', 'w');
            fwrite ($file,implode(config('params.amazon_csv.delimiter'),mb_convert_encoding(array_values($this->amazonCsvColumn), "SJIS", "UTF-8"))."\r\n");
            foreach ($amazonCSVContent as $content) {
                $row = [];
                foreach ($keys as $key) {
                    $row[$key] = $enclosure.$content->{$key}.$enclosure;
                }
                fwrite ($file,implode(config('params.amazon_csv.delimiter'),mb_convert_encoding($row, "SJIS", "UTF-8"))."\r\n");
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function createHistory($item,$fieldSearch,$publication_date){
        $currentTime = date("Y-m-d H:i:s",time());
        $mSaleses = new MSaleses();
        $mBillingHistoryHeaders =  new MBillingHistoryHeaders();
        $mBillingHistoryHeaderDetails =  new MBillingHistoryHeaderDetails();
        $mNumberings =  new MNumberings();
        $this->csvContent[$item['customer_cd']] = [];
        $serial_number = $mNumberings->getSerialNumberByTargetID('2001');
        DB::beginTransaction();
        try
        {
            $mBillingHistoryHeaders->invoice_number = $serial_number->serial_number;
            $mBillingHistoryHeaders->mst_customers_cd = $item['customer_cd'];
            $mBillingHistoryHeaders->mst_business_office_id = $item['mst_business_office_id'];
            $mBillingHistoryHeaders->publication_date = $publication_date;
            $mBillingHistoryHeaders->total_fee = $item['total_fee'];
            $mBillingHistoryHeaders->consumption_tax = $item['consumption_tax'];
            $mBillingHistoryHeaders->tax_included_amount = floatval($item['total_fee']) + floatval($item['consumption_tax']);
            $mBillingHistoryHeaders->add_mst_staff_id =  Auth::user()->id;
            $mBillingHistoryHeaders->upd_mst_staff_id = Auth::user()->id;
            if($mBillingHistoryHeaders->save()){
                $this->billingHistoryHeaderID = $mBillingHistoryHeaders->id;
                $history_details =  $mSaleses->getListByCustomerCd($item['customer_cd'], $item['mst_business_office_id'], $fieldSearch);
                $branch_number = 1;
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
                    $updateData = [
                        'invoicing_flag' => 1,
                        'modified_at' => $currentTime,
                        'upd_mst_staff_id' => Auth::user()->id,
                    ];
                    MSaleses::query()->where('id',$detail->id)->update($updateData);
                    $id =  MBillingHistoryHeaderDetails::query()->insertGetId( $arrayInsert );
                    array_push( $this->listBillingHistoryDetailID, $id);
                }
            }
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }

    }
}