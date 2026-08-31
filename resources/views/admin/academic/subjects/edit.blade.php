@extends('layouts.admin')
@section('title', 'Editar materia')
@section('content')
<h1 class="text-xl font-bold mb-4">Editar materia</h1>
<form method="POST" action="{{ route('admin.academic.subjects.update', $subject) }}">
    @csrf @method('PUT')
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $subject->name) }}" class="w-full border rounded p-2" required>
            @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Código</label>
            <input type="text" name="code" value="{{ old('code', $subject->code) }}" class="w-full border rounded p-2">
            @error('code')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Descripción</label>
            <textarea name="description" class="w-full border rounded p-2">{{ old('description', $subject->description) }}</textarea>
            @error('description')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Actualizar</button>
    </div>
</form>
@endsection
