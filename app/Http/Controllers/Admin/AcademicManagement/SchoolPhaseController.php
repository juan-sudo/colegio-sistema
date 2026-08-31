<?php

namespace App\Http\Controllers\Admin\AcademicManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academic\StoreSchoolPhaseRequest;
use App\Http\Requests\Admin\Academic\UpdateSchoolPhaseRequest;
use App\Models\SchoolPhase;
use Illuminate\Http\Request;

class SchoolPhaseController extends Controller
{
    public function index(Request $request)
    {
        $phases = SchoolPhase::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('order')
            ->paginate(20);

        return view('admin.academic.phases.index', compact('phases'));
    }

    public function create()
    {
        return view('admin.academic.phases.create');
    }

    public function store(StoreSchoolPhaseRequest $request)
    {
        SchoolPhase::create($request->validated());

        return redirect()->route('admin.academic.phases.index')->with('success', 'Fase escolar creada correctamente.');
    }

    public function edit(SchoolPhase $schoolPhase)
    {
        return view('admin.academic.phases.edit', compact('schoolPhase'));
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
