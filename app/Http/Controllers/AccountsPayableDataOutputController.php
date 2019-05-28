<?php

namespace App\Http\Controllers;


use App\Http\Controllers\TraitRepositories\ListTrait;
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

    public $labels = [
        "closed_date" => "締め日",
        "closed_date_input" => "特例締め日",
    ];

    public $csvColumn = [
        'daily_report_date' => '日報日付',
        'branch_office_cd' => '支店CD',
        'document_no' => '伝票NO',
        'registration_numbers' => '登録番号',
        'staff_cd' => '社員CD',
        'staff_nm' => '社員名',
        'mst_customers_cd' => '得意先CD',
        'customer_nm' => '得意先名',
        'goods' => '品物',
        'departure_point_name' => '発地名',
        'landing_name' => '着地名',
        'delivery_destination' => '納入先',
        'quantity' => '数量',
        'unit_price' => '単価',
        'total_fee' => '便請求金額',
        'insurance_fee' => '保険料',
        'billing_fast_charge' => '請求高速料',
        'discount_amount' => '値引金額',
        'tax_included_amount' => '請求金額',
        'loading_fee' => '積込料',
        'wholesale_fee' => '取卸料',
        'waiting_fee' => '待機料',
        'incidental_fee' => '附帯料',
        'surcharge_fee' => 'サーチャージ料',
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
}