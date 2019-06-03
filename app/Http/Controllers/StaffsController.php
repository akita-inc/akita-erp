<?php
namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Http\Controllers\TraitRepositories\StaffTrait;
use App\Models\MBusinessOffices;
use App\Models\MRoles;
use App\Models\MScreens;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MStaffs;
use App\Models\MGeneralPurposes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\MStaffAuths;
use App\Models\MModifyLogs;
class StaffsController extends Controller
{
    use ListTrait, FormTrait,StaffTrait;
    public $table = "mst_staffs";
    public $beforeItem = null;
    public $password=null;

    public $ruleValid = [
        'staff_cd'  => 'required|one_bytes_string|length:5',
        'last_nm'  => 'nullable|length:25',
        'last_nm_kana'  => 'kana|nullable|length:50',
        'first_nm'  => 'length:25|nullable',
        'first_nm_kana'=>'kana|nullable|length:50',
        'zip_cd'=>'zip_code|nullable|length:7',
        'mail'=>'length:255|nullable|email_format|email_character',
        'address1'=>'length:20|nullable',
        'address2'=>'length:20|nullable',
        'address3'=>'length:50|nullable',
        "landline_phone_number"=>"length:20|nullable|phone_number",
        "cellular_phone_number"=>"length:20|nullable|phone_number",
        "corp_cellular_phone_number"=>"length:20|nullable|phone_number",
        "notes"=>"length:50|nullable",
        "insurer_number"=>"length:3|nullable",
        "health_insurance_class"=>"one_byte_number|length:10|number_range|nullable",
        "welfare_annuity_class"=>"one_byte_number|length:10|number_range|nullable",
        "relocation_municipal_office_cd"=>"nullable|length:6",
        "basic_pension_number"=>"length:11|nullable",
        "person_insured_number"=>"length:11|nullable",
        "educational_background"=>"length:50|nullable",
        "retire_reasons"=>"length:50|nullable",
        "death_reasons"=>"length:50|nullable",
        "employment_insurance_numbers"=>"length:20|nullable",
        "employees_pension_insurance_numbers"=>"length:10|nullable",
        "drivers_license_number"=>"length:12|nullable",
        "section_id"=>'required',
        "belong_company_id"=>'required',
        "mst_business_office_id"=>'required'
    ];
    public $messagesCustom =[];
    public function __construct(){
        $this->labels = Lang::get("staffs.create.field");
        $this->ruleValid['drivers_license_picture'] = 'nullable|mimes:jpeg,jpg,png|max_mb:'.config("params.max_file_size");
        $this->messagesCustom["drivers_license_picture.mimes"]= Lang::get('messages.MSG02018');
        $this->messagesCustom["password.required"]=Lang::get('messages.MSG02001');
        parent::__construct();

    }

    protected function search($data)
    {
        $where = array(
            'staff_cd' => $data['fieldSearch']['staff_cd'],
            'staff_nm' => $data['fieldSearch']['staff_nm'],
            'employment_pattern_id'=>$data['fieldSearch']['employment_pattern_id'],
            'position_id'=>$data['fieldSearch']['position_id'],
            'belong_company_id'=>$data['fieldSearch']['belong_company_id'],
            'mst_business_office_id'=>$data['fieldSearch']['mst_business_office_id'],
        );
        $this->query->select(
            'mst_staffs.id',
            'mst_staffs.staff_cd',
            'employment_pattern.date_id',
            'employment_pattern.date_nm as employment_pattern_nm',
            DB::raw('CONCAT_WS("    ",mst_staffs.last_nm,mst_staffs.first_nm) as staff_nm'),
            DB::raw('CONCAT_WS("    ",mst_staffs.last_nm_kana,mst_staffs.first_nm_kana) as staff_nm_kana'),
            'position.date_nm as position_nm',
            'belong_company.date_nm as belong_company_nm',
            'mst_business_offices.business_office_nm',
            DB::raw("DATE_FORMAT(mst_staffs.modified_at, '%Y/%m/%d') as modified_at")
        );
        $this->query->leftJoin('mst_general_purposes as employment_pattern', function ($join) {
            $join->on('employment_pattern.date_id', '=', 'mst_staffs.employment_pattern_id')
                ->where('employment_pattern.data_kb', config('params.data_kb')['employment_pattern']);
        })->leftJoin('mst_general_purposes as position', function ($join) {
            $join->on('position.date_id', '=', 'mst_staffs.position_id')
                ->where('position.data_kb', config('params.data_kb')['position']);;
        })->leftJoin('mst_general_purposes as belong_company', function ($join) {
            $join->on('belong_company.date_id', '=', 'mst_staffs.belong_company_id')
                ->where('belong_company.data_kb', config('params.data_kb')['belong_company']);;
        })->leftJoin('mst_business_offices', function ($join) {
            $join->on('mst_business_offices.id', '=', 'mst_staffs.mst_business_office_id');
        });
        $this->query->whereRaw('mst_staffs.deleted_at IS NULL');

        $this->queryDataKb('employment_pattern_id',$where['employment_pattern_id']);
        $this->queryDataKb('position_id',$where['position_id']);
        $this->queryDataKb('belong_company_id',$where['belong_company_id']);
        $this->queryDataKb('mst_business_office_id', $where['mst_business_office_id']);
        if ($where['staff_cd'] != '') {
            $this->query->where('mst_staffs.staff_cd', 'LIKE', '%' . $where['staff_cd'] . '%');
        }
        if ($where['staff_nm'] != '') {
            $this->query->where( DB::raw('CONCAT(mst_staffs.last_nm,mst_staffs.first_nm)'), 'LIKE', '%'.$where['staff_nm'].'%');
        }

        if ($data["order"]["col"] != '') {
            if ($data["order"]["col"] == 'staff_nm_kana')
                $orderCol = 'CONCAT_WS("    ",mst_staffs.last_nm_kana,mst_staffs.first_nm_kana)';
            else
                $orderCol = $data["order"]["col"];
            if (isset($data["order"]["descFlg"]) && $data["order"]["descFlg"]) {
                $orderCol .= " DESC";
            }
            $this->query->orderbyRaw($orderCol);
        } else {
            $this->query->orderby('mst_staffs.staff_cd');
        }
    }

    public function index(Request $request)
    {
        $fieldShowTable = [
            'staff_cd' => [
                "classTH" => "wd-120",
                "sortBy" => "staff_cd"
            ],
            'employment_pattern_nm' => [
                "classTH" => "min-wd-100",
                "sortBy" => "employment_pattern.date_nm"
            ],
            'position_nm' => [
                "classTH" => "min-wd-100",
                "sortBy" => "position.date_nm"
            ],
            'staff_nm' => [
                "classTH" => "",
                "sortBy" => "staff_nm_kana"
            ],
            'belong_company_nm' => [
                "classTH" => "min-wd-100",
                "sortBy" => "belong_company.date_nm"
            ],
            'business_office_nm' => [
                "classTH" => "min-wd-100",
                "sortBy" => "business_office_nm"
            ],
            'modified_at' => [
                "classTH" => "wd-120",
                "classTD" => "text-center",
                "sortBy" => "modified_at"
            ],

        ];
        $mGeneralPurpose = new MGeneralPurposes();
        $mBussinessOffice = new MBusinessOffices();
        $employmentPatterns = $mGeneralPurpose->getDataByMngDiv(config('params.data_kb')['employment_pattern']);
        $positions = $mGeneralPurpose->getDataByMngDiv(config('params.data_kb')['position']);
        $belongCompanies = $mGeneralPurpose->getDataByMngDiv(config('params.data_kb')['belong_company']);
        $businessOffices = $mBussinessOffice->getAllData();
        return view('staffs.index', array(
            'fieldShowTable' => $fieldShowTable,
            'belongCompanies' => $belongCompanies,
            'employmentPatterns' => $employmentPatterns,
            'positions' => $positions,
            'businessOffices' => $businessOffices
        ));
    }

    public function delete($id)
    {
        $mStaffs = new MStaffs();
        $this->backHistory();
        try {
            if($id==Auth::user()->id)
            {
                \Session::flash('error',Lang::get('messages.MSG06005'));
                $response = ['data' => 'failed', 'msg' => Lang::get('messages.MSG06005')];
            }
            else
            {
                if ($mStaffs->deleteStaffs($id)) {
                    \Session::flash('message',Lang::get('messages.MSG10004'));
                    $response = ['data' => 'success'];
                } else {
                    \Session::flash('error',Lang::get('messages.MSG06002'));
                    $response = ['data' => 'failed', 'msg' => Lang::get('messages.MSG06002')];
                }
            }
        } catch (\Exception $ex) {
            $response = ['data' => $ex];
        }
        return response()->json($response);
    }
    protected function addLogModify( $dataBeforeUpdate,$data ){
        unset($data["mst_staff_job_experiences"]);
        unset($data["dropdown_relocate_municipal_office_nm"]);//
        unset($data["mst_staff_qualifications"]);
        unset($data["mst_staff_dependents"]);
        unset($data["mst_staff_auths"]);
        unset($data["drivers_license_picture"]);
        unset($data["deleteFile"]);
        unset($data["is_change_password"]);
        unset($data["is_change_password_confirm"]);
        unset($data["confirm_password"]);
        if(isset($data["password"]) && $data["password"]=="********" )
        {
            unset($data["password"]);
        }
        else
        {
            $data["password"]=$this->password;
        }
        $modifyLog = new MModifyLogs();
        $modifyLog->writeLogWithTable( $this->table,$dataBeforeUpdate,$data,$data["id"] );
    }
    protected function validAfter( &$validator,$data ){
        $this->validateBlockCollapse($validator,"mst_staff_job_experiences",$data,[
            'job_duties' => 'nullable|length:50'
        ]);
        $this->validateBlockCollapse($validator,"mst_staff_qualifications",$data,[
            'qualifications_notes' => 'nullable|length:100',
            'amounts'=>'nullable|one_byte_number|length:10|number_range'
        ]);
        $this->validateBlockCollapse($validator,"mst_staff_dependents",$data,[
            'dept_last_nm' => 'nullable|length:25',
            'dept_last_nm_kana' => 'kana|nullable|length:50',
            'dept_first_nm' => 'nullable|length:25',
            'dept_first_nm_kana' => 'kana|nullable|length:50',
            'dept_social_security_number'=>'nullable|length:10'
        ]);
        $staffScreen=$data["mst_staff_auths"][1]["staffScreen"];
        if($data["mst_staff_auths"][1]["accessible_kb"]==1 && !in_array("1",$staffScreen))
        {
            $validator->errors()->add('staffScreen',str_replace(' :attribute',"基本情報",Lang::get('messages.MSG10007'))
);
        }
        $this->validateExistBeforeItem($validator,$data);
    }
    protected function validateExistBeforeItem(&$validator,$data)
    {
        if(!isset($data["id"])){
            $staffExits =MStaffs::query()
                ->whereNull("deleted_at")
                ->where('staff_cd', '=', $data['staff_cd'])
                ->first();
            if($staffExits){
                $validator->errors()->add('staff_cd',str_replace(':screen','社員',Lang::get('messages.MSG10003')));
            }
        }
        if (isset($data["id"]) && $data["id"]) {
            $passwordStaff = MStaffs::select("password")->where("id","=",$data["id"])->first();
            if(Hash::check($data['confirm_password'], $passwordStaff->password)==false && (!isset($data["is_change_password"]) || $data["is_change_password"] == false) && (!empty($data['is_change_password_confirm']) || $data['is_change_password_confirm']==true))
            {
                $validator->errors()->add('confirm_password', Lang::get('messages.MSG02022'));
            }
        }

        if((isset($data["is_change_password"]) && $data["is_change_password"] == true) || !isset($data["id"])) {
            if ($data['password'] != $data['confirm_password'] && (isset($data['confirm_password']) || $data['confirm_password'])) {
                $validator->errors()->add('confirm_password', Lang::get('messages.MSG02022'));
            }
        }
    }
    protected function save($data){
        $mStaffAuth =  new MStaffAuths();
        $rolesStaffScreen=$mStaffAuth->getDataScreenStaffAuth();
        if((isset($data["is_change_password"]) && $data["is_change_password"] == true) || !isset($data["id"])) {
            $this->password= bcrypt($data['password']);
            $data['password'] = $this->password;
        }else{
            $passwordStaff = MStaffs::select("password")->where("id","=",$data["id"])->first();
            $data['password'] = $passwordStaff->password;
        }
        $arrayInsert = $data;
        $currentTime = date("Y-m-d H:i:s",time());
        $mst_staff_auths=$data["mst_staff_auths"];
        $drivers_license_picture=$data["drivers_license_picture"];
        $deleteFile=$data["deleteFile"];
        unset($arrayInsert["id"]);
        unset($arrayInsert["mst_staff_job_experiences"]);
        unset($arrayInsert["dropdown_relocate_municipal_office_nm"]);//
        unset($arrayInsert["mst_staff_qualifications"]);
        unset($arrayInsert["mst_staff_dependents"]);
        unset($arrayInsert["mst_staff_auths"]);
        unset($arrayInsert["drivers_license_picture"]);
        unset($arrayInsert["deleteFile"]);
        unset($arrayInsert["is_change_password"]);
        unset($arrayInsert["is_change_password_confirm"]);
        unset($arrayInsert["confirm_password"]);
        DB::beginTransaction();
        try{
            $modeEdit=false;
            if(isset( $data["id"]) && $data["id"]){
                $modeEdit=true;
                $id = $data["id"];
                $arrayInsert["modified_at"] = $currentTime;
                MStaffs::query()->where("id","=",$id)->update( $arrayInsert );//MODE UPDATE SUBMIT
            }else {
                $arrayInsert["modified_at"] = $currentTime;
                $arrayInsert["created_at"]=$currentTime;
                $id = DB::table($this->table)->insertGetId( $arrayInsert );
            }
            $this->deleteFile($id,$deleteFile);
            $this->uploadFile($id,$drivers_license_picture,config('params.staff_path'),$modeEdit);
//            $this->saveStaffAuth($id,$mst_staff_auths, $currentTime);
            $this->saveAccordion($id,$data,"mst_staff_job_experiences", null, [], $currentTime);
            $this->saveAccordion($id,$data,"mst_staff_qualifications","qualifications_", [], $currentTime);
            $this->saveAccordion($id,$data,"mst_staff_dependents","dept_",["disp_number"], $currentTime);
            DB::commit();
            if(isset( $data["id"])){
                $this->backHistory();
            }
            if(isset( $data["id"]) && $data["id"]){
                \Session::flash('message',Lang::get('messages.MSG04002'));
            }else{
                \Session::flash('message',Lang::get('messages.MSG03002'));
            }
            return $id;
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            dd($e);
            return false;
        }


    }
    protected function beforeSubmit($data){
        if((isset($data["is_change_password"]) && $data["is_change_password"] == true) || !isset($data["id"])){
            $this->ruleValid["password"]='required|length:50';
            $this->ruleValid["confirm_password"]='required|length:50';
        }
    }
    public function store(Request $request,$id=null)
    {
        $mGeneralPurposes = new MGeneralPurposes();
        $mBusinessOffices=new MBusinessOffices();
        $mRoles = new MRoles();
        $mScreen = new MScreens();
        $mStaffAuth =  new MStaffAuths();
        $role = $mStaffAuth->getRoleBySCreen(1);
        $rolesStaffScreen=$mStaffAuth->getDataScreenStaffAuth();
        $listEmployPattern = $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['employment_pattern'], '');
        $listPosition=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['position'], '');
        $listPrefecture= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['prefecture_cd'],'');
        $listSex=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['sex'],'');
        $listReMunicipalOffice=$mGeneralPurposes->getCodeByDataKB(config('params.data_kb')['relocation_municipal_office_cd'],'');
        $listQualificationKind=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['qualification_kind'],'');
        $listDependentKBs=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['dependent_kb'],'');
        $listBelongCompanies=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['belong_company'],'');
        $listOccupation=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['occupation'],'');
        $listDepartments=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['department'],'');
        $listMedicalCheckupInterval=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['medical_checkup_interval'],'');
        $mBusinessOffices=$mBusinessOffices->getListBusinessOffices();
        $listDriversLicenseDivisions=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['drivers_license_divisions_kb'],'');
        $listDriversLicenseColors=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['drivers_license_color'],'');
        $listRoles = $mRoles->getListRoles();
        $listStaffScreens = $mScreen->getListScreensByCondition(['screen_category_id' => 1]);
        $listAccessiblePermission=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['accessible_kb'],'Empty');
        $listApprovalLevels=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['wf_level'],'');
        $listSectionIds=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['wf_applicant_affiliation_classification'],'');
        $staff=null;
        //load form by update
        if($id != null){
            $staff = MStaffs::find( $id );
            if(empty($staff)){
                abort('404');
            }else{
                $staff = $staff->toArray();
            }
        }
        return view('staffs.form', [
            'staff'=>$staff,
            'listEmployPattern' => $listEmployPattern,
            'listPosition'=>$listPosition,
            'listPrefecture'=>$listPrefecture,
            'listSex'=>$listSex,
            'listReMunicipalOffice'=>$listReMunicipalOffice,
            'listQualificationKind'=>$listQualificationKind,
            'listDependentKBs'=>$listDependentKBs,
            'listRoles'=>$listRoles,
            'listStaffScreens'=>$listStaffScreens,
            'listAccessiblePermission'=>$listAccessiblePermission,
            'listBelongCompanies'=>$listBelongCompanies,
            'listOccupation'=>$listOccupation,
            'mBusinessOffices'=>$mBusinessOffices,
            'listDepartments'=>$listDepartments,
            'listDriversLicenseDivisions'=>$listDriversLicenseDivisions,
            'listDriversLicenseColors'=>$listDriversLicenseColors,
            'listMedicalCheckupInterval'=>$listMedicalCheckupInterval,
            'role'=>$role,
            'rolesStaffScreen'=>$rolesStaffScreen,
            'listApprovalLevels'=>$listApprovalLevels,
            'listSectionIds'=>$listSectionIds
        ]);
    }
}
