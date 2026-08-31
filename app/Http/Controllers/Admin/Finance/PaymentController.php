<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Exports\PaymentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Payment\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    public function index(Request $request)
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

        $payments = $query->orderByDesc('due_date')->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        return view('admin.payments.create', [
            'students' => Student::with('user')->get(),
        ]);
    }

    public function store(StorePaymentRequest $request)
    {
        Payment::create($request->validated());

        return redirect()->route('admin.payments.index')->with('success', 'Pago registrado correctamente.');
    }

    public function edit(Payment $payment)
    {
        return view('admin.payments.edit', [
            'payment' => $payment,
            'students' => Student::with('user')->get(),
        ]);
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
