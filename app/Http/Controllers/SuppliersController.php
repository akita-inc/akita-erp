<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Helpers\TimeFunction;
use App\Models\MGeneralPurposes;
use App\Models\MStaffAuths;
use App\Models\MSupplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class SuppliersController extends Controller
{

    use ListTrait,FormTrait;
    public $table = "mst_suppliers";

    public function index(Request $request)
    {
        return view('suppliers.index');
    }

    protected function search($data){
        $where = array(
            'suppliers_cd' => $data['fieldSearch']['mst_suppliers_cd'],
            'supplier_nm' => $data['fieldSearch']['supplier_nm'],
        );

        $this->query->select('mst_suppliers.id',
            'mst_suppliers.mst_suppliers_cd',
            'mst_suppliers.supplier_nm',
            'mst_suppliers.supplier_nm_kana',
            DB::raw("CONCAT_WS('',mst_general_purposes.date_nm,mst_suppliers.address1,mst_suppliers.address2,mst_suppliers.address3) as street_address"),
            'mst_suppliers.explanations_bill',
            DB::raw("DATE_FORMAT(mst_suppliers.modified_at, '%Y/%m/%d') as modified_at"));

        $this->query
            ->leftjoin('mst_general_purposes', function ($join) {
                $join->on('mst_general_purposes.date_id', '=', 'mst_suppliers.prefectures_cd')
                    ->where('mst_general_purposes.data_kb', config('params.data_kb.prefecture_cd'));
            })
            ->whereRaw('mst_suppliers.deleted_at IS NULL');

        if ($where['suppliers_cd'] != '') {
            $this->query->where('mst_suppliers.mst_suppliers_cd', "LIKE", "%{$where['suppliers_cd']}%");
        }
        if ($where['supplier_nm'] != '') {
            $this->query->where('mst_suppliers.supplier_nm', "LIKE", "%{$where['supplier_nm']}%");
        }
        if ($data["order"]["col"] != '') {
            if ($data["order"]["col"] == 'street_address')
                $orderCol = "CONCAT_WS('',mst_general_purposes.date_nm_kana,mst_suppliers.address1,mst_suppliers.address2,mst_suppliers.address3)";
            else
                $orderCol = $data["order"]["col"];
            if (isset($data["order"]["descFlg"]) && $data["order"]["descFlg"]) {
                $orderCol .= " DESC";
            }
            $this->query->orderbyRaw($orderCol);
        } else {
            $this->query->orderby('mst_suppliers.mst_suppliers_cd');
        }
    }

    public function delete(Request $request,$id)
    {
        $mSuppliers = new MSupplier();
        $this->backHistory();
        if ($request->getMethod() == 'POST') {
            if ($mSuppliers->deleteSupplier($id)) {
                \Session::flash('message',Lang::get('messages.MSG10004'));
            } else {
                \Session::flash('message',Lang::get('messages.MSG06002'));
            }

            return redirect()->route('suppliers.list');
        }
        if ($mSuppliers->deleteSupplier($id)) {
            $response = ['data' => 'success', 'msg' => Lang::get('messages.MSG10004')];
        } else {
            $response = ['data' => 'failed', 'msg' => Lang::get('messages.MSG06002')];
        }

        return response()->json($response);
    }

    public function create(Request $request, $id=null){

        $mSupplier = new MSupplier();
        if(!is_null($id)){
            $mSupplier = $mSupplier->find($id);
            if(is_null($mSupplier)){
                return abort(404);
            }
        }
        $mGeneralPurposes = new MGeneralPurposes();
        $listPrefecture= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['prefecture_cd'],'');
        $listPaymentMethod= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['payment_method'],'');
        $listPaymentMonth= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['payment_month'],'');
        $listConsumptionTaxCalcUnit= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['consumption_tax_calc_unit'],'');
        $listRoundingMethod= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['rounding_method'],'');
        $listPaymentAccountType= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['payment_account_type'],'');
        $mStaffAuth =  new MStaffAuths();
        $role = $mStaffAuth->getRoleBySCreen(2);
        if ($request->getMethod() == 'POST') {
            $data = $request->all();
            $rules = [
                'mst_suppliers_cd'  => 'required|one_bytes_string|length:5|unique:mst_suppliers,mst_suppliers_cd,NULL,id,deleted_at,NULL',
                'supplier_nm'  => 'required|length:200',
                'supplier_nm_kana'  => 'kana|nullable|length:200',
                'supplier_nm_formal'  => 'length:200|nullable',
                'supplier_nm_kana_formal'  => 'kana|length:200|nullable',
                'dealing_person_in_charge_last_nm'  => 'length:25|nullable',
                'dealing_person_in_charge_first_nm'  => 'length:25|nullable',
                'dealing_person_in_charge_last_nm_kana'  => 'kana|nullable|length:50',
                'dealing_person_in_charge_first_nm_kana'  => 'kana|nullable|length:50',
                'accounting_person_in_charge_last_nm'  => 'length:25|nullable',
                'accounting_person_in_charge_first_nm'  => 'length:25|nullable',
                'accounting_person_in_charge_last_nm_kana'  => 'kana|nullable|length:50',
                'accounting_person_in_charge_first_nm_kana'  => 'kana|nullable|length:50',
                'phone_number'  => 'phone_number|nullable|length:20',
                'address1'  => 'nullable|length:20',
                'address2'  => 'nullable|length:20',
                'address3'  => 'nullable|length:50',
                'hp_url'  => 'nullable|length:2500',
                'fax_number'  => 'fax_number|nullable|length:20',
                'zip_cd'  => 'zip_code|nullable|length:7',
                'bundle_dt'  => 'one_byte_number|nullable|length:2',
                'payday'  => 'one_byte_number|nullable|between_custom:1,31|length:2',
                'payment_day'  => 'one_byte_number|nullable|between_custom:1,31|length:2',
                'explanations_bill'  => 'nullable|length:100',
                'payment_bank_cd'  => 'nullable|length:4',
                'payment_bank_name'  => 'nullable|length:30',
                'payment_branch_cd'  => 'nullable|length:4',
                'payment_branch_name'  => 'nullable|length:30',
                'payment_account_number'  => 'nullable|length:10',
                'payment_account_holder'  => 'nullable|length:30',
                'notes'  => 'nullable|length:50',
            ];
            if($id!= null){
                $rules['mst_suppliers_cd'] = "required|one_bytes_string|length:5|unique:mst_suppliers,mst_suppliers_cd,{$id},id,deleted_at,NULL";
            }
            $validator = Validator::make($data, $rules,array('mst_suppliers_cd.unique' => str_replace(':screen','仕入先',Lang::get('messages.MSG10003'))),$mSupplier->label);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator->errors())
                    ->withInput();
            }else{
                DB::beginTransaction();
                try
                {
                    $mSupplier->mst_suppliers_cd= $data["mst_suppliers_cd"];
                    $mSupplier->supplier_nm= $data["supplier_nm"];
                    $mSupplier->supplier_nm_kana= $data["supplier_nm_kana"];
                    $mSupplier->supplier_nm_formal= $data["supplier_nm_formal"];
                    $mSupplier->supplier_nm_kana_formal= $data["supplier_nm_kana_formal"];
                    $mSupplier->dealing_person_in_charge_last_nm= $data["dealing_person_in_charge_last_nm"];
                    $mSupplier->dealing_person_in_charge_first_nm= $data["dealing_person_in_charge_first_nm"];
                    $mSupplier->dealing_person_in_charge_last_nm_kana= $data["dealing_person_in_charge_last_nm_kana"];
                    $mSupplier->dealing_person_in_charge_first_nm_kana= $data["dealing_person_in_charge_first_nm_kana"];
                    $mSupplier->accounting_person_in_charge_last_nm= $data["accounting_person_in_charge_last_nm"];
                    $mSupplier->accounting_person_in_charge_first_nm= $data["accounting_person_in_charge_first_nm"];
                    $mSupplier->accounting_person_in_charge_last_nm_kana= $data["accounting_person_in_charge_last_nm_kana"];
                    $mSupplier->accounting_person_in_charge_first_nm_kana= $data["accounting_person_in_charge_first_nm_kana"];
                    $mSupplier->zip_cd= $data["zip_cd"];
                    $mSupplier->prefectures_cd= $data["prefectures_cd"];
                    $mSupplier->address1= $data["address1"];
                    $mSupplier->address2= $data["address2"];
                    $mSupplier->address3= $data["address3"];
                    $mSupplier->phone_number= $data["phone_number"];
                    $mSupplier->fax_number= $data["fax_number"];
                    $mSupplier->hp_url= $data["hp_url"];
                    $mSupplier->bundle_dt= $data["bundle_dt"];
                    $mSupplier->payday= $data["payday"];
                    $mSupplier->payment_month_id= $data["payment_month_id"];
                    $mSupplier->payment_day= $data["payment_day"];
                    $mSupplier->payment_method_id= $data["payment_method_id"];
                    $mSupplier->explanations_bill= $data["explanations_bill"];
                    $mSupplier->business_start_dt= TimeFunction::dateFormat($data["business_start_dt"],'Y-m-d');
                    $mSupplier->consumption_tax_calc_unit_id= $data["consumption_tax_calc_unit_id"];
                    $mSupplier->rounding_method_id= $data["rounding_method_id"];
                    $mSupplier->payment_bank_cd= $data["payment_bank_cd"];
                    $mSupplier->payment_bank_name= $data["payment_bank_name"];
                    $mSupplier->payment_branch_cd= $data["payment_branch_cd"];
                    $mSupplier->payment_branch_name= $data["payment_branch_name"];
                    $mSupplier->payment_account_type= $data["payment_account_type"];
                    $mSupplier->payment_account_number= $data["payment_account_number"];
                    $mSupplier->payment_account_holder= $data["payment_account_holder"];
                    $mSupplier->notes= $data["notes"];
                    $mSupplier->save();
                    DB::commit();
                    if($id!= null){
                        $this->backHistory();
                        \Session::flash('message',Lang::get('messages.MSG04002'));
                    }else{
                        \Session::flash('message',Lang::get('messages.MSG03002'));
                    }
                    return redirect()->route('suppliers.list');
                }catch (\Exception $e) {
                    DB::rollback();
                    dd($e);
                }
            }
        }
        return view('suppliers.create',[
            'mSupplier' => $mSupplier,
            'listPrefecture' => $listPrefecture,
            'listPaymentMethod' => $listPaymentMethod,
            'listPaymentMonth' => $listPaymentMonth,
            'listConsumptionTaxCalcUnit' => $listConsumptionTaxCalcUnit,
            'listRoundingMethod' => $listRoundingMethod,
            'listPaymentAccountType' => $listPaymentAccountType,
            'role' => $role,
        ]);
    }

}