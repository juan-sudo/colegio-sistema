<?php

namespace App\Http\Controllers\Admin\AcademicManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academic\StoreSubjectRequest;
use App\Http\Requests\Admin\Academic\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $subjects = Subject::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('code', 'like', "%{$s}%"))
            ->paginate(20);

        return view('admin.academic.subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('admin.academic.subjects.create');
    }

    public function store(StoreSubjectRequest $request)
    {
        Subject::create($request->validated());

        return redirect()->route('admin.academic.subjects.index')->with('success', 'Materia creada correctamente.');
    }

    public function edit(Subject $subject)
    {
        return view('admin.academic.subjects.edit', compact('subject'));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        $subject->update($request->validated());

        return redirect()->route('admin.academic.subjects.index')->with('success', 'Materia actualizada correctamente.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return back()->with('success', 'Materia eliminada correctamente.');
    }
}
