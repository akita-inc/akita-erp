<?php
/**
 * Created by PhpStorm.
 * User: ptson
 * Date: 04/09/2019
 * Time: 9:29 AM
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;

class MModifyLogs extends Model
{
    protected $table = "modify_logs";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = Null;

    public function writeLogWithTable( $table,$dataBeforeUpdate,$dataAfterUpdate,$table_id ){
        $dataBeforeUpdate = (Array) $dataBeforeUpdate;
        foreach ($dataAfterUpdate as $key=>$value){
            if( ( (!isset($dataBeforeUpdate[$key]) && $value != null) || isset($dataBeforeUpdate[$key]) && $dataBeforeUpdate[$key] != $value) ){
                $log = new MModifyLogs();
                $log->table_name = $table;
                $log->table_id = $table_id;
                $log->column_name = $key;
                if(is_null($dataBeforeUpdate[$key])){
                    $log->before_data = DB::raw("Null");
                }else{
                    $log->before_data = $dataBeforeUpdate[$key];
                }

                if(is_null($value)){
                    $log->after_data = DB::raw("Null");
                }else{
                    $log->after_data = $value;
                }

                $log->mst_staff_id = Auth::user()->id;
                $log->save();
            }
        }
    }
}
