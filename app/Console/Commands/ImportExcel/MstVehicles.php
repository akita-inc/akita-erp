<?php
namespace App\Console\Commands\ImportExcel;
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 4/17/2019
 * Time: 3:06 PM
 */
class MstVehicles extends BaseImport
{
    public $list_etc_numbers = [];
    public $inputFileType = null;
    public $objReader = null;
    public $objPHPExcel = null;
    public $sheet = null;
    public $highestRow = 1;
    public $highestColumn = null;
    public $start_row = 1;

    public $excel_column_main = [
        'A'=>'vehicles_cd',
        'B'=>'vehicles_kb',
        'C'=>'registration_numbers',
        'D'=>'mst_business_office_id',
        'I'=>'vehicle_size_kb',
        'J'=>'vehicle_purpose_id',
        'AF'=>'land_transport_office_cd',
        'F' => 'registration_dt',
        'G' => 'first_year_registration_dt',
        'H' => 'vehicle_classification_id',
        'K' => 'private_commercial_id',
        'L' => 'car_body_shape_id',
        'M' => 'vehicle_id',
        'O' => 'seating_capacity',
        'P' => 'max_loading_capacity',
        'Q' => 'vehicle_body_weights',
        'R' => 'vehicle_total_weights',
        'S' => 'frame_numbers',
        'U' => 'vehicle_lengths',
        'V' => 'vehicle_widths',
        'W' => 'vehicle_heights',
        'Z' => 'axle_loads_ff',
        'AA' => 'axle_loads_fr',
        'AB' => 'axle_loads_rf',
        'AC' => 'axle_loads_rr',
        'N' => 'vehicle_types',
        'T' => 'engine_typese',
        'X' => 'total_displacements',
        'Y' => 'kinds_of_fuel_id',
        'AE' => 'user_base_locations',
        'AD' => 'expiry_dt',
        'AL' => 'mst_staff_cd',
        'AH' => 'personal_insurance_prices',
        'AI' => 'property_damage_insurance_prices',
        'AJ' => 'vehicle_insurance_prices',
        'AW' => 'dispose_dt',
        'AX' => 'created_at',
        'BA' => 'modified_at',
    ];
    public $excel_column_extra_1_sheet_1 = [
        'B' => 'vehicles_cd',
        'N' => 'bed_fg',
        'O' => 'refrigerator_fg',
        'V' => 'drive_system_id',
        'W' => 'drive_system_id',
        'X' => 'drive_system_id',
        'Y' => 'transmissions_id',
        'Z' => 'transmissions_id',
        'AA' => 'suspensions_cd',
        'AB' => 'suspensions_cd',
        'AC' => 'suspensions_cd',
        'AD' => 'tank_capacity_1',
        'AE' => 'tank_capacity_2',
        'AI' => 'floor_roller_fg',
        'AJ' => 'floor_joloda_conveyor_fg',
        'AK' => 'power_gate_cd',
    ];
    public $excel_column_extra_1_sheet_2 = [
        'B' => 'vehicles_cd',
        'N' => 'bed_fg',
        'O' => 'refrigerator_fg',
        'X' => 'transmissions_id',
        'AA' => 'transmissions_id',
        'AB' => 'suspensions_cd',
        'AC' => 'suspensions_cd',
        'AD' => 'suspensions_cd',
        'AE' => 'tank_capacity_1',
        'AF' => 'tank_capacity_2',
        'AJ' => 'floor_joloda_conveyor_fg',
        'AK' => 'power_gate_cd',
    ];

    public $data_extra_file_1 = [];
    public $data_extra_file_2 = [];
    public $data_extra_file_3 = [];

    public function run(){
        echo "mst_vehicles";
        exit;
//        $this->readingVehicleExtraFile1();
//        $this->readingVehicleExtraFile2();
//        $this->readingVehicleExtraFile3();
//        $this->readingMainFile();
    }

    public function getDataFromExcel( $path){

        $this->inputFileType = \PHPExcel_IOFactory::identify($path);
        $this->objReader = \PHPExcel_IOFactory::createReader($this->inputFileType );
        $this->objPHPExcel = $this->objReader->load($path);

        $this->sheet = $this->objPHPExcel->getSheet(0);
        $this->highestRow =  $this->sheet->getHighestRow();
        $this->highestColumn =  $this->sheet->getHighestColumn();

    }

    public function readingMainFile(){
        $model =  new MVehicles();
        $excel_column = $this->excel_column_main;
        $record = array();
        $data = [];
        $mGeneralPurposes = new MGeneralPurposes();
        $this->getDataFromExcel(config('params.import_file_path.mst_vehicles.main'));
        $this->start_row = 2;
        for ($row = $this->start_row; $row <= $this->highestRow; $row++) {
            $rowData = $this->sheet->rangeToArray('A' . $row . ':' .  $this->highestColumn . $row, null, false, false, true);
            foreach ($rowData[$row] as $pos => $value) {
                if (isset($excel_column[$pos])) {
                    switch ($excel_column[$pos]) {
                        case 'created_at':
                        case 'modified_at':
                            $record[$excel_column[$pos]] = \PHPExcel_Style_NumberFormat::toFormattedString($value, 'mm/dd/yyyy hh:mm:ss');
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
                            switch ($excel_column[$pos]) {
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
                            $result = $mGeneralPurposes->checkExistDataAndInsert($data_kb, $value);
                            if ($result) {
                                $record[$excel_column[$pos]] = $result;
                            } else {
                                break 2;
                            }
                            break;
                        default:
                            $record[$excel_column[$pos]] = $value;
                    }
                }
                if (!empty($record)) {
                    DB::table('mst_vehicles_copy1')->insert($record);
                }
            }
        }

    }

    public function readingVehicleExtraFile1(){
        $this->getDataFromExcel(config('params.import_file_path.mst_vehicles.extra')[0]);
        $this->start_row = 2;
        for ($row = $this->start_row; $row <= $this->highestRow; $row++) {
            $rowData = $this->sheet->rangeToArray('A' . $row . ':' .  $this->highestColumn . $row, null, false, false, true);
            if(!is_null($rowData[$row]['B']) && is_numeric($rowData[$row]['B']) && !is_null($rowData[$row]['R'])){
                $this->data_extra_file_1[$rowData[$row]['B']] = (string)$rowData[$row]['R'];
            }
        }

    }

    public function readingVehicleExtraFile2(){
        $this->getDataFromExcel(config('params.import_file_path.mst_vehicles.extra')[1]);
        $this->start_row = 7;
        $keys =[
            'N' => 1,
            'O' => 1,
            'V' => 1,
            'W' => 2,
            'X' => 3,
            'Y' => 2,
            'Z' => 1,
            'AA' => 1,
            'AB' => 2,
            'AC' => 3,
        ];

        for ($i = 0; $i<2 ; $i++) {
            $this->sheet = $this->objPHPExcel->setActiveSheetIndex($i);
            $this->highestRow =  $this->sheet->getHighestRow();
            $this->highestColumn =  $this->sheet->getHighestColumn();
            $excel_column = $this->{'excel_column_extra_1_sheet_'.($i+1)};
            for ($row = $this->start_row; $row <= $this->highestRow; $row++) {
                $rowData = $this->sheet->rangeToArray('A' . $row . ':' . $this->highestColumn . $row, null, false, false, true);
                foreach ($rowData[$row] as $pos => $value) {
                    if(!is_null($rowData[$row]['B'])){
                        $this->data_extra_file_2[$rowData[$row]['B']] = [];
                        if($i==0){
                            switch ($pos) {
                                case 'AD':
                                case 'AE':
                                    $this->data_extra_file_2[$rowData[$row]['B']][$excel_column[$pos]] = (string)$value;
                                    break;
                                case 'N':
                                case 'O':
                                case 'V':
                                case 'W':
                                case 'X':
                                case 'Y':
                                case 'Z':
                                case 'AA':
                                case 'AB':
                                case 'AC':
                                    if ($value != '') {

                                        $this->data_extra_file_2[$rowData[$row]['B']][$excel_column[$pos]] = $keys[$pos];
                                        dd($this->data_extra_file_2);
                                        if ($pos == 'Y' || $pos == 'Z') {
                                            if ($value != '○' && $value != '〇') {
                                                $this->data_extra_file_2[$rowData[$row]['B']]['transmissions_notes'] = $value;
                                            }
                                        }
                                    }
                                    break;
                            }
                        }else{
                            switch ($pos) {
                                case 'AE':
                                case 'AF':
                                    $this->data_extra_file_2[$rowData[$row]['B']][$excel_column[$pos]] = $value;
                                    break;
                                case 'N':
                                case 'O':
                                case 'Z':
                                case 'AA':
                                case 'AB':
                                case 'AC':
                                case 'AD':
                                    if ($value != '') {
                                        $this->data_extra_file_2[$rowData[$row]['B']][$excel_column[$pos]] = $keys[$pos];
                                        if ($pos == 'Y' || $pos == 'Z') {
                                            if ($value != '○' && $value != '〇') {
                                                $this->data_extra_file_2[$rowData[$row]['B']]['transmissions_notes'] = $value;
                                            }
                                        }
                                    }
                                    break;
                            }
                        }
                    }

                }
                dd($this->data_extra_file_2[$rowData[$row]['B']]);
            }
        }
    }

    public function readingVehicleExtraFile3(){
        $this->getDataFromExcel(config('params.import_file_path.mst_vehicles.extra')[2]);
        $this->start_row = 3;
        for ($row = $this->start_row; $row <= $this->highestRow; $row++) {
            $rowData = $this->sheet->rangeToArray('A' . $row . ':' .  $this->highestColumn . $row, null, false, false, true);
            if(!is_null($rowData[$row]['A']) && is_numeric($rowData[$row]['A'])){
                $this->data_extra_file_3[$rowData[$row]['A']] = [
                    'acquisition_amounts' => is_null($rowData[$row]['G']) ? null :(string)$rowData[$row]['G'],
                    'durable_years' => is_null($rowData[$row]['J']) ? null : (string)$rowData[$row]['J']
                ];
            }
        }
    }
}
