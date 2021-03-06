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
use Illuminate\Support\Facades\Session;
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
    public $csvColumn=[
            "daily_report_date"=>"日報日付",
            "branch_office_cd"=>"支店CD",
            "document_no"=>"伝票NO",
            "registration_numbers"=>"登録番号",
            "staff_cd"=>"社員CD",
            "staff_nm"=>"社員名",
            "mst_customers_cd"=>"得意先CD",
            "customer_nm"=>"得意先名",
            "goods"=>"品物",
            "departure_point_name"=>"発地名",
            "landing_name"=>"着地名",
            "delivery_destination"=>"納入先",
            "quantity"=>"数量",
            "unit_price"=>"単価",
            "total_fee"=>"便請求金額",
            "insurance_fee"=>"保険料",
            "billing_fast_charge"=>"請求高速料",
            "discount_amount"=>"値引金額",
            "tax_included_amount"=>"請求金額",
            "loading_fee"=>"積込料",
            "wholesale_fee"=>"取卸料",
            "waiting_fee"=>"待機料",
            "incidental_fee"=>"附帯料",
            "surcharge_fee"=>"サーチャージ料",
    ];
    public $currentData=null;
    public function __construct(){
        parent::__construct();

    }
    protected function getPaging(){
        return config('params.page_size_sale_lists');
    }
    protected function search($data){
        $where = array(
            'mst_business_office_id' => $data['fieldSearch']['mst_business_office_id'],
            'from_date' =>date('Y-m-d', strtotime($data['fieldSearch']['from_date'])),
            'to_date'=>date('Y-m-d', strtotime($data['fieldSearch']['to_date'])),
            'invoicing_flag'=>$data['fieldSearch']['invoicing_flag'],
            'mst_customers_cd'=>$data['fieldSearch']['mst_customers_cd'],
        );
        $this->query->select(
                DB::raw("DATE_FORMAT(t_saleses.daily_report_date, '%Y/%m/%d') as daily_report_date"),
                't_saleses.branch_office_cd',
                't_saleses.document_no',
                'mst_vehicles.registration_numbers',
                't_saleses.staff_cd',
                DB::raw('CONCAT_WS("　",mst_staffs.last_nm,mst_staffs.first_nm) as staff_nm'),
                't_saleses.mst_customers_cd',
                'mst_customers.customer_nm_formal as customer_nm',
                't_saleses.goods',
                't_saleses.departure_point_name',
                't_saleses.landing_name',
                't_saleses.delivery_destination',
                't_saleses.quantity',
                't_saleses.unit_price',
                't_saleses.total_fee',
                't_saleses.insurance_fee',
                't_saleses.billing_fast_charge',
                't_saleses.discount_amount',
                't_saleses.tax_included_amount',
                't_saleses.loading_fee',
                't_saleses.wholesale_fee',
                't_saleses.waiting_fee',
                't_saleses.incidental_fee',
                't_saleses.surcharge_fee',
                't_saleses.consumption_tax',
                DB::raw("DATE_FORMAT(headers.publication_date, '%Y/%m/%d') as publication_date")
            );
        $this->query->join('mst_customers', function ($join) {
                $join->on('mst_customers.mst_customers_cd', '=', 't_saleses.mst_customers_cd')
                    ->whereRaw('mst_customers.deleted_at IS NULL');
        })->leftJoin('mst_vehicles', function ($join) {
            $join->on('mst_vehicles.vehicles_cd', '=', 't_saleses.vehicles_cd')
                ->whereRaw('mst_vehicles.deleted_at IS NULL');
        })->leftJoin('mst_staffs', function ($join) {
            $join->on('mst_staffs.staff_cd', '=', 't_saleses.staff_cd')
                ->whereRaw('mst_staffs.deleted_at IS NULL');
        })->leftJoin('t_billing_history_header_details as details', function ($join) {
                $join->on('details.document_no','=','t_saleses.document_no')
                    ->whereRaw('details.deleted_at IS NULL')
                    ->whereRaw('t_saleses.branch_office_cd=details.branch_office_cd');
        })->leftJoin('t_billing_history_headers as headers', function ($join) {
                $join->on('headers.invoice_number', '=', 'details.invoice_number')
                    ->whereRaw('headers.deleted_at IS NULL');
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

        $this->getQuery();
        $this->search( $data );
        $allItems=$this->query->get();
        foreach ($allItems as $key=> $item)
        {
            unset($item->consumption_tax);
            unset($item->publication_date);
        }
        $items = $this->query->paginate($this->getPaging(), ['*'], 'page', $data['page']);
        if(count($items->items())==0){
            if($data['page'] > 1){
                $items = $this->query->paginate($this->getPaging(), ['*'], 'page', $data['page']-1);
            }
        }
        $response = [
            'pagination' => [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem()
            ],
            'data' => $items,
            'allItems'=>$allItems,
            'export_file_nm'=>config("params.export_csv.sales_lists.name"),
            'fieldSearch' => $data['fieldSearch'],
            'order' => $data['order'],
        ];
        return response()->json($response);
    }
    public function createCSV(Request $request){
        $items=$request->all()['data'];
        $keys = array_keys($this->csvColumn);
        $inputs=json_decode(json_encode($items), true);
        $fileName = 'kakunin_'.$items[0]['branch_office_cd'].'_'.date('YmdHis', time()).'.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $enclosure = config('params.csv.enclosure');
        $callback = function() use ($keys,$inputs, $enclosure) {
            $file = fopen('php://output', 'w');
            fwrite ($file,implode(config('params.csv.delimiter'),mb_convert_encoding(array_values($this->csvColumn), "SJIS", "UTF-8"))."\r\n");
            foreach ($inputs as $content) {
                $row = [];
                foreach ($keys as $key) {
                    $row[$key] = $enclosure.$content[$key].$enclosure;
                }
                fwrite ($file,implode(config('params.csv.delimiter'),mb_convert_encoding($row, "SJIS", "UTF-8"))."\r\n");
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }


}
