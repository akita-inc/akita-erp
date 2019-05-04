<?php

namespace App\Http\Controllers\Api;

use App\Models\MCustomers;
use Illuminate\Http\Request;
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
}