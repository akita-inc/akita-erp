<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TPaymentHistoryHeaderDetails extends Model {
    use SoftDeletes;

    protected $table = "t_payment_history_header_details";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];
}