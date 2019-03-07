<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MSupplier;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuppliersController extends Controller
{
    use ListTrait;
    public $table = "mst_suppliers";

    public function index(Request $request)
    {
        return view('suppliers.index');
    }

    protected function search($data){
        $where = array(
            'suppliers_cd' => $data['fieldSearch']['mst_suppliers_cd'],
            'supplier_nm' => $data['fieldSearch']['supplier_nm'],
            'radio_reference_date' => $data['fieldSearch']['radio_reference_date'],
            'reference_date' => $data['fieldSearch']['reference_date'],
        );

        $this->query->select('mst_suppliers.id',
            'mst_suppliers.mst_suppliers_cd',
            'mst_suppliers.supplier_nm',
            'mst_suppliers.supplier_nm_kana',
            DB::raw('CONCAT(mst_general_purposes.date_nm,mst_suppliers.address1,mst_suppliers.address2,mst_suppliers.address3) as street_address'),
            'mst_suppliers.explanations_bill',
            DB::raw("DATE_FORMAT(mst_suppliers.adhibition_start_dt, '%Y-%m-%d ') as adhibition_start_dt"),
            DB::raw("DATE_FORMAT(mst_suppliers.adhibition_end_dt, '%Y-%m-%d ') as adhibition_end_dt"),
            DB::raw("DATE_FORMAT(mst_suppliers.modified_at, '%Y-%m-%d') as modified_at"),
            'mst_general_purposes.date_nm',
            DB::raw("DATE_FORMAT(sub.max_adhibition_end_dt, '%Y-%m-%d') as max_adhibition_end_dt"));

        $this->query
            ->leftjoin('mst_general_purposes', function ($join) {
                $join->on('mst_general_purposes.date_id', '=', 'mst_suppliers.prefectures_cd')
                    ->where('mst_general_purposes.data_kb', config('params.data_kb')['prefecture']);
            })
            ->leftjoin(DB::raw('(select mst_suppliers_cd, max(adhibition_end_dt) AS max_adhibition_end_dt from mst_suppliers where deleted_at IS NULL group by mst_suppliers_cd) sub'), function ($join) {
                $join->on('sub.mst_suppliers_cd', '=', 'mst_suppliers.mst_suppliers_cd');
            });
        $this->query->whereRaw('mst_suppliers.deleted_at IS NULL');

        // 検索条件
        if (isset($where['suppliers_cd']) && $where['suppliers_cd'] != '')
            $this->query->where('mst_suppliers.mst_suppliers_cd', "LIKE", "%{$where['suppliers_cd']}%");
        if (isset($where['supplier_nm']) && $where['supplier_nm'] != '')
            $this->query->where('mst_suppliers.supplier_nm', "LIKE", "%{$where['supplier_nm']}%");
        if (isset($where['radio_reference_date']) && $where['radio_reference_date'] == '1' && isset($where['reference_date']) && $where['reference_date'] != '') {
            $this->query->where('mst_suppliers.adhibition_start_dt', "<=", $where['reference_date']);
            $this->query->where('mst_suppliers.adhibition_end_dt', ">=", $where['reference_date']);
        }

        $this->query->orderby('mst_suppliers.mst_suppliers_cd');
        $this->query->orderby('mst_suppliers.adhibition_start_dt');
    }

    public function delete($id)
    {
        $mSuppliers = new MSupplier();
        $mSuppliers = $mSuppliers->find($id);

        try
        {
            $mSuppliers->delete();
            $response = ['data' => 'success'];

        } catch (\Exception $ex){
            $response = ['data' => 'failed'];
        }
        return response()->json($response);
    }

    public function create(Request $request){

        return view('suppliers.create');
    }
}