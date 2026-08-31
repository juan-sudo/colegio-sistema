<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\MarkAttendanceRequest;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private WhatsAppService $whatsapp) {}

    public function scanner(Course $course)
    {
        return view("teacher.attendance.scanner", compact("course"));
    }

    public function registrar(MarkAttendanceRequest $request, Course $course)
    {
        $data = $request->validated();

        $student = Student::query()
            ->where("qr_token", $data["code"])
            ->orWhere("barcode", $data["code"])
            ->orWhere("biometric_id", $data["code"])
            ->orWhere("code", $data["code"])
            ->firstOrFail();

        $horaLimite = Carbon::parse("08:00:00");
        $ahora = Carbon::now();
        $status = $ahora->gt($horaLimite) ? "tardanza" : "presente";

        $attendance = Attendance::updateOrCreate(
            [
                "student_id" => $student->id,
                "course_id" => $course->id,
                "date" => $ahora->toDateString(),
            ],
            [
                "time_in" => $ahora->toTimeString(),
                "status" => $status,
                "method" => $data["method"],
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

    public function marcarFaltas(Request $request, Course $course)
    {
        $fecha = $request->date("date") ?? now()->toDateString();

        $yaRegistrados = Attendance::where("course_id", $course->id)
            ->where("date", $fecha)
            ->pluck("student_id");

        $faltantes = $course->students()->whereNotIn("students.id", $yaRegistrados)->get();

        foreach ($faltantes as $student) {
            $attendance = Attendance::create([
                "student_id" => $student->id,
                "course_id" => $course->id,
                "date" => $fecha,
                "status" => "falta",
                "method" => "manual",
                "recorded_by" => auth()->id(),
            ]);

            $this->whatsapp->notificarFalta($student, $attendance);
        }

        return back()->with("success", "Se marcaron {$faltantes->count()} inasistencias y se enviaron las alertas por WhatsApp.");
    }
}
