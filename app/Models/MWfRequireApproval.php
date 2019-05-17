<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MWfRequireApproval extends Model {
    use SoftDeletes;

    protected $table = "mst_wf_require_approval";


    const CREATED_AT = 'create_at';
    const DELETED_AT = 'delete_at';

    public $label = [];
}