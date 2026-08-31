@extends('layouts.admin')
@section('title', 'Nuevo horario')
@section('content')
<h1 class="text-xl font-bold mb-4">Nuevo horario</h1>
<form method="POST" action="{{ route('admin.academic.schedules.store') }}">
    @csrf
    <div class="bg-white rounded shadow p-4 grid gap-4 max-w-2xl">
        <div>
            <label class="block text-sm font-medium mb-1">Grado/Sección</label>
            <select name="grade_section_id" class="w-full border rounded p-2" required>
                <option value="">Seleccionar...</option>
                @foreach($gradeSections as $gs)
                <option value="{{ $gs->id }}" {{ old('grade_section_id') == $gs->id ? 'selected' : '' }}>{{ $gs->name }}</option>
                @endforeach
            </select>
            @error('grade_section_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Materia</label>
            <select name="subject_id" class="w-full border rounded p-2" required>
                <option value="">Seleccionar...</option>
                @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                @endforeach
            </select>
            @error('subject_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Profesor</label>
            <select name="teacher_id" class="w-full border rounded p-2">
                <option value="">Sin profesor</option>
                @foreach($teachers as $teacher)
                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->fullName() }}</option>
                @endforeach
            </select>
            @error('teacher_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Turno</label>
            <select name="shift_id" class="w-full border rounded p-2">
                <option value="">Sin turno</option>
                @foreach($shifts as $shift)
                <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                @endforeach
            </select>
            @error('shift_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Día</label>
            <select name="day_of_week" class="w-full border rounded p-2" required>
                <option value="">Seleccionar...</option>
                @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] as $day)
                <option value="{{ $day }}" {{ old('day_of_week') == $day ? 'selected' : '' }}>{{ $day }}</option>
                @endforeach
            </select>
            @error('day_of_week')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Inicio</label>
                <input type="time" name="start_time" value="{{ old('start_time') }}" class="w-full border rounded p-2" required>
                @error('start_time')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Fin</label>
                <input type="time" name="end_time" value="{{ old('end_time') }}" class="w-full border rounded p-2" required>
                @error('end_time')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Aula</label>
            <input type="text" name="classroom" value="{{ old('classroom') }}" class="w-full border rounded p-2">
            @error('classroom')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Guardar</button>
    </div>
</form>
@endsection
