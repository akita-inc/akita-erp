<?php

namespace App\Http\Controllers;

use App\Models\MSupplier;
use Illuminate\Http\Request;

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

    public function add(Request $request)
    {
        return view('suppliers.add');
    }
}