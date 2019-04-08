<?php
/**
 * Created by PhpStorm.
 * User: trinhhtm
 * Date: 5/5/2017
 * Time: 3:22 PM
 */

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
class MStaffs extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'mst_staffs';
    protected $fillable = ['id','staff_cd','password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';
    public $rules = [

    ];

    public function getHistoryNearest($staff_cd, $adhibition_end_dt) {
        $mMstaffs = new MStaffs();
        $mMstaffs = $mMstaffs->where('staff_cd', '=', $staff_cd)
            ->where("adhibition_end_dt", "<", $adhibition_end_dt)
            ->orderByDesc("adhibition_end_dt");
        return $mMstaffs->first();
    }

    public function deleteStaffs($id){
        $currentTime = date("Y-m-d H:i:s",time());
        $mMStaffs = new MStaffs();
        $mMStaffs = $mMStaffs->find($id);
        DB::beginTransaction();
        try
        {
            $this->deleteAccordions($id,"mst_staff_job_experiences",$currentTime);
            $this->deleteAccordions($id,"mst_staff_qualifications",$currentTime);
            $this->deleteAccordions($id,"mst_staff_dependents",$currentTime);
            $this->deleteStaffAuth($id,$currentTime);
            $mMStaffs->delete();
            DB::commit();
            return true;
        } catch (\Exception $ex){
            DB::rollBack();
            return false;
        }
    }
    protected function deleteAccordions($id,$name,$currentTime)
    {
        try {
            DB::table($name)
                    ->where("mst_staff_id", $id)
                    ->update(['deleted_at' => $currentTime]);
                return true;
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return false;
        }
    }
    protected function deleteStaffAuth($id, $currentTime)
    {
        try {
            DB::table("mst_staff_auths")
                ->where("mst_staff_id", $id)
                ->update(['deleted_at' => $currentTime]);
            return true;
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return false;
        }
    }
    public $label = array(
        "id" => "ID",
        "staff_cd" => "ログインID",
        "password" => "ログインPW",
    );

    public function getListOption($adhibition_start_dt){
        $data =  $this
            ->select(DB::raw("staff_cd as value, concat(staff_cd,'：',COALESCE(last_nm,''),'　',COALESCE(first_nm,'')) as text"))
            ->where('deleted_at','=',null)
            ->where('mst_role_id','=',1)
            ->where('adhibition_start_dt','<=',$adhibition_start_dt)
            ->where('adhibition_end_dt','>=',$adhibition_start_dt)
            ->orderBy('last_nm_kana', 'ASC')
            ->orderBy('first_nm_kana', 'ASC')
            ->orderBy('staff_cd', 'ASC')
            ->get();
        if($data){
            $data = $data->toArray();
            array_unshift($data,array('value' => '','text' => '==選択=='));
        }
        return $data;
    }

    public function checkVaildStaffCd($adhibition_start_dt, $staff_cd){
        $data =  $this
            ->where('deleted_at','=',null)
            ->where('staff_cd','=',$staff_cd)
            ->where('mst_role_id','=',1)
            ->where('adhibition_start_dt','<=',$adhibition_start_dt)
            ->where('adhibition_end_dt','>=',$adhibition_start_dt)
            ->count();
        return $data;
    }
}
