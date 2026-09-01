<?php

namespace App\Http\Controllers\Admin\AcademicManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academic\StoreSchoolPhaseRequest;
use App\Http\Requests\Admin\Academic\UpdateSchoolPhaseRequest;
use App\Models\SchoolPhase;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolPhaseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = SchoolPhase::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"));

        $phases = $this->applySort($query, $request, ['name', 'order'], 'order')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render('Admin/Academic/Phases/Index', [
            'phases' => $phases,
            'filters' => [
                'search' => $request->search,
                'per_page' => $this->perPage($request),
                'sort_by' => $request->sort_by,
                'sort_dir' => $request->sort_dir,
            ],
        ]);
    }

    public function store(StoreSchoolPhaseRequest $request)
    {
        SchoolPhase::create($request->validated());

        return redirect()->route('admin.academic.phases.index')->with('success', 'Fase escolar creada correctamente.');
    }

    public function update(UpdateSchoolPhaseRequest $request, SchoolPhase $schoolPhase)
    {
        $schoolPhase->update($request->validated());

        return redirect()->route('admin.academic.phases.index')->with('success', 'Fase escolar actualizada correctamente.');
    }

    public function destroy(SchoolPhase $schoolPhase)
    {
        $schoolPhase->delete();

        return back()->with('success', 'Fase escolar eliminada correctamente.');
    }
}
