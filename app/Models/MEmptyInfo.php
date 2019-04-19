<?php

namespace App\Models;

use App\Helpers\TimeFunction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MEmptyInfo extends Model
{
    use SoftDeletes;

    protected $table = "empty_info";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [

    ];

    public $rules = [

    ];

    public static function updateStatus($id,$status){
        $mEmptyInfo = MEmptyInfo::find( $id );
        DB::beginTransaction();
        try {
            if($mEmptyInfo->status==2 && $mEmptyInfo->regist_office_id == Auth::user()->mst_business_office_id && $status==1 && Carbon::parse($mEmptyInfo->arrive_date) <= Carbon::now()){
                $status = 9;
            }
            $mEmptyInfo->status = $status;
            switch ($status){
                case 1:
                    $mEmptyInfo->ask_date = null;
                    $mEmptyInfo->ask_office = null;
                    $mEmptyInfo->ask_staff = null;
                    $mEmptyInfo->ask_staff_email_address = null;
                    break;
                case 2:
                    $empty_mail_add = MEmptyMailTo::where('office_id',Auth::user()->mst_business_office_id)->whereNull('deleted_at')->first();
                    $mEmptyInfo->ask_date = TimeFunction::getTimestamp();
                    $mEmptyInfo->ask_office = Auth::user()->mst_business_office_id ;
                    $mEmptyInfo->ask_staff = Auth::user()->staff_cd;
                    $mEmptyInfo->ask_staff_email_address = $empty_mail_add ?$empty_mail_add->email_address : null;
                    break;
                case 8:
                    $mEmptyInfo->apr_date = TimeFunction::getTimestamp();
                    $mEmptyInfo->apr_staff = Auth::user()->staff_cd;
                    break;
                case 9:
                    $mEmptyInfo->ask_date = null;
                    $mEmptyInfo->ask_office = null;
                    $mEmptyInfo->ask_staff = null;
                    $mEmptyInfo->ask_staff_email_address = null;
                    $mEmptyInfo->apr_date = null;
                    $mEmptyInfo->apr_staff = null;
                    break;
            }
            $mEmptyInfo->save();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
            return false;
        }
        return true;
    }

}
