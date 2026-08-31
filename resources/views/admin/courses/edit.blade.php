@extends('layouts.admin')
@section("title", "Editar curso")
@section("content")
<h1 class="text-xl font-bold mb-4">Editar curso</h1>
<form method="POST" action="{{ route("admin.courses.update", $course) }}">
    @csrf @method("PUT")
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="name" value="{{ old("name", $course->name) }}" class="w-full border rounded p-2" required>
            @error("name")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Grado/Sección</label>
            <select name="grade_section_id" class="w-full border rounded p-2" required>
                <option value="">Seleccionar...</option>
                @foreach($gradeSections as $gs)
                <option value="{{ $gs->id }}" {{ old("grade_section_id", $course->grade_section_id) == $gs->id ? "selected" : "" }}>{{ $gs->name }} - {{ $gs->level }}</option>
                @endforeach
            </select>
            @error("grade_section_id")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Profesor</label>
            <select name="teacher_id" class="w-full border rounded p-2">
                <option value="">Sin profesor</option>
                @foreach($teachers as $teacher)
                <option value="{{ $teacher->id }}" {{ old("teacher_id", $course->teacher_id) == $teacher->id ? "selected" : "" }}>{{ $teacher->fullName() }}</option>
                @endforeach
            </select>
            @error("teacher_id")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Año escolar</label>
            <input type="text" name="school_year" value="{{ old("school_year", $course->school_year) }}" class="w-full border rounded p-2" required>
            @error("school_year")<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Actualizar</button>
    </div>
</form>
@endsection
