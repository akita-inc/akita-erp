<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MVehicles extends Model
{
    use SoftDeletes;

    protected $table = "mst_vehicles";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];

    public function getHistoryNearest($vehicles_cd, $adhibition_end_dt) {
        $vehicles = new MVehicles();
        $vehicles = $vehicles->where('vehicles_cd', '=', $vehicles_cd)
                                ->where("adhibition_end_dt", "<", $adhibition_end_dt)
                                ->orderByDesc("adhibition_end_dt");
        return $vehicles->first();
    }

    public function deleteVehicle($id){
        $mVehicle = new MVehicles();
        $mVehicle = $mVehicle->find($id);

        DB::beginTransaction();
        try
        {
            $historyVihicle = $this->getHistoryNearest($mVehicle->vehicles_cd, $mVehicle->adhibition_end_dt);
            if (isset($historyVihicle)) {
                $historyVihicle->adhibition_end_dt = $mVehicle->adhibition_end_dt;
                $historyVihicle->save();
            }
            $mVehicle->delete();
            DB::commit();
            return true;
        } catch (\Exception $ex){
            DB::rollBack();
            return false;
        }
    }

}
