<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGradeSectionRequest;
use App\Http\Requests\Admin\UpdateGradeSectionRequest;
use App\Models\GradeSection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GradeSectionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = GradeSection::query()
            ->when($request->search, fn ($q, $search) => $q->where("name", "like", "%{$search}%")
                ->orWhere("level", "like", "%{$search}%")
                ->orWhere("school_year", "like", "%{$search}%"));

        $gradeSections = $this->applySort($query, $request, ["name", "level", "school_year"], "name")
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render("Admin/GradeSections/Index", [
            "gradeSections" => $gradeSections,
            "filters" => [
                "search" => $request->search,
                "per_page" => $this->perPage($request),
                "sort_by" => $request->sort_by,
                "sort_dir" => $request->sort_dir,
            ],
        ]);
    }

    public function store(StoreGradeSectionRequest $request)
    {
        GradeSection::create($request->validated());

        return redirect()->route("admin.grade-sections.index")->with("success", "Grado/Sección registrado correctamente.");
    }

    public function update(UpdateGradeSectionRequest $request, GradeSection $gradeSection)
    {
        $gradeSection->update($request->validated());

        return redirect()->route("admin.grade-sections.index")->with("success", "Grado/Sección actualizado correctamente.");
    }

    public function destroy(GradeSection $gradeSection)
    {
        $gradeSection->delete();

        return back()->with("success", "Grado/Sección eliminado correctamente.");
    }
}
