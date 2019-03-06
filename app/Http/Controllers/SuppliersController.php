<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuppliersController extends Controller
{

    public function index(Request $request)
    {
        return view('suppliers.index');
    }

    public function create(Request $request){

        return view('suppliers.create');
    }
}