@extends('layouts.admin')
@section('title', 'Editar pago')
@section('content')
<h1 class="text-xl font-bold mb-4">Editar pago</h1>
<form method="POST" action="{{ route('admin.payments.update', $payment) }}">
    @csrf @method('PUT')
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Estudiante</label>
            <select name="student_id" class="w-full border rounded p-2" required>
                @foreach($students as $student)
                <option value="{{ $student->id }}" {{ old('student_id', $payment->student_id) == $student->id ? 'selected' : '' }}>{{ $student->fullName() }} ({{ $student->code }})</option>
                @endforeach
            </select>
            @error('student_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Tipo</label>
            <select name="type" class="w-full border rounded p-2" required>
                <option value="matricula" {{ old('type', $payment->type) == 'matricula' ? 'selected' : '' }}>Matrícula</option>
                <option value="pension" {{ old('type', $payment->type) == 'pension' ? 'selected' : '' }}>Pensión</option>
            </select>
            @error('type')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Monto</label>
            <input type="number" name="amount" step="0.01" value="{{ old('amount', $payment->amount) }}" class="w-full border rounded p-2" required>
            @error('amount')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Descuento</label>
            <input type="number" name="discount" step="0.01" value="{{ old('discount', $payment->discount) }}" class="w-full border rounded p-2">
            @error('discount')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Pagado</label>
            <input type="number" name="paid" step="0.01" value="{{ old('paid', $payment->paid) }}" class="w-full border rounded p-2">
            @error('paid')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Estado</label>
            <select name="status" class="w-full border rounded p-2" required>
                <option value="pendiente" {{ old('status', $payment->status) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="pagado" {{ old('status', $payment->status) == 'pagado' ? 'selected' : '' }}>Pagado</option>
                <option value="vencido" {{ old('status', $payment->status) == 'vencido' ? 'selected' : '' }}>Vencido</option>
            </select>
            @error('status')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Fecha pago</label>
            <input type="date" name="paid_date" value="{{ old('paid_date', $payment->paid_date) }}" class="w-full border rounded p-2">
            @error('paid_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Método de pago</label>
            <select name="payment_method" class="w-full border rounded p-2">
                <option value="">Seleccionar...</option>
                <option value="efectivo" {{ old('payment_method', $payment->payment_method) == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                <option value="transferencia" {{ old('payment_method', $payment->payment_method) == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                <option value="tarjeta" {{ old('payment_method', $payment->payment_method) == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
            </select>
            @error('payment_method')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Actualizar</button>
    </div>
</form>
@endsection
