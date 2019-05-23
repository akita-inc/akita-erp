<?php
/**
 * Created by PhpStorm.
 * User: ptson
 * Date: 04/09/2019
 * Time: 9:29 AM
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MTJiconaxSalesDatas extends Model
{
    use SoftDeletes;

    protected $table = "t_jiconax_sales_datas";

    const CREATED_AT = Null;
    const UPDATED_AT = Null;
}
