<?php

namespace App\Http\Controllers\Api;

use App\Models\MCustomers;
use Illuminate\Http\Request;
class SalesListsController
{
    public function getCustomerList(Request $request)
    {
        $mCustomers = new MCustomers();
        $data = $mCustomers->getAllNm();
        if ($data) {
            $data = $data->toArray();
        }
        return Response()->json(array('success' => true, 'data' => array_column($data, $request->get('type')=='cd'?'mst_customers_cd':'customer_nm_formal')));
    }
}