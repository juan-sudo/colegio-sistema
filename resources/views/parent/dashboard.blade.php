@extends("layouts.app")
@section("title", "Panel de padres")
@section("content")
<h1 class="text-xl font-bold mb-4">Mis hijos</h1>
<div class="grid md:grid-cols-3 gap-4">
    @foreach($students as $s)
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold">{{ $s->fullName() }}</h2>
        <p class="text-sm text-gray-500 mb-3">{{ optional($s->gradeSection)->name }}</p>
        <div class="flex flex-col gap-1 text-sm">
            <a class="text-indigo-600" href="{{ route("parent.grades", $s) }}">📝 Ver notas</a>
            <a class="text-indigo-600" href="{{ route("parent.attendance", $s) }}">📋 Ver asistencia</a>
        </div>
    </div>
    @endforeach
</div>
@endsection
