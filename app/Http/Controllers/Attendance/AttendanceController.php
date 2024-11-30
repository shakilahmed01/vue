<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $pageTitle = "Attendance";
        return view('attendance.attendance', compact('pageTitle'));
    }
}
