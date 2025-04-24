<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function home()
    {
        $pageTitle = "Customer Dashboard";
        return view('admin.customer.index', compact('pageTitle'));
    }
}
