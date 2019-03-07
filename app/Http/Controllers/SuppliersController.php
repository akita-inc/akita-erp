<?php

namespace App\Http\Controllers;

use App\Helpers\TimeFunction;
use App\Models\MSupplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
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
                'mst_suppliers_cd'  => 'required|one_bytes_string',
                'adhibition_start_dt'  => 'required',
                'supplier_nm_kana'  => 'kana|nullable',
                'supplier_nm_kana_formal'  => 'kana|nullable',
                'dealing_person_in_charge_last_nm_kana'  => 'kana|nullable',
                'dealing_person_in_charge_first_nm_kana'  => 'kana|nullable',
                'accounting_person_in_charge_last_nm_kana'  => 'kana|nullable',
                'accounting_person_in_charge_first_nm_kana'  => 'kana|nullable',
                'phone_number'  => 'phone_number|nullable',
                'fax_number'  => 'fax_number|nullable',
                'zip_cd'  => 'zip_code|nullable',
                'bundle_dt'  => 'one_byte_number|nullable',
                'payday'  => 'one_byte_number|nullable|between_custom:1,31',
                'payment_day'  => 'one_byte_number|nullable|between_custom:1,31',
            ];
            $validator = Validator::make($data, $rules,array(),$mSupplier->label);
            $validator->after(function ($validator) use ($data){
                if (Carbon::parse($data['adhibition_start_dt']) > Carbon::parse(config('params.adhibition_end_dt_default'))) {
                    $validator->errors()->add('adhibition_start_dt',Lang::get('messages.MSG02014'));
                }
            });
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator->errors())
                    ->withInput();
            }else{
                DB::beginTransaction();
                try
                {
                    $mSupplier->mst_suppliers_cd= $data["mst_suppliers_cd"];
                    $mSupplier->adhibition_start_dt= TimeFunction::dateFormat($data["adhibition_start_dt"],'yyyy-mm-dd');
                    $mSupplier->adhibition_end_dt= TimeFunction::dateFormat(config('params.adhibition_end_dt_default'),'yyyy-mm-dd');
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
                    $mSupplier->business_start_dt= TimeFunction::dateFormat($data["business_start_dt"],'yyyy-mm-dd');
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
                    \Session::flash('message',Lang::get('messages.MSG03002'));
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