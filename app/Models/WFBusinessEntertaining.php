<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WFBusinessEntertaining extends Model {

    protected $table = "wf_business_entertaining";

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'modified_at';
    const DELETED_AT = 'delete_at';

    public $label = [];
}