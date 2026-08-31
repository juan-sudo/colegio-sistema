@extends('layouts.admin')
@section('title', 'Editar matrícula')
@section('content')
<h1 class="text-xl font-bold mb-4">Editar matrícula</h1>
<form method="POST" action="{{ route('admin.enrollments.update', $enrollment) }}">
    @csrf @method('PUT')
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Estudiante</label>
            <select name="student_id" class="w-full border rounded p-2" required>
                @foreach($students as $student)
                <option value="{{ $student->id }}" {{ old('student_id', $enrollment->student_id) == $student->id ? 'selected' : '' }}>{{ $student->fullName() }} ({{ $student->code }})</option>
                @endforeach
            </select>
            @error('student_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Grado/Sección</label>
            <select name="grade_section_id" class="w-full border rounded p-2" required>
                @foreach($gradeSections as $gs)
                <option value="{{ $gs->id }}" {{ old('grade_section_id', $enrollment->grade_section_id) == $gs->id ? 'selected' : '' }}>{{ $gs->name }} - {{ $gs->level }}</option>
                @endforeach
            </select>
            @error('grade_section_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Año escolar</label>
            <select name="academic_year_id" class="w-full border rounded p-2" required>
                @foreach($academicYears as $year)
                <option value="{{ $year->id }}" {{ old('academic_year_id', $enrollment->academic_year_id) == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                @endforeach
            </select>
            @error('academic_year_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Estado</label>
            <select name="status" class="w-full border rounded p-2" required>
                <option value="matriculado" {{ old('status', $enrollment->status) == 'matriculado' ? 'selected' : '' }}>Matriculado</option>
                <option value="retirado" {{ old('status', $enrollment->status) == 'retirado' ? 'selected' : '' }}>Retirado</option>
                <option value="trasladado" {{ old('status', $enrollment->status) == 'trasladado' ? 'selected' : '' }}>Trasladado</option>
            </select>
            @error('status')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Observaciones</label>
            <textarea name="notes" class="w-full border rounded p-2">{{ old('notes', $enrollment->notes) }}</textarea>
            @error('notes')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Actualizar</button>
    </div>
</form>
@endsection
