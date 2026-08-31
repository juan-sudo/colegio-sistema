<?php

namespace App\Http\Controllers\Admin\AcademicManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academic\StoreShiftRequest;
use App\Http\Requests\Admin\Academic\UpdateShiftRequest;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $shifts = Shift::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->paginate(20);

        return view('admin.academic.shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('admin.academic.shifts.create');
    }

    public function store(StoreShiftRequest $request)
    {
        Shift::create($request->validated());

        return redirect()->route('admin.academic.shifts.index')->with('success', 'Turno creado correctamente.');
    }

    public function edit(Shift $shift)
    {
        return view('admin.academic.shifts.edit', compact('shift'));
    }

    public function update(UpdateShiftRequest $request, Shift $shift)
    {
        $shift->update($request->validated());

        return redirect()->route('admin.academic.shifts.index')->with('success', 'Turno actualizado correctamente.');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();

        return back()->with('success', 'Turno eliminado correctamente.');
    }
}
