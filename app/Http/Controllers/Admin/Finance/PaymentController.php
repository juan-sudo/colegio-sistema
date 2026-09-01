<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Exports\PaymentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Payment\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Payment::with('student');

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payments = $this->applySort($query, $request, ['type', 'amount', 'paid', 'due_date', 'status'], 'due_date', 'desc')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments,
            'students' => Student::with('user')->get(),
            'filters' => $request->only(['type', 'status']) + [
                'per_page' => $this->perPage($request),
                'sort_by' => $request->sort_by,
                'sort_dir' => $request->sort_dir,
            ],
        ]);
    }

    public function store(StorePaymentRequest $request)
    {
        Payment::create($request->validated());

        return redirect()->route('admin.payments.index')->with('success', 'Pago registrado correctamente.');
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:50',
            'due_date' => 'required|date',
            'paid_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $payment->update($data);

        return redirect()->route('admin.payments.index')->with('success', 'Pago actualizado correctamente.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return back()->with('success', 'Pago eliminado correctamente.');
    }

    public function export(Request $request)
    {
        $type = $request->query('type');
        $status = $request->query('status');

        return Excel::download(new PaymentsExport($type, $status), 'pagos.xlsx');
    }
}
