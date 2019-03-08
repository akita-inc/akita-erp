<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MSupplier extends Model
{
    use SoftDeletes;

    protected $table = "mst_suppliers";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
        'mst_suppliers_cd' => '仕入先コード',
        'adhibition_start_dt' => '適用開始日',
        'adhibition_end_dt' => '適用終了日',
        'supplier_nm' => '仕入先名',
        'supplier_nm_kana' => '仕入先カナ名',
        'supplier_nm_formal' => '仕入先正式名',
        'supplier_nm_kana_formal' => '仕入先正式カナ名',
        'dealing_person_in_charge_last_nm' => '取引担当者名(姓）',
        'dealing_person_in_charge_first_nm' => '取引担当者名(名）',
        'dealing_person_in_charge_last_nm_kana' => '取引担当者名カナ（姓）',
        'dealing_person_in_charge_first_nm_kana' => '取引担当者名カナ（名）',
        'accounting_person_in_charge_last_nm' => '経理担当者名(姓）',
        'accounting_person_in_charge_first_nm' => '経理担当者名(名）',
        'accounting_person_in_charge_last_nm_kana' => '経理担当者名カナ（姓）',
        'accounting_person_in_charge_first_nm_kana' => '経理担当者名カナ（名）',
        'zip_cd' => '郵便番号',
        'prefectures_cd' => '都道府県',
        'address1' => '市区町村',
        'address2' => '町名番地',
        'address3' => '建物等',
        'phone_number' => '電話番号',
        'fax_number' => 'FAX番号',
        'hp_url' => 'WEBサイトアドレス',
        'bundle_dt' => '締日',
        'payday' => '支払日',
        'payment_month_id' => '支払予定月',
        'payment_day' => '支払予定日',
        'payment_method_id' => '支払予定方法',
        'explanations_bill' => '支払いに関する説明',
        'business_start_dt' => '取引開始日',
        'consumption_tax_calc_unit_id' => '消費税計算単位区分',
        'rounding_method_id' => '消費税端数処理区分',
        'payment_bank_cd' => '支払銀行コード',
        'payment_bank_name' => '支払銀行名',
        'payment_branch_cd' => '支払銀行支店コード',
        'payment_branch_name' => '支払銀行支店名',
        'payment_account_type' => '支払口座種別',
        'payment_account_number' => '支払口座番号',
        'payment_account_holder' => '支払口座名義',
        'notes' => '備考',
    ];

    public $rules = [

    ];

    public function getSuppliers($where = array()){
        $suppliers = new MSupplier();
        $suppliers = $suppliers->select(DB::raw('mst_suppliers.*'))
                                ->addselect('mst_general_purposes.date_nm');
        $suppliers = $suppliers->leftjoin('mst_general_purposes', function ($join) {
            $join->on('mst_general_purposes.date_id', '=', 'mst_suppliers.prefectures_cd')
                    ->where('mst_general_purposes.data_kb', config('params.data_kb')['prefecture']);
        });

        // 検索条件
        if (isset($where['suppliers_cd']) && $where['suppliers_cd'] != '')
            $suppliers = $suppliers->where('mst_suppliers_cd', "LIKE", "%{$where['suppliers_cd']}%");
        if (isset($where['supplier_nm']) && $where['supplier_nm'] != '')
            $suppliers = $suppliers->where('supplier_nm', "LIKE", "%{$where['supplier_nm']}%");
        if (isset($where['reference_date']) && $where['reference_date'] != '') {
            $suppliers = $suppliers->where('adhibition_start_dt', "<=", $where['reference_date']);
            $suppliers = $suppliers->where('adhibition_end_dt', ">=", $where['reference_date']);
        }

        $suppliers->orderBy('mst_suppliers_cd', 'adhibition_start_dt');

        return $suppliers->paginate(config("params.page_size"));
    }

}
