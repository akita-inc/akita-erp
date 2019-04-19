<?php
namespace App\Console\Commands\ImportExcel;
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 4/17/2019
 * Time: 3:06 PM
 */
class MstBillIssueDestinations extends BaseImport
{
    public $path = "";
    public $excel_column = [
        'A'=>'mst_customer_id',
        'B'=>'bill_zip_cd',
        'C'=>'bill_address1',
        'D'=>'bill_address2',
        'E'=>'bill_address3',
        'G' => 'bill_phone_number',
    ];
    public $column_main_name=[
        'mst_customer_id'=>'mst_customer_id',
        'bill_zip_cd'=>'郵便番号',
        'bill_address1'=>'住所１',
        'bill_address2'=>'住所１',
        'bill_address3'=>'住所２',
        'bill_phone_number' => '電話番号',
    ];
    public $labels=[];
    public $messagesCustom=[];
    public $ruleValid = [
        'mst_customer_id'=>'required',
        'bill_zip_cd'  => 'required|zip_code|length:7',
        'address1'  => 'nullable|length:20',
        'address2'  => 'nullable|length:20',
        'address3'  => 'nullable|length:50',
        'bill_phone_number'  => 'phone_number|nullable|length:20',
    ];
    public $configDataImport = [];

    public function __construct()
    {
        $this->path = config('params.import_file_path.mst_staff_dependents');
        $this->dateTimeRun = date("YmdHis");
    }

    public function import()
    {
    }
}
