@extends('layouts.app')
@section('title', 'Mis cursos y tareas')
@section('content')
<h1 class="text-xl font-bold mb-4">Mis cursos y tareas</h1>

@foreach($courses as $course)
<div class="bg-white rounded shadow p-4 mb-4">
    <h2 class="font-semibold text-lg mb-2">{{ $course->name }}</h2>
    @forelse($course->assignments as $assignment)
    <div class="border-t pt-3 mt-3">
        <p class="font-medium">{{ $assignment->title }}</p>
        <p class="text-sm text-gray-600">{{ $assignment->description }}</p>
        <p class="text-xs text-gray-400">Vence: {{ optional($assignment->due_date)->format('d/m/Y H:i') ?? 'Sin fecha' }}</p>

        <form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="mt-2 flex gap-2">
            @csrf
            <input type="file" name="file" required class="border rounded px-2 py-1 text-sm">
            <button class="bg-green-600 text-white px-3 py-1 rounded text-sm">Entregar</button>
        </form>
    </div>
    @empty
    <p class="text-sm text-gray-400">Sin tareas publicadas aún.</p>
    @endforelse
</div>
@endforeach
@endsection
