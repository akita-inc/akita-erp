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
        'adhibition_start_dt'  => 'required',
        'discount_rate'  => 'nullable|one_byte_number|length:3',
        'customer_nm'  => 'required|nullable|length:200',
        'customer_nm_kana'  => 'kana|nullable|length:200',
        'customer_nm_formal'  => 'length:200|nullable',
        'customer_nm_kana_formal'  => 'kana|nullable|length:200',
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
        $reference_date=date('Y-m-d', strtotime($dataSearch['reference_date']) );
        $this->query->select('mst_customers.id',
            'mst_customers.mst_customers_cd',
            'mst_customers.customer_nm',
            'mst_customers.customer_nm_kana',
            DB::raw("CONCAT_WS('',mst_general_purposes.date_nm,mst_customers.address1,mst_customers.address2,mst_customers.address3) as street_address"),
            'mst_customers.explanations_bill',
            DB::raw("DATE_FORMAT(mst_customers.adhibition_start_dt, '%Y/%m/%d') as adhibition_start_dt"),
            DB::raw("DATE_FORMAT(mst_customers.adhibition_end_dt, '%Y/%m/%d') as adhibition_end_dt"),
            DB::raw("DATE_FORMAT(mst_customers.modified_at, '%Y/%m/%d') as modified_at"),
            DB::raw("DATE_FORMAT(sub.max_adhibition_end_dt, '%Y/%m/%d') as max_adhibition_end_dt")
        );
        $this->query->leftJoin('mst_general_purposes', function ($join) {
            $join->on("data_kb", "=", DB::raw(config("params.data_kb.prefecture_cd")));
            $join->on("date_id", "=", "mst_customers.prefectures_cd");
        })
        ->leftjoin(DB::raw('(select mst_customers_cd, max(adhibition_end_dt) AS max_adhibition_end_dt from mst_customers where deleted_at IS NULL group by mst_customers_cd) sub'), function ($join) {
            $join->on('sub.mst_customers_cd', '=', 'mst_customers.mst_customers_cd');
        })
        ->whereRaw('mst_customers.deleted_at IS NULL');

        if ($dataSearch['mst_customers_cd'] != '') {
            $this->query->where('mst_customers.mst_customers_cd', 'LIKE', '%' . $dataSearch['mst_customers_cd'] . '%');
        }
        if ($dataSearch['customer_nm'] != '') {
            $this->query->where('mst_customers.customer_nm', 'LIKE', '%' . $dataSearch['customer_nm'] . '%');
        }
        if ($dataSearch['status'] == '1' && $reference_date!=null) {
            $this->query->where('mst_customers.adhibition_start_dt','<=',$reference_date)
                        ->where('mst_customers.adhibition_end_dt','>=',$reference_date);
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
            $this->query->orderby('mst_customers.adhibition_start_dt');
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
            'adhibition_start_dt'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'adhibition_end_dt'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
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

    public function checkIsExist($id){
        $mCustomers = new MCustomers();
        $mCustomers = $mCustomers->find($id);
        if (isset($mCustomers)) {
            return Response()->json(array('success'=>true));
        } else {
            return Response()->json(array('success'=>false, 'msg'=> Lang::trans('messages.MSG06003')));
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
        $role = $mStaffAuth->getDataByCondition(3);
        $customer = null;
        $flagRegisterHistory = false;
        //load form by update
        if($id != null){
            $customer = MCustomers::find( $id );
            if(empty($customer)){
                abort('404');
            }else{
                $customerLast = MCustomers::where('mst_customers_cd', '=', $customer->mst_customers_cd)
                    ->orderByDesc("adhibition_start_dt")->first();
                if($customerLast->id == $id){
                    $flagRegisterHistory = true;
                }
                $customer = $customer->toArray();
            }
        }
        return view('customers.form', [
            'customer' => $customer,
            'flagRegisterHistory' => $flagRegisterHistory,
            'listPrefecture' => $listPrefecture,
            'customer_categories'=>$customer_categories,
            'business_offices'=>$mBusinessOffices,
            'listDepositMethods'=>$listDepositMethods,
            'listDepositMonths'=>$listDepositMonths,
            'listConsumptionTaxCalcUnit'=>$listConsumptionTaxCalcUnit,
            'listRoundingMethod'=>$listRoundingMethod,
            'listAccountTitles'=>$listAccountTitles,
            'role' => count($role)<=0 ? 9 : $role[0]->accessible_kb,
        ]);
    }

    protected function beforeSubmit($data){
        if(isset($data["id"]) && $data["id"]) {
            if (!isset($data["clone"])) {
                $this->ruleValid['adhibition_start_dt_edit'] = 'required';
                $this->ruleValid['adhibition_end_dt_edit'] = 'required';
            }else{
                $this->ruleValid['adhibition_start_dt_history'] = 'required';
                $this->ruleValid['adhibition_end_dt_history'] = 'required';
            }
        }
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
        if (Carbon::parse($data['adhibition_start_dt']) > Carbon::parse(config('params.adhibition_end_dt_default'))) {
            $validator->errors()->add('adhibition_start_dt',str_replace(' :attribute',$this->labels['adhibition_start_dt'],Lang::get('messages.MSG02014')));
        }

        if (isset($data['mst_customers_cd']) && !empty($data['mst_customers_cd'])){
            $strWhereStartDate = 'DATE_FORMAT("'.$data['adhibition_start_dt'].'", "%Y%m%d")';
            $strWhereEndDate = 'DATE_FORMAT("'.$data['adhibition_end_dt'].'", "%Y%m%d")';
            $strWhereStartDateDB = 'DATE_FORMAT(adhibition_start_dt, "%Y%m%d")';
            $strWhereEndDateDB = 'DATE_FORMAT(adhibition_end_dt, "%Y%m%d")';
            $strWhere = $strWhereStartDate." > ".$strWhereEndDateDB." or ".$strWhereEndDate." < ".$strWhereStartDateDB;
            $countExist = MCustomers::query()
                ->where('mst_customers_cd','=',$data['mst_customers_cd'])
                ->whereNull("deleted_at")
                ->whereRaw("!(".$strWhere.")");

            if(isset($data["id"]) && $data["id"]){
                if(!isset($data["clone"])){
                    if (Carbon::parse($data['adhibition_start_dt_edit']) > Carbon::parse($data['adhibition_end_dt_edit'])) {
                        $validator->errors()->add('adhibition_start_dt_edit',str_replace(' :attribute',$this->labels['adhibition_start_dt_edit'],Lang::get('messages.MSG02014')));
                    }
                    $beforeItem = MCustomers::query()
                        ->whereRaw($strWhereStartDateDB." < ".$strWhereStartDate)
                        ->whereNull("deleted_at")
                        ->where('mst_customers_cd','=',$data['mst_customers_cd'])
                        ->where("id","<>",$data["id"])
                        ->orderByDesc("adhibition_start_dt")
                        ->first();
                    if($beforeItem){
                        $this->beforeItem = $beforeItem;
                        $countExist = $countExist->where("id","<>",$beforeItem->id);
                    }
                }else{
                    if (Carbon::parse($data['adhibition_start_dt_history']) > Carbon::parse($data['adhibition_end_dt_history'])) {
                        $validator->errors()->add('adhibition_start_dt_history',str_replace(' :attribute',$this->labels['adhibition_start_dt_history'],Lang::get('messages.MSG02014')));
                    }
                    $mCustomer = MCustomers::find($data['id']);
                    if (Carbon::parse($data['adhibition_start_dt_history']) <= Carbon::parse($mCustomer->adhibition_start_dt)){
                        $validator->errors()->add('mst_customers_cd', str_replace(':screen', '得意先', Lang::get('messages.MSG10003')));
                    }
                }
                $countExist = $countExist->where("id","<>",$data["id"]);
            }
            if(!isset($data["id"]) || (isset($data['id']) && !isset($data["clone"]) )) {
                $countExist = $countExist->count();
                if ($countExist > 0) {
                    $validator->errors()->add('mst_customers_cd', str_replace(':screen', '得意先', Lang::get('messages.MSG10003')));
                }
            }
        }
    }

    protected function save($data){
        $arrayInsert = $data;
        $currentTime = date("Y-m-d H:i:s",time());
        $mst_bill_issue_destinations =  $data["mst_bill_issue_destinations"];
        unset($arrayInsert["mst_bill_issue_destinations"]);
        unset($arrayInsert["adhibition_start_dt_edit"]);
        unset($arrayInsert["adhibition_end_dt_edit"]);
        unset($arrayInsert["adhibition_start_dt_history"]);
        unset($arrayInsert["adhibition_end_dt_history"]);
        unset($arrayInsert["id"]);
        unset($arrayInsert["clone"]);
        DB::beginTransaction();
        if(isset($arrayInsert["except_g_drive_bill_fg"]) && $arrayInsert["except_g_drive_bill_fg"] == true){
            $arrayInsert["except_g_drive_bill_fg"] = 1;
        }else{
            $arrayInsert["except_g_drive_bill_fg"] = 0;
        }
        if(isset( $data["id"]) && $data["id"] && !isset($data["clone"]) ){
            $id = $data["id"];
            $arrayInsert["modified_at"] = $currentTime;
            MCustomers::query()->where("id","=",$id)->update( $arrayInsert );
            if($this->beforeItem){
                MCustomers::query()->where("id","=",$this->beforeItem["id"])->update([
                    "adhibition_end_dt" => date_create($arrayInsert["adhibition_start_dt"])->modify('-1 days')->format('Y-m-d'),
                    "modified_at" => $currentTime
                ]);
            }
        }else {
            $arrayInsert["created_at"] = $currentTime;
            $arrayInsert["modified_at"] = $currentTime;
            $id =  MCustomers::query()->insertGetId( $arrayInsert );
            if(isset($data["clone"])){
                MCustomers::query()->where("id","=",$data["id"])->update([
                    "adhibition_end_dt" => date_create($arrayInsert["adhibition_start_dt"])->modify('-1 days')->format('Y-m-d'),
                    "modified_at" => $currentTime
                ]);
            }
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
                if(isset($bill_issue_destination["id"]) && $bill_issue_destination["id"] && !isset($data["clone"])){
                    $arrayInsertBill["modified_at"] = $currentTime;
                    MMstBillIssueDestinations::query()
                        ->where("id","=",$bill_issue_destination["id"])->update($arrayInsertBill);
                    $arrayIDInsert[] = $flagUpdate = $bill_issue_destination["id"];
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
            $deleteBill->delete();
        }
        DB::commit();
        if(isset( $data["id"]) || isset($data["clone"])){
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
            ->orderBy(DB::raw("disp_number*1"))
            ->get();
        if($listBills){
            $listBills->toArray();
        }
        return Response()->json(array('success'=>true, 'data'=> $listBills));
    }
}
