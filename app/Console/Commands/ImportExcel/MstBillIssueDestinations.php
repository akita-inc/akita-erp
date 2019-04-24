<?php
namespace App\Console\Commands\ImportExcel;
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 4/17/2019
 * Time: 3:06 PM
 */
use App\Models\MCustomers;
use App\Models\MGeneralPurposes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
class MstBillIssueDestinations extends BaseImport
{
    public $path = "";
    public $error_fg=false;
    public $excel_column = [
        'A'=>'mst_customer_cd',
        'E'=>'bill_zip_cd',
        'F'=>'bill_address1',
        'G'=>'bill_address3',
        'H' => 'bill_phone_number',
    ];
    public $column_main_name=[
        'mst_customer_cd'=>'得意先CD',
        'bill_zip_cd'=>'郵便番号',
        'bill_address1'=>'住所１',
        'bill_address2'=>'住所１',
        'bill_address3'=>'住所２',
        'bill_phone_number' => '電話番号',
    ];
    public $labels=[];
    public $messagesCustom=[];
    public $ruleValid = [
        'mst_customer_cd'=>'required',
        'bill_zip_cd'  => 'required|zip_code|length:7',
        'bill_address1'  => 'nullable|length:2',
        'bill_address2'  => 'nullable|length:20',
        'bill_address3'  => 'nullable|length:50',
        'bill_phone_number'  => 'nullable|length:20',
    ];
    public $configDataImport = [];

    public function __construct()
    {
        $this->path = config('params.import_file_path.mst_bill_issue_destinations.main');
        date_default_timezone_set("Asia/Tokyo");
        $this->dateTimeRun = date("YmdHis");
    }

    public function import()
    {
        $this->mainReading($this->rowCurrentData,$this->rowIndex);
    }
    public function mainReading($rowData,$row)
    {
        $this->error_fg=false;
        $excel_column = $this->excel_column;
        $record = array();
        $mGeneralPurposes = new MGeneralPurposes();
        if(!empty($rowData[$row]) ) {
            foreach($rowData[$row] as $pos=>$value) {
                if (isset($excel_column[$pos])) {
                    switch ($excel_column[$pos]) {
                        case 'bill_address1':
                            $prefectures_cd = $mGeneralPurposes->getPrefCdByPrefName($value);
                            if($prefectures_cd) {
                                $record['bill_address1']=$prefectures_cd['date_id'];
                                $record['bill_address2']=mb_substr($value,mb_strlen($prefectures_cd['date_nm']));
                            }
                            else {
                                $record['bill_address1']=null;
                                $record['bill_address2']=$value;
                            }
                            break;
                        case 'mst_customer_cd':
                            $mst_customer_id = $this->checkCustomerId($value);
                            $record['mst_customer_id'] = $mst_customer_id;
                            $record[$excel_column[$pos]] = is_null($value)?null:(string)$value;
                            break;
                        case "bill_zip_cd":
                            $record[$excel_column[$pos]] = str_replace("-", "", $value);
                            break;
                        default :
                            $record[$excel_column[$pos]] = is_null($value)?null:(string)$value;
                            break;
                    }
                }
                $record['disp_number']=1;
                $record['created_at']=date('Y-m-d H:i:s');
                $record['modified_at']=date('Y-m-d H:i:s');
            }

            if(!$this->error_fg) {
                $this->validateRow($record);
                unset($record['mst_customer_cd']);
                $this->insertDB($record);
            }
            else {
                $this->numErr++;
            }

        }
    }
    protected function validateRow(&$record)
    {
        if( !empty($this->ruleValid)) {
            $validator = Validator::make( $record, $this->ruleValid );
            if ($validator->fails()) {
                $failedRules = $validator->failed();
                foreach ($failedRules as $field => $errors){
                    foreach ($errors as $ruleName => $error){
                        if($ruleName=='Length'){
                            $this->log("DataConvert_Trim",Lang::trans("log_import.check_length_and_trim",[
                                "fileName" => config('params.import_file_path.mst_bill_issue_destinations.main_file_name'),
                                "excelFieldName" => $this->column_main_name[$field],
                                "row" => $this->rowIndex,
                                "excelValue" => $record[$field],
                                "tableName" => $this->table,
                                "DBFieldName" => $field,
                                "DBvalue" => mb_substr($record[$field],0,$error[0]),
                            ]));
                            $record[$field] = mb_substr($record[$field],0,$error[0]);
                        }
                        elseif($ruleName=='Required')
                        {
                            $this->error_fg=true;
                            $this->log("DataConvert_Err_required",Lang::trans("log_import.required",[
                                "fileName" => config('params.import_file_path.mst_bill_issue_destinations.main_file_name'),
                                "fieldName" => $this->column_main_name[$field],
                                "row" => $this->rowIndex,
                            ]));
                        }
                    }
                }
            }

        }

    }

    public function checkCustomerId($customer_cd)
    {
        $customer = MCustomers::where('deleted_at','=',null)
                            ->where('mst_customers_cd','=',$customer_cd)
                            ->first();
        if($customer) {
            return $customer->id;
        }
        else{
            $this->error_fg  = true;
            $this->log("DataConvert_Err_ID_Match", Lang::trans("log_import.existed_record_in_db", [
                "fileName" => config('params.import_file_path.mst_bill_issue_destinations.main_file_name'),
                "fieldName" => $this->column_main_name['mst_customer_cd'],
                "row" => $this->rowIndex,
            ]));
            return null;
        }
    }

    protected function insertDB($record)
    {
        if(!$this->error_fg) {
            DB::beginTransaction();
            try {
                if (DB::table('mst_bill_issue_destinations_copy1')->insert($record))               {
                    $this->numNormal++;
                    DB::commit();
                };
            } catch (\Exception $e) {
                $this->numErr++;
                DB::rollback();
                $this->log("DataConvert_Err_SQL", Lang::trans("log_import.insert_error", [
                    "fileName" => config('params.import_file_path.mst_bill_issue_destinations.main_file_name'),
                    "row" => $this->rowIndex,
                    "errorDetail" => $e->getMessage(),
                ]));
            }
        }
    }
}
