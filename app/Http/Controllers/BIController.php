<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BIController extends Controller
{
    public function index()
    {
        return view('bi.index');
    }
}
