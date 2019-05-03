<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MCustomers extends Model
{
    use SoftDeletes;

    protected $table = "mst_customers";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];

    public function getHistoryNearest($customer_cd, $adhibition_end_dt) {
        $suppliers = new MCustomers();
        $suppliers = $suppliers->where('mst_customers_cd', '=', $customer_cd)
            ->where("adhibition_end_dt", "<", $adhibition_end_dt)
            ->orderByDesc("adhibition_end_dt");
        return $suppliers->first();
    }

    public function deleteCustomer($id){
        $mCustomer = new MCustomers();
        $mCustomer = $mCustomer->find($id);

        DB::beginTransaction();
        try
        {
            $mCustomer->delete();
            DB::commit();
            return true;
        } catch (\Exception $ex){
            DB::rollBack();
            return false;
        }
    }
    public function getAllNm(){
        return $this->select('mst_customers_cd','customer_nm_formal')
            ->where('deleted_at','=',null)
            ->get();
    }
}
