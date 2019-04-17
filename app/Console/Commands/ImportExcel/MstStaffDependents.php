<?php
namespace App\Console\Commands\ImportExcel;
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 4/17/2019
 * Time: 3:06 PM
 */
class MstStaffDependents extends BaseImport
{
    public $path = "";
    public function __construct()
    {
        $this->path = config('params.import_file_path.mst_staff_dependents');
    }

    public function import()
    {
        $this->log();
        dd($this->rowCurrent);
    }
}
