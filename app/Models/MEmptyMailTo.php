<?php

namespace App\Models;

use App\Helpers\TimeFunction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MEmptyMailTo extends Model
{
    use SoftDeletes;

    protected $table = "empty_mail_to";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [

    ];

    public $rules = [

    ];

}
