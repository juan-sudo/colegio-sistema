@extends("layouts.app")
@section("title", "Asistencia de " . $student->fullName())
@section("content")
<h1 class="text-xl font-bold mb-4">Asistencia — {{ $student->fullName() }}</h1>
<div class="bg-white rounded shadow p-4">
    <table class="w-full text-sm">
        <thead><tr class="text-left border-b"><th>Fecha</th><th>Estado</th><th>Hora</th><th>Método</th></tr></thead>
        <tbody>
        @foreach($attendances as $a)
            <tr class="border-b">
                <td>{{ $a->date->format("d/m/Y") }}</td>
                <td>{{ ucfirst($a->status) }}</td>
                <td>{{ $a->time_in }}</td>
                <td>{{ $a->method }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $attendances->links() }}
</div>
@endsection
