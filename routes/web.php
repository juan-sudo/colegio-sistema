<?php

use App\Http\Controllers\Admin\AcademicManagement\AcademicYearController;
use App\Http\Controllers\Admin\AcademicManagement\EnrollmentController;
use App\Http\Controllers\Admin\AcademicManagement\EvaluationCriteriaController;
use App\Http\Controllers\Admin\AcademicManagement\SchoolPhaseController;
use App\Http\Controllers\Admin\AcademicManagement\ShiftController;
use App\Http\Controllers\Admin\AcademicManagement\SubjectController;
use App\Http\Controllers\Admin\Academic\ScheduleController;
use App\Http\Controllers\Admin\Academic\SchoolScheduleController;
use App\Http\Controllers\Admin\AccountingController;
use App\Http\Controllers\Admin\Attendance\DailyAttendanceController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\Finance\AccountingController as FinanceAccountingController;
use App\Http\Controllers\Admin\Finance\PaymentController;
use App\Http\Controllers\Admin\GradeSectionController;
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\Settings\SettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboard;
use App\Http\Controllers\Student\SubmissionController;
use App\Http\Controllers\Teacher\AssignmentController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Teacher\GradeController;
use Illuminate\Support\Facades\Route;

Route::get("/", fn () => redirect("/login"));

Route::get("/login", [LoginController::class, "showLoginForm"])->name("login");
Route::post("/login", [LoginController::class, "login"]);
Route::post("/logout", [LoginController::class, "logout"])->name("logout");

Route::middleware(["auth", "role:admin"])->prefix("admin")->name("admin.")->group(function () {
    Route::get("/dashboard", [AdminDashboard::class, "index"])->name("dashboard");

    Route::resource("students", StudentController::class)->except(["create", "edit"]);
    Route::resource("teachers", TeacherController::class)->except(["create", "edit"]);
    Route::resource("guardians", GuardianController::class)->except(["create", "edit"]);
    Route::resource("courses", CourseController::class)->except(["create", "edit"]);
    Route::resource("grade-sections", GradeSectionController::class)->except(["create", "edit"]);
    Route::resource("users", \App\Http\Controllers\Admin\UserController::class)->except(["create", "edit"]);
    Route::post("/users/{user}/toggle-active", [\App\Http\Controllers\Admin\UserController::class, "toggleActive"])->name("users.toggle-active");
    Route::get("/students/{student}/carnet", [StudentController::class, "carnet"])->name("students.carnet");
    Route::get("/students/{student}/carnet/print", [StudentController::class, "carnet"])->name("students.carnet.print")->whereNumber('student');
    Route::get("/students/{student}/carnet/pdf", [StudentController::class, "carnetPdf"])->name("students.carnet.pdf")->whereNumber('student');

    Route::prefix("academic")->name("academic.")->group(function () {
        Route::resource("years", AcademicYearController::class)->except(["create", "edit"]);
        Route::resource("phases", SchoolPhaseController::class)->except(["create", "edit"]);
        Route::resource("shifts", ShiftController::class)->except(["create", "edit"]);
        Route::resource("subjects", SubjectController::class)->except(["create", "edit"]);
        Route::resource("evaluation-criteria", EvaluationCriteriaController::class)->except(["create", "edit"]);
        Route::get("evaluation-criteria/{evaluationCriterion}/grades", [EvaluationCriteriaController::class, "grades"])->name("evaluation-criteria.grades");
        Route::post("evaluation-criteria/{evaluationCriterion}/grades", [EvaluationCriteriaController::class, "storeGrades"])->name("evaluation-criteria.store-grades");
        Route::post("assessment-criteria/{assessmentCriterion}/grades", [EvaluationCriteriaController::class, "storeGradesByAssessment"])->name("assessment-criteria.store-grades");
        Route::resource("schedules", ScheduleController::class)->except(["create", "edit"]);
        Route::get("school-schedule", [SchoolScheduleController::class, "index"])->name("school-schedule.index");
        Route::put("school-schedule/{schoolSchedule}", [SchoolScheduleController::class, "update"])->name("school-schedule.update");
    });

    Route::resource("enrollments", EnrollmentController::class)->except(["create", "edit"]);
    Route::resource("payments", PaymentController::class)->except(["create", "edit"]);
    Route::get("/payments/export", [PaymentController::class, "export"])->name("payments.export");
    Route::resource("accounting", FinanceAccountingController::class)->except(["create", "edit"]);
    Route::get("/accounting/export", [FinanceAccountingController::class, "export"])->name("accounting.export");
    Route::get("/settings", [SettingController::class, "index"])->name("settings.index");
    Route::put("/settings", [SettingController::class, "update"])->name("settings.update");

    Route::get("/grades", [App\Http\Controllers\Admin\GradeController::class, "index"])->name("grades.index");

    Route::prefix("attendance")->name("attendance.")->group(function () {
        Route::get("/", [DailyAttendanceController::class, "index"])->name("index");
        Route::get("/general", [DailyAttendanceController::class, "general"])->name("general");
        Route::post("/general/registrar", [DailyAttendanceController::class, "registrarGeneral"])->name("registrar-general");
        Route::get("/scanner", [DailyAttendanceController::class, "scanner"])->name("scanner");
        Route::post("/registrar", [DailyAttendanceController::class, "registrar"])->name("registrar");
        Route::get("/manual", [DailyAttendanceController::class, "manual"])->name("manual");
        Route::post("/manual", [DailyAttendanceController::class, "storeManual"])->name("store-manual");
        Route::post("/mark-absences", [DailyAttendanceController::class, "markAbsences"])->name("mark-absences");
    });

    Route::prefix("reports")->name("reports.")->group(function () {
        Route::get("/", [ReportController::class, "index"])->name("index");
        Route::get("/attendance", [ReportController::class, "attendance"])->name("attendance");
        Route::get("/grades", [ReportController::class, "grades"])->name("grades");
        Route::get("/students", [ReportController::class, "students"])->name("students");
        Route::get("/payments", [ReportController::class, "payments"])->name("payments");
        Route::get("/payments/export", [ReportController::class, "exportPayments"])->name("payments.export");
        Route::get("/accounting/export", [ReportController::class, "exportAccounting"])->name("accounting.export");
        Route::get("/grades/export", [ReportController::class, "exportGrades"])->name("grades.export");
        Route::get("/attendance/export", [ReportController::class, "exportAttendance"])->name("attendance.export");
    });
});

Route::middleware(["auth", "role:teacher"])->prefix("teacher")->name("teacher.")->group(function () {
    Route::get("/dashboard", [TeacherDashboard::class, "index"])->name("dashboard");

    Route::get("/courses/{course}/attendance/scanner", [AttendanceController::class, "scanner"])->name("attendance.scanner");
    Route::post("/courses/{course}/attendance/registrar", [AttendanceController::class, "registrar"])->name("attendance.registrar");
    Route::post("/courses/{course}/attendance/marcar-faltas", [AttendanceController::class, "marcarFaltas"])->name("attendance.marcar-faltas");

    Route::get("/courses/{course}/grades", [GradeController::class, "index"])->name("grades.index");
    Route::post("/courses/{course}/grades", [GradeController::class, "store"])->name("grades.store");
    Route::post('/courses/{course}/grades/criteria', [GradeController::class, 'storeCriteria'])->name('grades.criteria.store');
    Route::get("/courses/{course}/grades/import", [GradeController::class, "importForm"])->name("grades.import-form");
    Route::post("/courses/{course}/grades/import", [GradeController::class, "import"])->name("grades.import");
    Route::get("/courses/{course}/grades/template", [GradeController::class, "downloadTemplate"])->name("grades.template");

    Route::get("/courses/{course}/assignments", [AssignmentController::class, "index"])->name("assignments.index");
    Route::post("/courses/{course}/assignments", [AssignmentController::class, "store"])->name("assignments.store");
    Route::get("/assignments/{assignment}/submissions", [AssignmentController::class, "submissions"])->name("assignments.submissions");
    Route::post("/submissions/{submission}/grade", [AssignmentController::class, "grade"])->name("submissions.grade");
});

Route::middleware(["auth", "role:parent"])->prefix("parent")->name("parent.")->group(function () {
    Route::get("/dashboard", [ParentDashboard::class, "index"])->name("dashboard");
    Route::get("/students/{student}/grades", [ParentDashboard::class, "grades"])->name("grades");
    Route::get("/students/{student}/attendance", [ParentDashboard::class, "attendance"])->name("attendance");
});

Route::middleware(["auth", "role:student"])->prefix("student")->name("student.")->group(function () {
    Route::get("/dashboard", [SubmissionController::class, "index"])->name("dashboard");
    Route::post("/assignments/{assignment}/submit", [SubmissionController::class, "store"])->name("assignments.submit");
});
