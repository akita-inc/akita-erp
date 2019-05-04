<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class MSalesLists  extends Model
{
    protected $table = "t_saleses";

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
