<?php

namespace App\Http\Controllers\Admin\AcademicManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academic\StoreSubjectRequest;
use App\Http\Requests\Admin\Academic\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubjectController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Subject::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('code', 'like', "%{$s}%"));

        $subjects = $this->applySort($query, $request, ['name', 'code'], 'name')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render('Admin/Academic/Subjects/Index', [
            'subjects' => $subjects,
            'filters' => [
                'search' => $request->search,
                'per_page' => $this->perPage($request),
                'sort_by' => $request->sort_by,
                'sort_dir' => $request->sort_dir,
            ],
        ]);
    }

    public function store(StoreSubjectRequest $request)
    {
        Subject::create($request->validated());

        return redirect()->route('admin.academic.subjects.index')->with('success', 'Materia creada correctamente.');
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
