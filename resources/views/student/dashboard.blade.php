@extends("layouts.app")
@section("title", "Panel del estudiante")
@section("content")
<h1 class="text-xl font-bold mb-4">Mis tareas</h1>
<div class="grid md:grid-cols-2 gap-4">
    @foreach($courses as $course)
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">{{ $course->name }}</h2>
        <p class="text-sm text-gray-500 mb-3">{{ $course->gradeSection->name }}</p>
        @if($course->assignments->count())
        <ul class="text-sm space-y-1">
            @foreach($course->assignments as $assignment)
            <li class="flex justify-between items-center border-t pt-2">
                <span>{{ $assignment->title }}</span>
                <a href="{{ route("student.assignments.submit", $assignment) }}" class="text-indigo-600">Entregar</a>
            </li>
            @endforeach
        </ul>
        @else
        <p class="text-sm text-gray-400">Sin tareas</p>
        @endif
    </div>
    @endforeach
</div>
@endsection
