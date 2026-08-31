@extends('layouts.app')
@section('title', 'Tareas - ' . $course->name)
@section('content')
<h1 class="text-xl font-bold mb-4">Tareas — {{ $course->name }}</h1>

<form method="POST" action="{{ route('teacher.assignments.store', $course) }}" enctype="multipart/form-data"
      class="bg-white rounded shadow p-4 mb-6 space-y-3">
    @csrf
    <input type="text" name="title" required placeholder="Título de la tarea" class="w-full border rounded px-3 py-2">
    <textarea name="description" placeholder="Descripción / instrucciones" class="w-full border rounded px-3 py-2"></textarea>
    <div class="flex gap-4">
        <input type="datetime-local" name="due_date" class="border rounded px-3 py-2">
        <input type="file" name="file" class="border rounded px-3 py-2">
    </div>
    <button class="bg-indigo-600 text-white px-4 py-2 rounded">Publicar tarea</button>
</form>

<div class="space-y-3">
    @foreach($assignments as $a)
    <div class="bg-white rounded shadow p-4 flex justify-between items-center">
        <div>
            <p class="font-semibold">{{ $a->title }}</p>
            <p class="text-sm text-gray-500">Vence: {{ optional($a->due_date)->format('d/m/Y H:i') ?? 'Sin fecha' }} — {{ $a->submissions_count }} entregas</p>
        </div>
        <a href="{{ route('teacher.assignments.submissions', $a) }}" class="text-indigo-600 text-sm">Ver entregas →</a>
    </div>
    @endforeach
</div>
@endsection
