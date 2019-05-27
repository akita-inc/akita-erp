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


class PaymentHistories extends Controller {
    use ListTrait,FormTrait;
    public $table = "t_payment_histories";
    public $allNullAble = false;
    public $beforeItem = null;
    public $add_log = false;

    public $ruleValid = [
    ];

    public $labels = [

    ];

    public $csvColumn = [

    ];
    public $amazonCsvColumn = [

    ];

    public $csvContent = [];

    public $billingHistoryHeaderID ="";
    public $listBillingHistoryDetailID = [];

    public function __construct(){
        date_default_timezone_set("Asia/Tokyo");
        parent::__construct();
    }

    public function getItems(Request $request)
    {        if(Session::exists('backQueryFlag') && Session::get('backQueryFlag')){
            if(Session::exists('backQueryFlag') ){
                $data = Session::get('requestHistory');
            }
            Session::put('backQueryFlag', false);
        }else{
            $data = $request->all();
            Session::put('requestHistory', $data);
        }
        $fieldSearch = $data['fieldSearch'];
        $validator = Validator::make( $fieldSearch, $this->ruleValid ,[] ,$this->labels );
        if ( $validator->fails() ) {
            return response()->json([
                'success'=>FALSE,
                'message'=> $validator->errors()
            ]);
        }else {
            $this->getQuery();
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
        $where = array(
            'from_date' =>date('Y-m-d', strtotime($data['fieldSearch']['from_date'])),
            'to_date'=>date('Y-m-d', strtotime($data['fieldSearch']['to_date'])),
            'mst_customers_cd'=>$data['fieldSearch']['customer_cd'],
        );
        $this->query->select(
            DB::raw("DATE_FORMAT(payment.dw_day, '%Y/%m/%d') as dw_day"),
            DB::raw("t_payment_histories as payment")
        );
        $this->query->where('t_saleses.deleted_at',null);
//        $this->query->orderBy('t_saleses.document_no','asc')
//            ->orderBy('t_saleses.daily_report_date','asc');
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
                "classTH" => "wd-100",
            ],
            'dw_classification'=> [
                "classTH" => "wd-120",
            ],
            'total_dw_amount'=> [
                "classTH" => "wd-100",
            ],
            'fee'=> [
                "classTH" => "wd-120",
            ],
            'discount'=> [
                "classTH" => "wd-100",
            ],
            'note'=> [
                "classTH" => "wd-100",
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
        $mCustomer = new MCustomers();
        return view('payment_histories.index',[
            'fieldShowTable'=>$fieldShowTable,
            'fieldShowTableDetails'=>$fieldShowTableDetails,
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