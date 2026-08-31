@extends("layouts.app")
@section("title", "Entregas")
@section("content")
<h1 class="text-xl font-bold mb-4">Entregas - {{ $assignment->title }}</h1>
<a href="{{ route("teacher.assignments.index", $assignment->course) }}" class="text-indigo-600 mb-4 inline-block">&larr; Volver a tareas</a>
<table class="w-full bg-white rounded shadow text-sm mt-2">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Estudiante</th>
            <th class="p-2 text-left">Estado</th>
            <th class="p-2 text-left">Nota</th>
            <th class="p-2 text-left">Retroalimentación</th>
            <th class="p-2 text-left">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($submissions as $submission)
        <tr class="border-t">
            <td class="p-2">{{ $submission->student->fullName() }}</td>
            <td class="p-2">{{ ucfirst($submission->status) }}</td>
            <td class="p-2">{{ $submission->grade ?? "-" }}</td>
            <td class="p-2">{{ $submission->feedback ?? "-" }}</td>
            <td class="p-2">
                <form method="POST" action="{{ route("teacher.submissions.grade", $submission) }}" class="flex items-center gap-2">
                    @csrf
                    <input type="number" name="grade" step="0.01" min="0" max="20" class="border rounded p-1 w-16" value="{{ $submission->grade ?? "" }}" required>
                    <input type="text" name="feedback" class="border rounded p-1 w-32" placeholder="Retroalimentación" value="{{ $submission->feedback ?? "" }}">
                    <button type="submit" class="bg-indigo-600 text-white px-2 py-1 rounded text-xs">Calificar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
