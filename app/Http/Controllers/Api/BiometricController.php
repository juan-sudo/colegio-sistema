<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BiometricController extends Controller
{
    public function registrar(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            "code" => "required|string",
            "method" => "required|in:qr,barcode,biometric,manual",
        ]);

        $student = Student::query()
            ->where("qr_token", $request->code)
            ->orWhere("barcode", $request->code)
            ->orWhere("biometric_id", $request->code)
            ->orWhere("code", $request->code)
            ->firstOrFail();

        $horaLimite = Carbon::parse("08:00:00");
        $ahora = Carbon::now();
        $status = $ahora->gt($horaLimite) ? "tardanza" : "presente";

        Attendance::updateOrCreate(
            [
                "student_id" => $student->id,
                "course_id" => $course->id,
                "date" => $ahora->toDateString(),
            ],
            [
                "time_in" => $ahora->toTimeString(),
                "status" => $status,
                "method" => $request->method,
                "recorded_by" => auth()->id(),
            ]
        );

        return response()->json([
            "ok" => true,
            "student" => $student->fullName(),
            "status" => $status,
            "time" => $ahora->format("H:i:s"),
        ]);
    }
}
