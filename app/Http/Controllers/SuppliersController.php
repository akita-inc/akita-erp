<?php

namespace App\Http\Controllers;

use App\Models\MSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SuppliersController extends Controller
{

    public function index(Request $request)
    {
        $mSuppliers = new MSupplier();

        $where = array(
            'suppliers_cd' => $request->get('supplier_cd'),
            'supplier_nm' => $request->get('supplier_nm'),
        );

        return view('suppliers.index', [
            'suppliers' => $mSuppliers->getSuppliers($where),
        ]);
    }

    public function create(Request $request){

        $mSupplier = new MSupplier();
        if ($request->getMethod() == 'POST') {
            $data = $request->all();
            $rules = [
                'mst_suppliers_cd'  => 'required',
                'adhibition_start_dt'  => 'required',
            ];
            $validator = Validator::make($data, $rules,array(),$mSupplier->label);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator->errors())
                    ->withInput();
            }else{
                DB::beginTransaction();
                try
                {
                    $mSupplier->mst_suppliers_cd= $data["mst_suppliers_cd"];
                    $mSupplier->adhibition_start_dt= $data["adhibition_start_dt"];
                    $mSupplier->adhibition_end_dt= $data["adhibition_end_dt"];
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
                    $mSupplier->business_start_dt= $data["business_start_dt"];
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
                    \Session::flash('message','Add users successfully.');
                    return redirect()->route('suppliers.list');
                }catch (\Exception $e) {
                    DB::rollback();
                    dd($e);
                }
            }
        }
        return view('suppliers.create',[
            '$Supplier' => $mSupplier
        ]);
    }
}