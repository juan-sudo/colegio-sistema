@extends("layouts.app")
@section("title", "Notas de " . $student->fullName())
@section("content")
<h1 class="text-xl font-bold mb-4">Notas — {{ $student->fullName() }}</h1>
@foreach($grades as $courseName => $items)
<div class="bg-white rounded shadow p-4 mb-3">
    <h2 class="font-semibold mb-2">{{ $courseName }}</h2>
    <table class="w-full text-sm">
        <thead><tr class="text-left border-b"><th>Periodo</th><th>Evaluación</th><th>Nota</th></tr></thead>
        <tbody>
        @foreach($items as $g)
            <tr class="border-b"><td>{{ $g->gradePeriod->name }}</td><td>{{ $g->evaluation }}</td><td>{{ $g->score }}</td></tr>
        @endforeach
        </tbody>
    </table>
</div>
@endforeach
@endsection
