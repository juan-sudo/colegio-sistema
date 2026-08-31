<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGradeSectionRequest;
use App\Http\Requests\Admin\UpdateGradeSectionRequest;
use App\Models\GradeSection;
use Illuminate\Http\Request;

class GradeSectionController extends Controller
{
    public function index(Request $request)
    {
        $gradeSections = GradeSection::query()
            ->when($request->search, fn ($q, $search) => $q->where("name", "like", "%{$search}%")
                ->orWhere("level", "like", "%{$search}%")
                ->orWhere("school_year", "like", "%{$search}%"))
            ->paginate(20);

        return view("admin.grade-sections.index", compact("gradeSections"));
    }

    public function create()
    {
        return view("admin.grade-sections.create");
    }

    public function store(StoreGradeSectionRequest $request)
    {
        $data = $request->validated();

        $gradeSection = GradeSection::create($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Grado/Sección registrado correctamente.', 'gradeSection' => $gradeSection]);
        }

        return redirect()->route("admin.grade-sections.index")->with("success", "Grado/Sección registrado correctamente.");
    }

    public function edit(GradeSection $gradeSection)
    {
        return view("admin.grade-sections.edit", compact("gradeSection"));
    }

    public function update(UpdateGradeSectionRequest $request, GradeSection $gradeSection)
    {
        $data = $request->validated();

        $gradeSection->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Grado/Sección actualizado correctamente.', 'gradeSection' => $gradeSection]);
        }

        return redirect()->route("admin.grade-sections.index")->with("success", "Grado/Sección actualizado correctamente.");
    }

    public function destroy(GradeSection $gradeSection)
    {
        $gradeSection->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Grado/Sección eliminado correctamente.']);
        }

        return back()->with("success", "Grado/Sección eliminado correctamente.");
    }
}
