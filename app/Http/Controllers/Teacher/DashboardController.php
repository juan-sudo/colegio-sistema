<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $courses = auth()->user()->teacher->courses;

        return view("teacher.dashboard", compact("courses"));
    }
}
