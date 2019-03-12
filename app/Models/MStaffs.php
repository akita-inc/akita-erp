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
        $mMStaffs = new MStaffs();
        $mMStaffs = $mMStaffs->find($id);

        DB::beginTransaction();
        try
        {
            $historyStaffs = $this->getHistoryNearest($mMStaffs->staff_cd, $mMStaffs->adhibition_end_dt);
            if (isset($historyStaffs)) {
                $historyStaffs->adhibition_end_dt = $mMStaffs->adhibition_end_dt;
                $historyStaffs->save();
            }
            $mMStaffs->delete();
            DB::commit();
            return true;
        } catch (\Exception $ex){
            DB::rollBack();
            return false;
        }
    }
    public $label = array(
        "id" => "ID",
        "staff_cd" => "ログインID",
        "password" => "ログインPW",
    );

    public function getListOption($kDefault =''){
        $result = array($kDefault => '==選択==');
        $data =  $this->where('deleted_at','=',null)->where('admin_fg','=',1)
            ->get();
        foreach (json_decode(json_encode($data), true) as $key=>$item){
            $result[$item['id']] = $item['first_nm'].' '.$item['last_nm'];
        }
        return $result;
    }
}
