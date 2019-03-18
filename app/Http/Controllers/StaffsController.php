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
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class StaffsController extends Controller
{
    use ListTrait, FormTrait,StaffTrait;
    public $table = "mst_staffs";
    public $ruleValid = [
        'staff_cd'  => 'required|one_bytes_string|length:5',
        'adhibition_start_dt'  => 'required',
        'password'=>'required|length:50',
        'last_nm'  => 'nullable|length:25',
        'last_nm_kana'  => 'kana|nullable|length:25',
        'first_nm'  => 'length:50|nullable',
        'first_nm_kana'=>'kana|nullable|length:50',
        'zip_cd'=>'one_bytes_string|length:7',
        'address1'=>'length:20|nullable',
        'address2'=>'length:20|nullable',
        'address3'=>'length:50|nullable',
        "landline_phone_number"=>"length:20|nullable",
        "cellular_phone_number"=>"length:20|nullable",
        "corp_cellular_phone_number"=>"length:20|nullable",
        "notes"=>"length:50|nullable",
        "insurer_number"=>"length:3|nullable",
        "health_insurance_class"=>"integer|nullable",
        "welfare_annuity_class"=>"integer|nullable",
        "basic_pension_number"=>"length:11|nullable",
        "person_insured_number"=>"length:11|nullable",
        "educational_background"=>"length:50|nullable",
        "retire_reasons"=>"length:50|nullable",
        "death_reasons"=>"length:50|nullable",
        "employment_insurance_numbers"=>"length:20|nullable",
        "health_insurance_numbers"=>"length:20|nullable",
        "employees_pension_insurance_numbers"=>"length:10|nullable",
        "drivers_license_number"=>"length:12|nullable",
        'drivers_license_picture' => 'nullable|is_image'
    ];
    public function __construct(){
        $this->labels = Lang::get("staffs.create.field");
    }

    protected function search($data)
    {
        $where = array(
            'staff_cd' => $data['fieldSearch']['staff_cd'],
            'staff_nm' => $data['fieldSearch']['staff_nm'],
            'reference_date' => date('Y-m-d', strtotime($data['fieldSearch']['reference_date'])),
            'status' => $data['fieldSearch']['status'],
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
            DB::raw('CONCAT(mst_staffs.last_nm,"    ",mst_staffs.first_nm) as staff_nm'),
            DB::raw('CONCAT(mst_staffs.last_nm_kana,"   ",mst_staffs.first_nm_kana) as staff_nm_kana'),
            'position.date_nm as position_nm',
            'belong_company.date_nm as belong_company_nm',
            'mst_business_offices.business_office_nm',
            DB::raw("DATE_FORMAT(mst_staffs.adhibition_start_dt, '%Y/%m/%d') as adhibition_start_dt"),
            DB::raw("DATE_FORMAT(mst_staffs.adhibition_end_dt, '%Y/%m/%d') as adhibition_end_dt"),
            DB::raw("DATE_FORMAT(mst_staffs.modified_at, '%Y/%m/%d') as modified_at"),
            DB::raw("DATE_FORMAT(sub.max_adhibition_end_dt, '%Y/%m/%d') as max_adhibition_end_dt")
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
        })->leftjoin(DB::raw('(select staff_cd, max(adhibition_end_dt) AS max_adhibition_end_dt from mst_staffs where deleted_at IS NULL group by staff_cd) sub'), function ($join) {
            $join->on('sub.staff_cd', '=', 'mst_staffs.staff_cd');
        });
        $this->query->whereRaw('mst_staffs.deleted_at IS NULL');
        $this->query->where('mst_staffs.staff_cd', 'LIKE', '%' . $where['staff_cd'] . '%')
                    ->where( DB::raw('CONCAT(mst_staffs.last_nm,mst_staffs.first_nm)'), 'LIKE', '%'.$where['staff_nm'].'%');
        $this->queryDataKb('employment_pattern_id',$where['employment_pattern_id']);
        $this->queryDataKb('position_id',$where['position_id']);
        $this->queryDataKb('belong_company_id',$where['belong_company_id']);
        $this->queryDataKb('mst_business_office_id', $where['mst_business_office_id']);
        if ($where['status'] == '1' && $where['reference_date'] != null) {
            $this->query->where('mst_staffs.adhibition_start_dt','<=',$where['reference_date'])
                ->where('mst_staffs.adhibition_end_dt','>=',$where['reference_date']);
        }
        $this->query->orderby('mst_staffs.staff_cd')
                    ->orderby('mst_staffs.adhibition_start_dt');
    }

    public function index(Request $request)
    {
        $fieldShowTable = [
            'staff_cd' => [
                "classTH" => "wd-100"
            ],
            'employment_pattern_nm' => [
                "classTH" => ""
            ],
            'position_nm' => [
                "classTH" => ""
            ],
            'staff_nm' => [
                "classTH" => ""
            ],
            'belong_company_nm' => [
                "classTH" => ""
            ],
            'business_office_nm' => [
                "classTH" => ""
            ],
            'adhibition_start_dt' => [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'adhibition_end_dt' => [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'modified_at' => [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'delete' => [
                "classTH" => "wd-60",
                "classTD" => "no-padding"
            ]

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
        try {
            if ($mStaffs->deleteStaffs($id)) {
                $response = ['data' => 'success'];
            } else {
                $response = ['data' => 'failed', 'msg' => Lang::get('messages.MSG06002')];
            }

        } catch (\Exception $ex) {
            $response = ['data' => $ex];
        }
        return response()->json($response);
    }

    public function checkIsExist($id){
        $mStaffs = new MStaffs();
        $mStaffs = $mStaffs->find($id);
        if (isset($mStaffs)) {
            return Response()->json(array('success'=>true));
        } else {
            return Response()->json(array('success'=>false, 'msg'=> Lang::trans('messages.MSG06003')));
        }
    }
    protected function validAfter( &$validator,$data ){
        //running in ValidateStaffTrait
        $this->validateBlockCollapse($validator,"mst_staff_job_experiences",$data,[
            'job_duties' => 'nullable|length:50'
        ]);
        $this->validateBlockCollapse($validator,"mst_staff_qualifications",$data,[
            'qualifications_notes' => 'nullable|length:100',
            'amounts'=>'nullable|integer'
        ]);
        $this->validateBlockCollapse($validator,"mst_staff_dependents",$data,[
            'dept_last_nm' => 'nullable|length:25',
            'dept_last_nm_kana' => 'nullable|length:50',
            'dept_first_nm' => 'nullable|length:25',
            'dept_first_nm_kana' => 'nullable|length:50',
            'dept_social_security_number'=>'nullable|length:10'
        ]);
        if (Carbon::parse($data['adhibition_start_dt']) > Carbon::parse(config('params.adhibition_end_dt_default'))) {
            $validator->errors()->add('adhibition_start_dt',str_replace(' :attribute',$this->labels['adhibition_start_dt'],Lang::get('messages.MSG02014')));
        }
        if (Carbon::parse($data['retire_dt']) > Carbon::parse($data['death_dt'])) {
            $validator->errors()->add('retire_dt', str_replace(' :attribute', $this->labels['retire_dt'], Lang::get('messages.MSG02014')));
        }
    }
    protected function uploadFile($file,$path)
    {
        if(isset($file))
        {
            $exploded=explode(",",$file);
            $decoded=base64_decode($exploded[1]);
            if(str_contains($exploded[0],'jpeg'))
            {
                $extension='jpg';
            }
            else
            {
                $extension='png';
            }
            $fileName=str_random().'.'.$extension;
            $filePath=$path.$fileName;
            file_put_contents($filePath,$decoded);
            return $fileName;
        }
        return "";
    }

    protected function save($data){
        $data['password']=bcrypt($data['password']);
        $data['admin_fg']=isset($data['admin_fg'])?1:0;
        $data['workmens_compensation_insurance_fg']=isset($data['workmens_compensation_insurance_fg'])?1:0;
        $data["drivers_license_picture"]=$this->uploadFile($data["drivers_license_picture"],config('params.staff_path'));
        $arrayInsert = $data;
        $mst_staff_job_experiences =  $data["mst_staff_job_experiences"];
        $mst_staff_qualifications=$data["mst_staff_qualifications"];
        $mst_staff_dependents=$data["mst_staff_dependents"];
        $mst_staff_auths=$data["mst_staff_auths"];
        DB::beginTransaction();
        unset($arrayInsert["mst_staff_job_experiences"]);
        unset($arrayInsert["dropdown_relocate_municipal_office_nm"]);//
        unset($arrayInsert["mst_staff_qualifications"]);
        unset($arrayInsert["mst_staff_dependents"]);
        unset($arrayInsert["mst_staff_auths"]);
        $id = DB::table($this->table)->insertGetId( $arrayInsert );
        $this->saveStaffAuth($id,$mst_staff_auths);
        $this->saveBlock($id,$mst_staff_job_experiences,"mst_staff_job_experiences");
        $this->saveBlock($id,$mst_staff_qualifications,"mst_staff_qualifications","qualifications_");
        $this->saveBlock($id,$mst_staff_dependents,"mst_staff_dependents","dept_",["disp_number"]);
        DB::commit();
        \Session::flash('message',Lang::get('messages.MSG03002'));
        return $id;
    }


    public function create(Request $request)
    {
        $mGeneralPurposes = new MGeneralPurposes();
        $mBusinessOffices=new MBusinessOffices();
        $mRoles = new MRoles();
        $mScreen = new MScreens();
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

        return view('staffs.create', [
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
        ]);
    }

}
