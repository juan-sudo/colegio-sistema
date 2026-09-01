<?php

namespace App\Http\Controllers\Admin\AcademicManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academic\StoreAcademicYearRequest;
use App\Http\Requests\Admin\Academic\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AcademicYearController extends Controller
{
    public function index(Request $request): Response
    {
        $query = AcademicYear::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"));

        $years = $this->applySort($query, $request, ['name', 'start_date', 'end_date', 'is_current'], 'start_date', 'desc')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render('Admin/Academic/Years/Index', [
            'years' => $years,
            'filters' => [
                'search' => $request->search,
                'per_page' => $this->perPage($request),
                'sort_by' => $request->sort_by,
                'sort_dir' => $request->sort_dir,
            ],
        ]);
    }

    public function store(StoreAcademicYearRequest $request)
    {
        $data = $request->validated();

        if ($data['is_current']) {
            AcademicYear::query()->update(['is_current' => false]);
        }

        AcademicYear::create($data);

        return redirect()->route('admin.academic.years.index')->with('success', 'Año escolar creado correctamente.');
    }

    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear)
    {
        $data = $request->validated();

        if ($data['is_current']) {
            AcademicYear::query()->where('id', '!=', $academicYear->id)->update(['is_current' => false]);
        }

        $academicYear->update($data);

        return redirect()->route('admin.academic.years.index')->with('success', 'Año escolar actualizado correctamente.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return back()->with('success', 'Año escolar eliminado correctamente.');
    }
}
