<?php

namespace App\Http\Controllers\Api;

use App\Models\MCustomers;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
class SalesListsController
{
    public function getCustomerList(Request $request)
    {
        $mCustomer = new MCustomers();
        $data = $mCustomer->select('mst_customers_cd','customer_nm_formal as mst_customers_nm')
                            ->whereNull('deleted_at')
                            ->get();
        return Response()->json(array('success'=>true,'data'=>$data));
    }
    public function exportCSV(Request $request)
    {
        $dateTimeRun = date("YmdHis");
        $data=$request->all();
        $filename=str_replace("123456",'mst_customer_cd',config("params.export_csv.sales_lists.name"));
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