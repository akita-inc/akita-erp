<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 3/5/2019
 * Time: 4:11 PM
 */
namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MAccountTitles;
use App\Models\MBusinessOffices;
use App\Models\MCustomers;
use App\Models\MCustomersCategories;
use App\Models\MGeneralPurposes;
use App\Models\MModifyLogs;
use App\Models\MMstBillIssueDestinations;
use App\Models\MStaffAuths;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class CustomersController extends Controller
{
    use ListTrait,FormTrait;
    public $table = "mst_customers";
    public $allNullAble = false;
    public $beforeItem = null;

    public $ruleValid = [
        'mst_customers_cd'  => 'required|one_bytes_string|length:5',
        'discount_rate'  => 'nullable|one_byte_number|length:3',
        'customer_nm'  => 'required|nullable|length:200',
        'customer_nm_kana'  => 'kana|nullable|length:200',
        'customer_nm_formal'  => 'length:200|nullable',
        'customer_nm_kana_formal'  => 'kana|nullable|length:200',
        'person_in_charge_last_nm'  => 'length:25|nullable',
        'person_in_charge_first_nm'  => 'length:25|nullable',
        'person_in_charge_last_nm_kana'  => 'kana|nullable|length:50',
        'person_in_charge_first_nm_kana'  => 'kana|nullable|length:50',
        'zip_cd'  => 'zip_code|nullable|length:7',
        'address1'  => 'nullable|length:20',
        'address2'  => 'nullable|length:20',
        'address3'  => 'nullable|length:50',
        'phone_number'  => 'phone_number|nullable|length:20',
        'fax_number'  => 'fax_number|nullable|length:20',
        'hp_url'  => 'nullable|length:2500',
        'explanations_bill'  => 'nullable|length:100',
        'bundle_dt'  => 'one_byte_number|nullable|length:2',
        'deposit_day'  => 'one_byte_number|nullable|between_custom:1,31|length:2',
        'deposit_method_notes'  => 'nullable|length:200',
        'deposit_bank_cd'  => 'nullable|length:4',
        'notes'  => 'nullable|length:50',
    ];

    public $labels = [];

    public $messagesCustom = [];

    public function __construct(){
        $this->labels = Lang::get("customers.create.field");
        parent::__construct();
    }

    protected function search($data){
        $dataSearch=$data['fieldSearch'];
        $this->query->select('mst_customers.id',
            'mst_customers.mst_customers_cd',
            'mst_customers.customer_nm',
            'mst_customers.customer_nm_kana',
            DB::raw("CONCAT_WS('',mst_general_purposes.date_nm,mst_customers.address1,mst_customers.address2,mst_customers.address3) as street_address"),
            'mst_customers.explanations_bill',
            DB::raw("DATE_FORMAT(mst_customers.modified_at, '%Y/%m/%d') as modified_at")
        );
        $this->query->leftJoin('mst_general_purposes', function ($join) {
            $join->on("data_kb", "=", DB::raw(config("params.data_kb.prefecture_cd")));
            $join->on("date_id", "=", "mst_customers.prefectures_cd");
        })
        ->whereRaw('mst_customers.deleted_at IS NULL');

        if ($dataSearch['mst_customers_cd'] != '') {
            $this->query->where('mst_customers.mst_customers_cd', 'LIKE', '%' . $dataSearch['mst_customers_cd'] . '%');
        }
        if ($dataSearch['customer_nm'] != '') {
            $this->query->where('mst_customers.customer_nm', 'LIKE', '%' . $dataSearch['customer_nm'] . '%');
        }

        if ($data["order"]["col"] != '') {
            if ($data["order"]["col"] == 'street_address')
                $orderCol = "CONCAT_WS('',mst_general_purposes.date_nm,mst_customers.address1,mst_customers.address2,mst_customers.address3)";
            else
                $orderCol = $data["order"]["col"];
            if (isset($data["order"]["descFlg"]) && $data["order"]["descFlg"]) {
                $orderCol .= " DESC";
            }
            $this->query->orderbyRaw($orderCol);
        } else {
            $this->query->orderby('mst_customers.mst_customers_cd');
        }
    }

    public function index(Request $request){
        $fieldShowTable = [
            'mst_customers_cd' => [
                "classTH" => "wd-100"
            ],
            'customer_nm'=> [
                "classTH" => "",
                "sortBy" => "customer_nm_kana"
            ],
            'street_address'=> [
                "classTH" => ""
            ],
            'explanations_bill'=> [
                "classTH" => "",
                "classTD" => "td-nl2br",
            ],
            'modified_at'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ]
        ];
        return view('customers.index',[ 'fieldShowTable'=>$fieldShowTable ]);
    }

    public function delete($id)
    {
        $mCustomers = new MCustomers();
        $this->backHistory();
        if ($mCustomers->deleteCustomer($id)) {
            \Session::flash('message',Lang::get('messages.MSG10004'));
            $response = ['data' => 'success'];
        } else {
            \Session::flash('message',Lang::get('messages.MSG06002'));
            $response = ['data' => 'failed', 'msg' => Lang::get('messages.MSG06002')];
        }
        return response()->json($response);
    }

    public function checkIsExist(Request $request,$id){
        $mode = $request->get('mode');
        $mCustomers = new MCustomers();
        $mCustomers = $mCustomers->find($id);
        if (isset($mCustomers)) {
            return Response()->json(array('success'=>true));
        } else {
            return Response()->json(array('success'=>false, 'msg'=> is_null($mode) ? Lang::trans('messages.MSG04004') : Lang::trans('messages.MSG04001')));
        }
    }

    public function store(Request $request, $id=null){
        $mGeneralPurposes = new MGeneralPurposes();
        $mCustomerCategories=new MCustomersCategories();
        $mBusinessOffices=new MBusinessOffices();
        $mAccountTitles=new MAccountTitles();
        $customer_categories=$mCustomerCategories->getListCustomerCategories();
        $mBusinessOffices=$mBusinessOffices->getListBusinessOffices();
        $listAccountTitles=$mAccountTitles->getListAccountTitles();
        $listPrefecture= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['prefecture_cd'],'');
        $listDepositMethods=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['deposit_method'],'');
        $listDepositMonths=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['deposit_month'],'');
        $listConsumptionTaxCalcUnit=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['consumption_tax_calc_unit'],'');
        $listRoundingMethod=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['rounding_method'],'');
        $mStaffAuth =  new MStaffAuths();
        $role = $mStaffAuth->getRoleBySCreen(3);
        $customer = null;
        //load form by update
        if($id != null){
            $customer = MCustomers::find( $id );
            if(empty($customer)){
                abort('404');
            }else{
                $customer = $customer->toArray();
            }
        }
        return view('customers.form', [
            'customer' => $customer,
            'listPrefecture' => $listPrefecture,
            'customer_categories'=>$customer_categories,
            'business_offices'=>$mBusinessOffices,
            'listDepositMethods'=>$listDepositMethods,
            'listDepositMonths'=>$listDepositMonths,
            'listConsumptionTaxCalcUnit'=>$listConsumptionTaxCalcUnit,
            'listRoundingMethod'=>$listRoundingMethod,
            'listAccountTitles'=>$listAccountTitles,
            'role' => $role,
        ]);
    }

    protected function validAfter( &$validator,$data ){
        $mst_bill_issue_destinations = $data["mst_bill_issue_destinations"];
        $errorsEx = [];
        $this->allNullAble = true;
        if( count($mst_bill_issue_destinations) > 0 ){
            foreach ($mst_bill_issue_destinations as $index => $items){
                foreach ($items as $valueChk){
                    if(!empty($valueChk)){
                        $this->allNullAble = false;
                    }
                }
                $validatorEx = Validator::make( $items, [
                    'zip_cd'  => 'required|zip_code|nullable|length:7',
                    'address1'  => 'nullable|length:20',
                    'address2'  => 'nullable|length:20',
                    'address3'  => 'nullable|length:50',
                    'phone_number'  => 'phone_number|nullable|length:20',
                    'fax_number'  => 'fax_number|nullable|length:20'
                ] ,$this->messagesCustom ,$this->labels );
                if ( $validatorEx->fails() ) {
                    $errorsEx[$index] = $validatorEx->errors();
                }
            }
        }
        if( count($errorsEx) > 0 && !$this->allNullAble){
            $validator->errors()
                ->add("mst_bill_issue_destinations",$errorsEx);
        }

        if (!isset($data["id"]) && isset($data['mst_customers_cd']) && !empty($data['mst_customers_cd'])){
            $countExist = MCustomers::query()
                ->where('mst_customers_cd','=',$data['mst_customers_cd'])
                ->whereNull("deleted_at");
            $countExist = $countExist->count();
            if ($countExist > 0) {
                $validator->errors()->add('mst_customers_cd', str_replace(':screen', '得意先', Lang::get('messages.MSG10003')));
            }
        }
    }

    protected function save($data){
        $arrayInsert = $data;
        $currentTime = date("Y-m-d H:i:s",time());
        $mst_bill_issue_destinations =  $data["mst_bill_issue_destinations"];
        unset($arrayInsert["mst_bill_issue_destinations"]);
        unset($arrayInsert["id"]);
        DB::beginTransaction();
        if(isset($arrayInsert["except_g_drive_bill_fg"]) && $arrayInsert["except_g_drive_bill_fg"] == true){
            $arrayInsert["except_g_drive_bill_fg"] = 1;
        }else{
            $arrayInsert["except_g_drive_bill_fg"] = 0;
        }
        if(isset( $data["id"]) && $data["id"]){
            $id = $data["id"];
            $arrayInsert["modified_at"] = $currentTime;
            MCustomers::query()->where("id","=",$id)->update( $arrayInsert );
        }else {
            $arrayInsert["created_at"] = $currentTime;
            $arrayInsert["modified_at"] = $currentTime;
            $id =  MCustomers::query()->insertGetId( $arrayInsert );
        }
        $arrayIDInsert = [];
        if( count($mst_bill_issue_destinations) > 0 && !$this->allNullAble ){
            $disp_number = 1;
            foreach ($mst_bill_issue_destinations as $bill_issue_destination){
                $arrayInsertBill = [
                    'mst_customer_id' => $id,
                    'bill_zip_cd' => $bill_issue_destination['zip_cd'],
                    'bill_address1' => $bill_issue_destination['prefectures_cd'],
                    'bill_address2' => $bill_issue_destination['address1'],
                    'bill_address3' => $bill_issue_destination['address2'],
                    'bill_address4' => $bill_issue_destination['address3'],
                    'bill_phone_number' => $bill_issue_destination['phone_number'],
                    'bill_fax_number' => $bill_issue_destination['fax_number'],
                    'disp_number' => $disp_number,
                ];
                if(isset($bill_issue_destination["id"]) && $bill_issue_destination["id"]){
                    $arrayInsertBill["modified_at"] = $currentTime;
                    $dataBeforeUpdate = DB::table("mst_bill_issue_destinations")->where("id","=",$bill_issue_destination["id"])->first();
                    MMstBillIssueDestinations::query()
                        ->where("id","=",$bill_issue_destination["id"])->update($arrayInsertBill);
                    $arrayIDInsert[] = $flagUpdate = $bill_issue_destination["id"];
                    $modifyLog = new MModifyLogs();
                    $modifyLog->writeLogWithTable( "mst_bill_issue_destinations",$dataBeforeUpdate,$arrayInsertBill,$bill_issue_destination["id"] );
                }else{
                    $arrayInsertBill["created_at"] = $currentTime;
                    $arrayInsertBill["modified_at"] = $currentTime;
                    $flagUpdate = MMstBillIssueDestinations::query()->insertGetId($arrayInsertBill);
                    $arrayIDInsert[] = $flagUpdate;
                }
                if(!$flagUpdate){
                    DB::rollBack();
                    return false;
                }
                $disp_number++;
            }
        }
        if(isset( $data["id"]) && $data["id"]) {
            $deleteBill = DB::table("mst_bill_issue_destinations")
                ->where("mst_customer_id", $id);
            if (!empty($arrayIDInsert)) {
                $deleteBill = $deleteBill->whereNotIn("id", $arrayIDInsert);
            }
            $deleteBill->update(['deleted_at' => $currentTime]);
        }
        DB::commit();
        if(isset( $data["id"])){
            $this->backHistory();
        }
        \Session::flash('message',Lang::get('messages.MSG03002'));
        return $id;
    }

    public function getListBill( $id ){
        $listBills = DB::table("mst_bill_issue_destinations")
            ->select(
                "id",
                "bill_zip_cd as zip_cd",
                "bill_address1 as prefectures_cd",
                "bill_address1 as prefectures_cd",
                "bill_address2 as address1",
                "bill_address3 as address2",
                "bill_address4 as address3",
                "bill_phone_number as phone_number",
                "bill_fax_number as fax_number"
            )
            ->where("mst_customer_id","=",$id)
            ->whereNULL('deleted_at')
            ->orderBy(DB::raw("disp_number*1"))
            ->get();
        if($listBills){
            $listBills->toArray();
        }
        return Response()->json(array('success'=>true, 'data'=> $listBills));
    }
}
