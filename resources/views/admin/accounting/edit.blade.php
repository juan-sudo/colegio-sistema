@extends('layouts.admin')
@section('title', 'Editar asiento')
@section('content')
<h1 class="text-xl font-bold mb-4">Editar asiento contable</h1>
<form method="POST" action="{{ route('admin.accounting.update', $accountingEntry) }}">
    @csrf @method('PUT')
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Tipo</label>
            <select name="type" class="w-full border rounded p-2" required>
                <option value="ingreso" {{ old('type', $accountingEntry->type) == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                <option value="egreso" {{ old('type', $accountingEntry->type) == 'egreso' ? 'selected' : '' }}>Egreso</option>
                <option value="gasto_fijo" {{ old('type', $accountingEntry->type) == 'gasto_fijo' ? 'selected' : '' }}>Gasto fijo</option>
            </select>
            @error('type')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Categoría</label>
            <input type="text" name="category" value="{{ old('category', $accountingEntry->category) }}" class="w-full border rounded p-2" required>
            @error('category')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Descripción</label>
            <textarea name="description" class="w-full border rounded p-2">{{ old('description', $accountingEntry->description) }}</textarea>
            @error('description')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Monto</label>
            <input type="number" name="amount" step="0.01" value="{{ old('amount', $accountingEntry->amount) }}" class="w-full border rounded p-2" required>
            @error('amount')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Fecha</label>
            <input type="date" name="date" value="{{ old('date', $accountingEntry->date) }}" class="w-full border rounded p-2" required>
            @error('date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Referencia</label>
            <input type="text" name="reference" value="{{ old('reference', $accountingEntry->reference) }}" class="w-full border rounded p-2">
            @error('reference')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Notas</label>
            <textarea name="notes" class="w-full border rounded p-2">{{ old('notes', $accountingEntry->notes) }}</textarea>
            @error('notes')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Actualizar</button>
    </div>
</form>
@endsection
