<?php

namespace App\Console\Commands;

use App\Models\MBusinessOffices;
use App\Models\MCustomers;
use App\Models\MEmptyInfo;
use App\Models\MGeneralPurposes;
use App\Models\MStaffDependents;
use App\Models\MStaffQualifications;
use App\Models\MStaffs;
use App\Models\MSupplier;
use App\Models\MVehicles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\WriteLogs;

class ConvertDataByExcels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ConvertDataByExcels {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
マスタデータマッピング.xlsx 車両シートを参考に、table_params にデータコンバートするバッチを作成してください。
登録時にエラーが発生した場合はすべてのデータをロールバックしてください。
エラーが発生しても次のデータを読み込んで、すべてのデータを処理してください。
';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $type = $this->option("type");
        switch ($type){
            case 'mst_staffs':
                $path = config('params.import_file_path.mst_staffs');
                break;
            case 'mst_staff_dependents':
                $path = "";
                break;
            case 'mst_staff_qualifications':
                $path = "";
                break;
            case 'mst_vehicles':
                $path = config('params.import_file_path.mst_vehicles');
                break;
            case 'mst_customers':
                $path = "";
                break;
            case 'mst_suppliers':
                $path = "";
                break;
            case 'mst_business_offices':
                $path = "";
                break;
        }
        $this->getDataFromExcel($path, $type);
    }

    protected function getDataFromExcel($path, $type){
        try {
            $inputFileType = \PHPExcel_IOFactory::identify($path);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($path);
        } catch(Exception $e) {
            return ('Error loading file "'.pathinfo($path,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $keys = array();
        $start_row = 2;
        for ($row = $start_row; $row <= $highestRow; $row++){
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,null,false,false, true);
            switch ($type){
                case 'mst_staffs':
                    $this->importStaffs($rowData,$row);
                    break;
                case 'mst_staff_dependents':
                    break;
                case 'mst_staff_qualifications':
                    break;
                case 'mst_vehicles':
                    $this->importVehicle($rowData, $row);
                    break;
                case 'mst_customers':
                    break;
                case 'mst_suppliers':
                    break;
                case 'mst_business_offices':
                    break;
            }
        }
    }

    public function importStaffs($rowData,$row){
        $model= new MStaffs();
        $excel_column = $model->excel_column;
        $record = array();
        $mGeneralPurposes = new MGeneralPurposes();
        foreach($rowData[$row] as $pos=>$value){
            if(isset($excel_column[$pos])) {
                switch ($excel_column[$pos]){
                    case 'created_at':
                    case 'modified_at':
                        $record[$excel_column[$pos]] = \PHPExcel_Style_NumberFormat::toFormattedString($value,'mm/dd/yyyy hh:mm:ss');
                        break;
                    default:
                        $record[$excel_column[$pos]] = $value;
                }
            }

        }
        WriteLogs::initLog();
        dd($record);
    }
    public function importVehicle($rowData, $row){
        $model =  new MVehicles();
        $excel_column = $model->excel_column;
        $record = array();
        $mGeneralPurposes = new MGeneralPurposes();
        foreach($rowData[$row] as $pos=>$value){
            if(isset($excel_column[$pos])) {
                switch ($excel_column[$pos]){
                    case 'created_at':
                    case 'modified_at':
                        $record[$excel_column[$pos]] = \PHPExcel_Style_NumberFormat::toFormattedString($value,'mm/dd/yyyy hh:mm:ss');
                    break;
                    default:
                        $record[$excel_column[$pos]] = $value;
                }
            }

        }
        dd($record);
//              if(!empty($rows)){
//                DB::table($type)->insert($rows);
//              }
    }
}
