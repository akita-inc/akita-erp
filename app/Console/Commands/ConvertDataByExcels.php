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
                $path = "";
                break;
            case 'mst_staff_dependents':
                $path = "";
                break;
            case 'mst_staff_qualifications':
                $path = "";
                break;
            case 'mst_vehicles':
                $path = config('params.import_file_path.mst_vehicles.main');
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

    public function importVehicle($rowData, $row){
        $model =  new MVehicles();
        $excel_column = $model->excel_column;
        $record = array();
        $data = [];
        $mGeneralPurposes = new MGeneralPurposes();
        foreach($rowData[$row] as $pos=>$value){
            if(isset($excel_column[$pos])) {
                switch ($excel_column[$pos]){
                    case 'created_at':
                    case 'modified_at':
                        $record[$excel_column[$pos]] = \PHPExcel_Style_NumberFormat::toFormattedString($value,'mm/dd/yyyy hh:mm:ss');
                        break;
                    case 'vehicles_kb':
                    case 'vehicle_size_kb':
                    case 'vehicle_purpose_id':
                    case 'land_transport_office_cd':
                    case 'vehicle_classification_id':
                    case 'private_commercial_id':
                    case 'car_body_shape_id':
                    case 'vehicle_id':
                    case 'kinds_of_fuel_id':
                        switch ($excel_column[$pos]){
                            case 'vehicles_kb':
                                $data_kb = config('params.data_kb')['vehicles_kb'];
                                break;
                            case 'vehicle_size_kb':
                                $data_kb = config('params.data_kb')['vehicle_size_kb'];
                                break;
                            case 'vehicle_purpose_id':
                                $data_kb = config('params.data_kb')['vehicle_purpose'];
                                break;
                            case 'land_transport_office_cd':
                                $data_kb = config('params.data_kb')['land_transport_office_cd'];
                                break;
                            case 'vehicle_classification_id':
                                $data_kb = config('params.data_kb')['vehicle_classification'];
                                break;
                            case 'private_commercial_id':
                                $data_kb = config('params.data_kb')['private_commercial'];
                                break;
                            case 'car_body_shape_id':
                                $data_kb = config('params.data_kb')['car_body_shape'];
                                break;
                            case 'vehicle_id':
                                $data_kb = config('params.data_kb')['vehicle'];
                                break;
                            case 'kinds_of_fuel_id':
                                $data_kb = config('params.data_kb')['kinds_of_fuel'];
                                break;
                        }
                        $result = $mGeneralPurposes->checkExistDataAndInsert($data_kb,$value);
                        if($result){
                            $record[$excel_column[$pos]] = $result;
                        }else{
                            break 2;
                        }
                        break;
                    default:
                        $record[$excel_column[$pos]] = $value;
                }
            }
          if(!empty($record)){
            DB::table('mst_vehicles_copy1')->insert($record);
          }
        }

    }
}
