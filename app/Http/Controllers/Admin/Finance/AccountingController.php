<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Exports\AccountingExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Accounting\StoreAccountingEntryRequest;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountingEntry::query();

        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $entries = $query->orderByDesc('date')->paginate(20);

        $totalIncome = (clone $query)->income()->sum('amount');
        $totalExpense = (clone $query)->expense()->sum('amount');
        $totalFixedCost = (clone $query)->fixedCost()->sum('amount');

        return view('admin.accounting.index', compact('entries', 'totalIncome', 'totalExpense', 'totalFixedCost'));
    }

    public function create()
    {
        return view('admin.accounting.create');
    }

    public function store(StoreAccountingEntryRequest $request)
    {
        AccountingEntry::create($request->validated());

        return redirect()->route('admin.accounting.index')->with('success', 'Asiento contable registrado correctamente.');
    }

    public function edit(AccountingEntry $accountingEntry)
    {
        return view('admin.accounting.edit', compact('accountingEntry'));
    }

    public function update(Request $request, AccountingEntry $accountingEntry)
    {
        $data = $request->validate([
            'type' => 'required|string|max:50',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $accountingEntry->update($data);

        return redirect()->route('admin.accounting.index')->with('success', 'Asiento contable actualizado correctamente.');
    }

    public function destroy(AccountingEntry $accountingEntry)
    {
        $accountingEntry->delete();

        return back()->with('success', 'Asiento contable eliminado correctamente.');
    }

    public function export(Request $request)
    {
        $type = $request->query('type');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        return Excel::download(new AccountingExport($type, $dateFrom, $dateTo), 'contabilidad.xlsx');
    }
}
