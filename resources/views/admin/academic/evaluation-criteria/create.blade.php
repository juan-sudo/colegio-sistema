@extends('layouts.admin')
@section('title', 'Nuevo criterio')
@section('content')
<h1 class="text-xl font-bold mb-4">Nuevo criterio de evaluación</h1>
<form method="POST" action="{{ route('admin.academic.evaluation-criteria.store') }}">
    @csrf
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded p-2" required>
            @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Descripción</label>
            <textarea name="description" class="w-full border rounded p-2">{{ old('description') }}</textarea>
            @error('description')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Guardar</button>
    </div>
</form>
@endsection
