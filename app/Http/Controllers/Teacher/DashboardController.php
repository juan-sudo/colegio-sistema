<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $courses = auth()->user()->teacher->courses()->with('gradeSection')->get();

        return Inertia::render('Teacher/Dashboard', compact('courses'));
    }
}
