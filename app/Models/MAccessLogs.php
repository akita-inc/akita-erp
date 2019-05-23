<?php
/**
 * Created by PhpStorm.
 * User: ptson
 * Date: 04/09/2019
 * Time: 9:29 AM
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;

class MAccessLogs extends Model
{
    protected $table = "accesslogs";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = Null;
}
