<?php

namespace App\Http\Controllers;


use App\Helpers\TimeFunction;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MPurchases;
use App\Models\MSupplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AccountsPayableDataOutputController extends Controller {
    use ListTrait;
    public $table = "t_saleses";
    public $allNullAble = false;
    public $beforeItem = null;
    public $add_log = false;

    public $ruleValid = [
    ];

    public $labels = [];

    public $csvColumn = [
        'mst_suppliers_cd' => '仕入先コード',
        'supplier_nm' => '仕入先名',
        'purchases_tax_included_amount' => '請求金額',
        'saleses_tax_included_amount' => '売上金額',
    ];

    public function __construct(){
        date_default_timezone_set("Asia/Tokyo");
        parent::__construct();
    }

    public function index(Request $request){
        $mSupplier= new MSupplier();
        $listBundleDt = [];
        $listBundleDt= $mSupplier->getListBundleDt();
        if($listBundleDt){
            $listBundleDt = $listBundleDt->toArray();
        }
        $listMonth = [1,2,3,4,5,6,7,8,9,10,11,12];
        $currentMonth = date('m');
        $currentYear = (int)date('Y');
        if($currentMonth=='12'){
            $currentYear--;
        }
        $listYear = [$currentYear-1, $currentYear];
        return view('accounts_payable_data_output.index',[
            'listMonth'=>$listMonth,
            'listYear'=>$listYear,
            'listBundleDt' => $listBundleDt,
        ]);
    }

    public function getCurrentYearMonth(){
        $currentYear = (int)date("Y");
        $currentMonth = (int)date("m");;
        return response()->json([
            'current_year'=> $currentYear,
            'current_month'=> $currentMonth
        ]);
    }

    public function createCSV(Request $request){
        $data = $request->all();
        $fieldSearch = $data['fieldSearch'];
        $header1 = '買掛（傭車）,,,';
        $monthYear = TimeFunction::dateFormat($fieldSearch['billing_year'].'-'.$fieldSearch['billing_month'],'Y年m月');
        $date = ($fieldSearch['closed_date'] < 10 ? '0'.$fieldSearch['closed_date'] : $fieldSearch['closed_date'] ).'日締め';
        $header2 = "$monthYear,$date,,";
        $keys = array_keys($this->csvColumn);
        $mPurchases =  new MPurchases();
        $csvContent = $mPurchases->getAccountsPayableData($fieldSearch);
        if(count($csvContent) > 0 ){
            $fileName = 'purchase'.TimeFunction::dateFormat($fieldSearch['start_date'],'Ymd').'-'.TimeFunction::dateFormat($fieldSearch['end_date'],'Ymd').'.csv';
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0",
            );

            $callback = function() use ($keys,$csvContent,$header1,$header2) {
                $file = fopen('php://output', 'w');
                fwrite ($file,mb_convert_encoding($header1, "SJIS-win", "UTF-8")."\r\n");
                fwrite ($file,mb_convert_encoding($header2, "SJIS-win", "UTF-8")."\r\n");
                fwrite ($file,implode(config('params.accounts_payable_csv.delimiter'),mb_convert_encoding(array_values($this->csvColumn), "SJIS-win", "UTF-8"))."\r\n");
                foreach ($csvContent as $content) {
                    $row = [];
                    foreach ($keys as $key) {
                        $row[$key] = $content->{$key};
                    }
                    fwrite ($file,implode(config('params.accounts_payable_csv.delimiter'),mb_convert_encoding($row, "SJIS-win", "UTF-8"))."\r\n");
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }else{
            return response()->json(['success' =>false,'msg' => '']);
        }

    }
}