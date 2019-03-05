<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuppliersController extends Controller
{

    public function index(Request $request)
    {
        return view('suppliers.index');
    }
}