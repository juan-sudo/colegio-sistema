@extends('layouts.admin')
@section('title', 'Nuevo asiento')
@section('content')
<h1 class="text-xl font-bold mb-4">Nuevo asiento contable</h1>
<form method="POST" action="{{ route('admin.accounting.store') }}">
    @csrf
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Tipo</label>
            <select name="type" class="w-full border rounded p-2" required>
                <option value="ingreso">Ingreso</option>
                <option value="egreso">Egreso</option>
                <option value="gasto_fijo">Gasto fijo</option>
            </select>
            @error('type')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Categoría</label>
            <input type="text" name="category" value="{{ old('category') }}" class="w-full border rounded p-2" required>
            @error('category')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Descripción</label>
            <textarea name="description" class="w-full border rounded p-2">{{ old('description') }}</textarea>
            @error('description')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Monto</label>
            <input type="number" name="amount" step="0.01" value="{{ old('amount') }}" class="w-full border rounded p-2" required>
            @error('amount')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Fecha</label>
            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full border rounded p-2" required>
            @error('date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Referencia</label>
            <input type="text" name="reference" value="{{ old('reference') }}" class="w-full border rounded p-2">
            @error('reference')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Notas</label>
            <textarea name="notes" class="w-full border rounded p-2">{{ old('notes') }}</textarea>
            @error('notes')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Guardar</button>
    </div>
</form>
@endsection
