<?php

namespace App\Http\Controllers\Admin\AcademicManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academic\StoreShiftRequest;
use App\Http\Requests\Admin\Academic\UpdateShiftRequest;
use App\Models\Shift;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShiftController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Shift::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"));

        $shifts = $this->applySort($query, $request, ['name', 'start_time', 'end_time'], 'name')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render('Admin/Academic/Shifts/Index', [
            'shifts' => $shifts,
            'filters' => [
                'search' => $request->search,
                'per_page' => $this->perPage($request),
                'sort_by' => $request->sort_by,
                'sort_dir' => $request->sort_dir,
            ],
        ]);
    }

    public function store(StoreShiftRequest $request)
    {
        Shift::create($request->validated());

        return redirect()->route('admin.academic.shifts.index')->with('success', 'Turno creado correctamente.');
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
