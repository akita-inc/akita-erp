<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MBusinessOffices  extends Model
{
    use SoftDeletes;

    protected $table = "mst_business_offices";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];
    public function getAllData()
    {
        return $this->where('deleted_at','=',null)
                    ->get();
    }

}
