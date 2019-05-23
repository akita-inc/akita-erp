<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class MWfAdditionalNotice extends Model {

    protected $table = "wf_additional_notice";

    const CREATED_AT = 'create_at';
    const UPDATED_AT = null;
    const DELETED_AT = null;

    public $label = [];
}