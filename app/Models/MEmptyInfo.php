<?php

namespace App\Models;

use App\Helpers\TimeFunction;
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
            $mEmptyInfo->status = $status;
            switch ($status){
                case 1:
                    $mEmptyInfo->ask_date = null;
                    $mEmptyInfo->ask_office = null;
                    $mEmptyInfo->ask_staff = null;
                    break;
                case 2:
                case 8:
                    $mEmptyInfo->ask_date = TimeFunction::getTimestamp();
                    $mEmptyInfo->ask_office = Auth::user()->mst_business_office_id ;
                    $mEmptyInfo->ask_staff = Auth::user()->id;
                    break;
            }

            $mEmptyInfo->save();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }
        return true;
    }

}
