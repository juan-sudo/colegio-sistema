<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorPNG;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with(["user", "gradeSection"])
            ->when($request->search, fn ($q, $search) => $q->where("code", "like", "%{$search}%")
                ->orWhere("dni", "like", "%{$search}%")
                ->orWhere("first_name", "like", "%{$search}%")
                ->orWhere("last_name", "like", "%{$search}%"))
            ->paginate(20);

        $gradeSections = \App\Models\GradeSection::all();

        return view("admin.students.index", compact("students", "gradeSections"));
    }

    public function create()
    {
        return view("admin.students.create");
    }

    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();

        $user = \App\Models\User::create([
            "name" => "{$data['first_name']} {$data['last_name']}",
            "email" => $data["email"],
            "password" => bcrypt($data["password"]),
            "role" => "student",
            "phone" => $data["phone"] ?? null,
        ]);

        $year = now()->year;
        $code = "{$year}-{$data['dni']}";

        $student = Student::create([
            "user_id" => $user->id,
            "first_name" => $data["first_name"],
            "last_name" => $data["last_name"],
            "dni" => $data["dni"],
            "code" => $code,
            "qr_token" => Str::uuid(),
            "barcode" => $data['dni'],
            "grade_section_id" => $data["grade_section_id"],
            "birth_date" => $data["birth_date"] ?? null,
            "biometric_id" => $data['dni'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Estudiante registrado correctamente.', 'student' => $student]);
        }

        return redirect()->route("admin.students.index")->with("success", "Estudiante registrado correctamente.");
    }

    public function edit(Student $student)
    {
        return view("admin.students.edit", compact("student"));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $data = $request->validated();

        $year = now()->year;
        $code = "{$year}-{$data['dni']}";

        $student->update([
            "first_name" => $data["first_name"],
            "last_name" => $data["last_name"],
            "dni" => $data["dni"],
            "code" => $code,
            "grade_section_id" => $data["grade_section_id"],
            "birth_date" => $data["birth_date"] ?? null,
            "active" => $data["active"] ?? true,
            "barcode" => $data['dni'],
            "biometric_id" => $data['dni'],
        ]);

        $student->user->update([
            "name" => "{$data['first_name']} {$data['last_name']}",
            "email" => $data["email"],
            "phone" => $data["phone"] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Estudiante actualizado correctamente.', 'student' => $student]);
        }

        return redirect()->route("admin.students.index")->with("success", "Estudiante actualizado correctamente.");
    }

    public function destroy(Student $student)
    {
        $student->user->delete();
        $student->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Estudiante eliminado correctamente.']);
        }

        return back()->with("success", "Estudiante eliminado correctamente.");
    }

    public function carnet(Student $student)
    {
        $showPdf = request()->routeIs('admin.students.carnet.print');

        return view("admin.students.carnet", compact("student", "showPdf"));
    }

    public function carnetPdf(Student $student)
    {
        $attendanceMethod = \App\Models\Setting::get('attendance_method', 'qr');
        $qrBase64 = '';
        $barcodeBase64 = '';

        if ($attendanceMethod === 'qr' || $attendanceMethod === 'both') {
            $qrWriter = new PngWriter();
            $qrCode = new QrCode($student->qr_token);
            $qrImage = $qrWriter->write($qrCode);
            $qrBase64 = 'data:image/png;base64,' . base64_encode($qrImage->getString());
        }

        if ($attendanceMethod === 'barcode' || $attendanceMethod === 'both') {
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $barcodeImage = $barcodeGenerator->getBarcode($student->barcode, BarcodeGeneratorPNG::TYPE_CODE_128);
            $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcodeImage);
        }

        $schoolName = \App\Models\Setting::get('school_name', 'Sistema Escolar');
        $schoolAddress = \App\Models\Setting::get('school_address', '');
        $schoolPhone = \App\Models\Setting::get('school_phone', '');

        $pdf = Pdf::loadView('admin.students.carnet-pdf', compact('student', 'qrBase64', 'barcodeBase64', 'schoolName', 'schoolAddress', 'schoolPhone', 'attendanceMethod'));
        $pdf->setPaper([0, 0, 243, 153]);
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultMediaType' => 'screen',
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
        ]);

        return $pdf->stream("carnet-{$student->code}.pdf");
    }
}
