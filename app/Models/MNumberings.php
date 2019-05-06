<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MNumberings extends Model {

    protected $table = "mst_numberings";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];

    public function getSerialNumberByTargetID($target_id){
        $currentNumber =  $this->select('serial_number')->where('mst_numbering_target_id',$target_id)->first();
        $nextNumber = $currentNumber->serial_number++;
        $this->where('mst_numbering_target_id',$target_id)->update(['serial_number' => $nextNumber]);
        return $currentNumber;
    }
}