@extends('layouts.admin')
@section('title', 'Editar año escolar')
@section('content')
<h1 class="text-xl font-bold mb-4">Editar año escolar</h1>
<form method="POST" action="{{ route('admin.academic.years.update', $academicYear) }}">
    @csrf @method('PUT')
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $academicYear->name) }}" class="w-full border rounded p-2" required>
            @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Fecha inicio</label>
            <input type="date" name="start_date" value="{{ old('start_date', $academicYear->start_date) }}" class="w-full border rounded p-2" required>
            @error('start_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Fecha fin</label>
            <input type="date" name="end_date" value="{{ old('end_date', $academicYear->end_date) }}" class="w-full border rounded p-2" required>
            @error('end_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_current" value="1" {{ old('is_current', $academicYear->is_current) ? 'checked' : '' }}>
                <span class="text-sm">Año actual</span>
            </label>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Actualizar</button>
    </div>
</form>
@endsection
