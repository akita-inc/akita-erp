<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MWfType extends Model {
    use SoftDeletes;

    protected $table = "wf_type";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [];
}