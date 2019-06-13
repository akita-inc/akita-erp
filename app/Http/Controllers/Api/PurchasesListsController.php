<?php

namespace App\Http\Controllers\Api;

use App\Models\MSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
class PurchasesListsController
{
    public function getSupplierList(Request $request)
    {
        $mSupplier = new MSupplier();
        $data = $mSupplier->select('mst_suppliers_cd',DB::raw("IFNULL(supplier_nm,'') as mst_suppliers_nm"))
                            ->whereNull('deleted_at')
                            ->get();
        return Response()->json(array('success'=>true,'data'=>$data));
    }
    public function exportCSV(Request $request)
    {
        $data=$request->all();

        $dateTimeRun = date("YmdHis");
        $filename=str_replace("123456",'branch_office_cd',config("params.export_csv.sales_lists.name"));
        $filename=str_replace($dateTimeRun,'yyyymmddhhmmss',$filename);
        Excel::create($filename, function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download('csv');
        return Response()->json(array('success'=>true));
    }
}