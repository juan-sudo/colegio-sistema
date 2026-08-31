@extends('layouts.admin')
@section("title", "Nuevo grado/sección")
@section("content")
<h1 class="text-xl font-bold mb-4">Nuevo grado/sección</h1>
<form method="POST" action="{{ route("admin.grade-sections.store") }}">
    @csrf
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="name" value="{{ old("name") }}" class="w-full border rounded p-2" required>
            @error("name")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nivel</label>
            <input type="text" name="level" value="{{ old("level") }}" class="w-full border rounded p-2" required>
            @error("level")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Año escolar</label>
            <input type="text" name="school_year" value="{{ old("school_year") }}" class="w-full border rounded p-2" required>
            @error("school_year")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Guardar</button>
    </div>
</form>
@endsection
