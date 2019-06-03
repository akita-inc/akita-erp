<?php

namespace App\Http\Controllers;


use App\Helpers\InvoicePDF;
use App\Helpers\TimeFunction;
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


class InvoiceHistoryController extends Controller {
    use ListTrait,FormTrait;
    public $table = "t_billing_history_headers";
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
        $start_date = TimeFunction::dateFormat($dataSearch['start_date'],'Y-m-d');
        $end_date = TimeFunction::dateFormat($dataSearch['end_date'],'Y-m-d');
        $this->query = DB::table(DB::raw($this->table.' as tb') )->select(
            'tb.id',
            'tb.invoice_number',
            DB::raw("tb.mst_customers_cd as customer_cd"),
            'tb.mst_business_office_id',
            DB::raw("office.mst_business_office_cd as office_cd"),
            DB::raw("format(IFNULL( tp.total_dw_amount, 0 ), '#,##0') AS payment_amount"),
            DB::raw("format(IFNULL( ( tb.tax_included_amount - IFNULL( tp.total_dw_amount, 0 )), 0 ), '#,##0') AS payment_remaining"),
            DB::raw("( CASE WHEN tp.total_dw_amount = tb.tax_included_amount THEN '済' ELSE '未' END ) AS payment "),
            DB::raw("office.business_office_nm as regist_office"),
            DB::raw("mst_customers.customer_nm_formal as customer_nm"),
            DB::raw("DATE_FORMAT(publication_date, '%Y/%m/%d') as publication_date"),
            DB::raw("format(IFNULL(tb.total_fee,0), '#,##0') as total_fee"),
            DB::raw("format( IFNULL(tb.consumption_tax,0), '#,##0') as consumption_tax"),
            DB::raw("format(IFNULL(tb.tax_included_amount,0), '#,##0') as tax_included_amount")
        )
        ->whereNull('tb.deleted_at')
        ->where('tb.publication_date','>=',$start_date)
        ->where('tb.publication_date','<=',$end_date)
        ->leftJoin(DB::raw('( SELECT invoice_number, IFNULL( SUM( total_dw_amount ), 0 ) AS total_dw_amount FROM t_payment_histories WHERE deleted_at IS NULL GROUP BY invoice_number ) tp'),
            function($join)
            {
                $join->on('tb.invoice_number', '=', 'tp.invoice_number');
            })
        ->join('mst_customers', function ($join) {
            $join->on('mst_customers.mst_customers_cd', '=', 'tb.mst_customers_cd')
                ->whereNull('mst_customers.deleted_at');
        })
        ->join(DB::raw('mst_business_offices office'), function ($join) {
            $join->on('office.id', '=', 'tb.mst_business_office_id')
                ->whereNull('office.deleted_at');
        });
        if ($dataSearch['mst_business_office_id'] != '') {
            $this->query->where('tb.mst_business_office_id','=',$dataSearch['mst_business_office_id']);
        };
        if ($dataSearch['customer_cd'] != '') {
            $this->query->where('tb.mst_customers_cd','=',$dataSearch['customer_cd']);
        }
        if($dataSearch['display_remaining_payment']){

            $this->query->having('payment' ,'=', '未');
            $this->query->groupBy(
                'tb.id',
                'tb.invoice_number',
                "tb.mst_customers_cd",
                'tb.mst_business_office_id',
                'tp.total_dw_amount',
                'tb.publication_date',
                'tb.total_fee',
                'tb.consumption_tax',
                'tb.tax_included_amount',
                'mst_customers.customer_nm_formal',
                'office.business_office_nm'
            );
        }
        $this->query->orderBy('tb.mst_business_office_id');
        $this->query->orderBy('tb.mst_customers_cd');
        return $this->query->get();

    }

    public function index(Request $request){
        $fieldShowTable = [
            'regist_office' => [
                "classTH" => "wd-100",
            ],
            'customer_cd'=> [
                "classTH" => "wd-60",
            ],
            'customer_nm'=> [
                "classTH" => "wd-120",
            ],
            'invoice_number'=>[
                "classTH" => "wd-60",
            ],
            'publication_date'=>[
                "classTH" => "wd-60",
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
            'payment'=> [
                "classTH" => "wd-60",
            ],
            'payment_amount'=> [
                "classTH" => "wd-100",
            ],
            'payment_remaining'=> [
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
        $mBussinessOffice = new MBusinessOffices();
        $mCustomer = new MCustomers();
        $businessOffices = $mBussinessOffice->getAllData();
        $dateData = $this->getFirstLastDatePreviousMonth(true);
        $first_day = $dateData['firstDayPreviousMonth'];
        $last_day = $dateData['lastDayPreviousMonth'];
        return view('invoice_history.index',[
            'fieldShowTable'=>$fieldShowTable,
            'fieldShowTableDetails'=>$fieldShowTableDetails,
            'businessOffices'=> $businessOffices,
            'firstDayPreviousMonth'=>$first_day,
            'lastDayPreviousMonth'=>$last_day,
        ]);
    }

    public function getFirstLastDatePreviousMonth($notJsonFg=false){
        $month_start = new \DateTime("first day of last month");
        $first_day = $month_start->format('Y/m/d');
        $month_end = new \DateTime("last day of last month");
        $last_day = $month_end->format('Y/m/d');
        if($notJsonFg){
            return [
                'firstDayPreviousMonth'=>$first_day,
                'lastDayPreviousMonth'=>$last_day,
            ];
        }
        return response()->json([
            'firstDayPreviousMonth'=>$first_day,
            'lastDayPreviousMonth'=>$last_day,
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


    public function getDetailsInvoice(Request $request){
        $input = $request->all();
        $mBillingHistoryHeaderDetails= new MBillingHistoryHeaderDetails();
        $listDetail =  $mBillingHistoryHeaderDetails->getListByCondition($input);
        return response()->json([
            'success'=>true,
            'info'=> $listDetail,
        ]);
    }

    public function createPDF(Request $request){
        $mBillingHistoryHeaders =  new MBillingHistoryHeaders();
        $mBillingHistoryHeaderDetails =  new MBillingHistoryHeaderDetails();
        $data = $request->all();
        $item = $data['data'];
        $type= $data['type'];
        if($type==1){
            $this->billingHistoryHeaderID = $item['id'];
            $listDetails = $mBillingHistoryHeaderDetails->getListByCondition($item);
            if($listDetails){
                $this->listBillingHistoryDetailID = array_column($listDetails->toArray(),'id');
            }
            $fileName = 'seikyu_'.$item['office_cd'].'_'.$item['customer_cd'].'_'.date('Ymd', time()).'.pdf';
            if(!empty($this->billingHistoryHeaderID)){
                $contentHeader = $mBillingHistoryHeaders->getInvoicePDFHeader($this->billingHistoryHeaderID);
                $contentDetails = $mBillingHistoryHeaderDetails->getInvoicePDFDetail($this->listBillingHistoryDetailID);
                $pdf = new InvoicePDF();
                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
                $pdf->AddPage();
                $pdf->getTotalPage($contentDetails);
                $pdf->writeHeader($contentHeader[0]);
                $pdf->writeDetails($contentDetails);
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
        $mBillingHistoryHeaderDetails =  new MBillingHistoryHeaderDetails();
        $data = $request->all();
        $item = $data['data'];
        $keys = array_keys($this->csvColumn);
        $this->billingHistoryHeaderID = $item['id'];
        $listDetails = $mBillingHistoryHeaderDetails->getListByCondition($item);
        if($listDetails){
            $this->listBillingHistoryDetailID = array_column($listDetails->toArray(),'id');
        }
        $csvContent = $mBillingHistoryHeaderDetails->getCSVContent($this->listBillingHistoryDetailID);
        $fileName = 'seikyu_'.$csvContent[0]->branch_office_cd.'_'.$item['customer_cd'].'_'.date('YmdHis', time()).'.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );
        $enclosure = config('params.csv.enclosure');
        $callback = function() use ($keys,$item, $enclosure,$csvContent) {
            $file = fopen('php://output', 'w');
            fwrite ($file,implode(config('params.amazon_csv.delimiter'),mb_convert_encoding(array_values($this->csvColumn), "SJIS-win", "UTF-8"))."\r\n");
            foreach ($csvContent as $content) {
                $row = [];
                foreach ($keys as $key) {
                    $row[$key] = $enclosure.$content->{$key}.$enclosure;
                }
                fwrite ($file,implode(config('params.csv.delimiter'),mb_convert_encoding($row, "SJIS-win", "UTF-8"))."\r\n");
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function createAmazonCSV(Request $request){
        $mBillingHistoryHeaderDetails =  new MBillingHistoryHeaderDetails();
        $data = $request->all();
        $item = $data['data'];
        $keys = array_keys($this->amazonCsvColumn);
        $this->billingHistoryHeaderID = $item['id'];
        $listDetails = $mBillingHistoryHeaderDetails->getListByCondition($item);
        if($listDetails){
            $this->listBillingHistoryDetailID = array_column($listDetails->toArray(),'id');
        }
        $amazonCSVContent = $mBillingHistoryHeaderDetails->getAmazonCSVContent($this->listBillingHistoryDetailID);
        $fileName = 'Amazon_seikyu_'.$amazonCSVContent[0]->branch_office_cd.'_'.$item['customer_cd'].'_'.date('YmdHis', time()).'.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        );

        $enclosure = config('params.amazon_csv.enclosure');
        $callback = function() use ($amazonCSVContent,$keys,$item, $enclosure) {
            $file = fopen('php://output', 'w');
            fwrite ($file,implode(config('params.amazon_csv.delimiter'),mb_convert_encoding(array_values($this->amazonCsvColumn), "SJIS-win", "UTF-8"))."\r\n");
            foreach ($amazonCSVContent as $content) {
                $row = [];
                foreach ($keys as $key) {
                    $row[$key] = $enclosure.$content->{$key}.$enclosure;
                }
                fwrite ($file,implode(config('params.amazon_csv.delimiter'),mb_convert_encoding($row, "SJIS-win", "UTF-8"))."\r\n");
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}