@extends('layouts.admin')
@section('title', 'Editar turno')
@section('content')
<h1 class="text-xl font-bold mb-4">Editar turno</h1>
<form method="POST" action="{{ route('admin.academic.shifts.update', $shift) }}">
    @csrf @method('PUT')
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $shift->name) }}" class="w-full border rounded p-2" required>
            @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Hora inicio</label>
            <input type="time" name="start_time" value="{{ old('start_time', $shift->start_time) }}" class="w-full border rounded p-2">
            @error('start_time')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Hora fin</label>
            <input type="time" name="end_time" value="{{ old('end_time', $shift->end_time) }}" class="w-full border rounded p-2">
            @error('end_time')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Actualizar</button>
    </div>
</form>
@endsection
