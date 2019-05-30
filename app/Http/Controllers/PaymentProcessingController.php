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

class PaymentProcessingController extends Controller{
    use ListTrait;
    public $table = "t_billing_history_headers";
    public $allNullAble = false;
    public $beforeItem = null;
    public $add_log = false;

    public $ruleValid = [
    ];

    public $labels = [];

    public $csvColumn = [
        'mst_suppliers_cd' => '仕入先コード',
        'supplier_nm' => '仕入先名',
        'purchases_tax_included_amount' => '請求金額',
        'saleses_tax_included_amount' => '売上金額',
    ];

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
            'invoice_number' => [
                "classTH" => "wd-60",
            ],
            'publication_date'=> [
                "classTH" => "wd-60",
            ],
            'office_nm'=> [
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
            'last_payment_amount'=> [
                "classTH" => "wd-60",
            ],
            'total_dw_amount'=> [
                "classTH" => "wd-60",
            ],
            'fee'=> [
                "classTH" => "wd-60",
            ],
            'discount'=> [
                "classTH" => "wd-60",
            ],
            'payment_remaining'=> [
                "classTH" => "wd-60",
            ],
        ];
        $mGeneralPurposes = new MGeneralPurposes();
        $listDepositMethod= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.deposit_method'),'Empty');
        return view('payment_processing.index',[
            'fieldShowTable'=>$fieldShowTable,
            'listDepositMethod'=>$listDepositMethod,
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
}