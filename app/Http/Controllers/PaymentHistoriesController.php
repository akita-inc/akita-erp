<?php

namespace App\Http\Controllers;


use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\TPaymentHistories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

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
            'mst_customers_cd'=>$data['fieldSearch']['mst_customers_cd'],
            'customer_nm_formal'=>$data['fieldSearch']['customer_nm']
        );
        $this->query->select(
            DB::raw("DATE_FORMAT(t_payment_histories.dw_day, '%Y/%m/%d') as dw_day"),
            "t_payment_histories.mst_customers_cd",
            "mst_general_purposes.date_nm as dw_classification",
            DB::raw("mst_customers.customer_nm_formal as customer_nm"),
            DB::raw('IFNULL(SUM(t_payment_histories.actual_dw),0) as actual_dw'),
            DB::raw('IFNULL(SUM(t_payment_histories.fee),0) as fee'),
            DB::raw('IFNULL(SUM(t_payment_histories.discount),0) as discount'),
            DB::raw('IFNULL(SUM(t_payment_histories.total_dw_amount),0) as total_dw_amount'),
            't_payment_histories.dw_number',
            't_payment_histories.note');
        $this->query->join('mst_customers', function ($join) {
            $join->on('mst_customers.mst_customers_cd', '=', 't_payment_histories.mst_customers_cd')
                ->whereRaw('mst_customers.deleted_at IS NULL');
        })->join('t_billing_history_headers as bill_headers', function ($join) {
            $join->on('bill_headers.invoice_number', '=',  't_payment_histories.invoice_number')
                ->whereRaw('bill_headers.deleted_at IS NULL');
        })->leftJoin('mst_general_purposes',function ($join){
            $join->on('mst_general_purposes.date_id', '=',  't_payment_histories.dw_classification')
                ->where('mst_general_purposes.data_kb', config('params.data_kb')['payment_method_id']);
        });
        if ($where['from_date'] != '' && $where['to_date'] != '' ) {
            $this->query->where('t_payment_histories.dw_day', '>=',$where['from_date'])
                ->where('t_payment_histories.dw_day','<=',$where['to_date']);
        }
        if ($where['mst_customers_cd'] != '' ) {
            $this->query->where('mst_customers.mst_customers_cd', '=',  $where['mst_customers_cd']);
        }
        if ($where['customer_nm_formal'] != '' ) {
            $this->query->where('mst_customers.customer_nm_formal', 'LIKE',  '%'.$where['customer_nm_formal'].'%');
        }
        $this->query->where('t_payment_histories.deleted_at',null);
        $this->query->groupBy(
                            't_payment_histories.dw_number',
                            't_payment_histories.dw_day',
                            'mst_general_purposes.date_nm',
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
                "classTH" => "wd-100",
            ],
            'publication_date'=> [
                "classTH" => "wd-60",
            ],
            'total_fee'=> [
                "classTH" => "wd-100",
            ],
            'consumption_tax'=> [
                "classTH" => "wd-100",
            ],
            'tax_included_amount'=> [
                "classTH" => "wd-100",
            ],
            'last_payment_amount'=> [
                "classTH" => "wd-100",
            ],
            'total_dw_amount'=> [
                "classTH" => "wd-60",
            ],
            'fee'=> [
                "classTH" => "wd-100",
            ],
            'discount'=> [
                "classTH" => "wd-60",
            ],
            'deposit_balance'=> [
                "classTH" => "wd-120",
            ],

        ];
        return view('payment_histories.index',[
            'fieldShowTable'=>$fieldShowTable,
            'fieldShowTableDetails'=>$fieldShowTableDetails,
        ]);
    }
    public function delete($dw_number)
    {
        $this->backHistory();
        if (TPaymentHistories::query()->where("dw_number","=",$dw_number)->update([
                    'deleted_at' => date("Y-m-d H:i:s",time()),
                    'upd_mst_staff_id'=>Auth::user()->id]))
        {
            $response = ['success' => 'false','msg'=>Lang::get('messages.MSG10004')];
        } else {
            $response = ['success' => 'true', 'msg' => Lang::get('messages.MSG06002')];
        }
        return response()->json($response);
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
        $recentDwNumber=DB::select('SELECT
                                                max(dw_number) as dw_number
                                          FROM
                                                t_payment_histories
                                          WHERE
                                                deleted_at IS NULL');
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
            'recent_dw_number'=>isset($recentDwNumber[0]->dw_number)?$recentDwNumber[0]->dw_number:null,
            'fieldSearch' => $data['fieldSearch'],
            'order' => $data['order'],
        ];
        return response()->json($response);
    }



}