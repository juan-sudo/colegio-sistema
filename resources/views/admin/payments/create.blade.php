@extends('layouts.admin')
@section('title', 'Nuevo pago')
@section('content')
<h1 class="text-xl font-bold mb-4">Nuevo pago</h1>
<form method="POST" action="{{ route('admin.payments.store') }}">
    @csrf
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Estudiante</label>
            <select name="student_id" class="w-full border rounded p-2" required>
                <option value="">Seleccionar...</option>
                @foreach($students as $student)
                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->fullName() }} ({{ $student->code }})</option>
                @endforeach
            </select>
            @error('student_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Tipo</label>
            <select name="type" class="w-full border rounded p-2" required>
                <option value="matricula">Matrícula</option>
                <option value="pension">Pensión</option>
            </select>
            @error('type')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Monto</label>
            <input type="number" name="amount" step="0.01" value="{{ old('amount') }}" class="w-full border rounded p-2" required>
            @error('amount')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Descuento</label>
            <input type="number" name="discount" step="0.01" value="{{ old('discount', 0) }}" class="w-full border rounded p-2">
            @error('discount')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Pagado</label>
            <input type="number" name="paid" step="0.01" value="{{ old('paid', 0) }}" class="w-full border rounded p-2">
            @error('paid')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Fecha vencimiento</label>
            <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full border rounded p-2" required>
            @error('due_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Método de pago</label>
            <select name="payment_method" class="w-full border rounded p-2">
                <option value="">Seleccionar...</option>
                <option value="efectivo">Efectivo</option>
                <option value="transferencia">Transferencia</option>
                <option value="tarjeta">Tarjeta</option>
            </select>
            @error('payment_method')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Guardar</button>
    </div>
</form>
@endsection
