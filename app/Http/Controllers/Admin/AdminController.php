<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $pageTitle = "Admin Dashboard";
        return view('admin.index',compact('pageTitle'));
    }
}
