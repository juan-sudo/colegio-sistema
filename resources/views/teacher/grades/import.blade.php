@extends("layouts.app")
@section("title", "Importar notas desde Excel")
@section("content")
<h1 class="text-xl font-bold mb-4">Carga masiva de notas — {{ $course->name }}</h1>

<div class="bg-white rounded shadow p-6 max-w-lg">
    <p class="text-sm text-gray-600 mb-4">
        1. Descarga la <a class="text-indigo-600 underline" href="{{ route("teacher.grades.template", $course) }}">plantilla Excel</a>.<br>
        2. Llena la columna "nota" para cada alumno.<br>
        3. Sube el archivo aquí.
    </p>
    <form method="POST" action="{{ route("teacher.grades.import", $course) }}" enctype="multipart/form-data">
        @csrf
        <label class="block text-sm font-medium mb-1">Periodo</label>
        <select name="grade_period_id" required class="w-full border rounded px-3 py-2 mb-4">
            @foreach($course->gradeSection->courses as $c)@endforeach
        </select>
        <label class="block text-sm font-medium mb-1">Archivo Excel (.xlsx)</label>
        <input type="file" name="file" required accept=".xlsx,.xls,.csv" class="w-full border rounded px-3 py-2 mb-4">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 w-full">Importar</button>
    </form>
</div>
@endsection
