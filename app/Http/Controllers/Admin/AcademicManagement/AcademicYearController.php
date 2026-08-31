<?php

namespace App\Http\Controllers\Admin\AcademicManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academic\StoreAcademicYearRequest;
use App\Http\Requests\Admin\Academic\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $years = AcademicYear::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderByDesc('start_date')
            ->paginate(20);

        return view('admin.academic.years.index', compact('years'));
    }

    public function create()
    {
        return view('admin.academic.years.create');
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

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic.years.edit', compact('academicYear'));
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
