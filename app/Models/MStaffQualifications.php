<?php
/**
 * Created by PhpStorm.
 * User: ptson
 * Date: 04/11/2019
 * Time: 1:05 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class MStaffQualifications extends Model
{
    use SoftDeletes;

    protected $table = "mst_staff_qualifications";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $excel_column = [];
}