@extends('layouts.admin')
@section("title", "Carnet")
@section("content")
<h1 class="text-xl font-bold mb-4">Carnet de {{ $student->fullName() }}</h1>

@php
    $attendanceMethod = \App\Models\Setting::get('attendance_method', 'qr');
@endphp

<div class="bg-white rounded shadow p-8 max-w-md mx-auto text-center mb-4">
    <h2 class="text-lg font-semibold mb-2">{{ $student->fullName() }}</h2>
    <p class="text-sm text-gray-600 mb-1">DNI: {{ $student->dni }}</p>
    <p class="text-sm text-gray-600 mb-1">Código: {{ $student->code }}</p>

    @if($attendanceMethod === 'qr' || $attendanceMethod === 'both')
        <p class="text-sm text-gray-600 mb-1">QR: {{ $student->qr_token }}</p>
    @endif

    @if($attendanceMethod === 'barcode' || $attendanceMethod === 'both')
        <p class="text-sm text-gray-600 mb-1">Barras: {{ $student->barcode }}</p>
    @endif

    @if($attendanceMethod === 'biometric')
        <p class="text-sm text-gray-600 mb-1">Biométrico: {{ $student->biometric_id }}</p>
    @endif

    <p class="text-sm text-gray-600 mb-4">{{ $student->gradeSection->name ?? "" }}</p>
    @if($student->photo)
    <img src="{{ asset("storage/{$student->photo}") }}" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
    @endif
    <p class="text-xs text-gray-400">Sistema Escolar</p>
</div>

<div class="text-center">
    <button onclick="openPdfModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        🖨️ Imprimir carnet
    </button>
</div>

<div id="pdfModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold">Carnet - {{ $student->fullName() }}</h3>
            <button onclick="closePdfModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-4">
            <iframe id="pdfFrame" src="" class="w-full h-[600px] border-0"></iframe>
        </div>
        <div class="flex justify-end gap-2 p-4 border-t">
            <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                🖨️ Imprimir
            </button>
            <button onclick="closePdfModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
    function openPdfModal() {
        const modal = document.getElementById('pdfModal');
        const iframe = document.getElementById('pdfFrame');
        iframe.src = "{{ route('admin.students.carnet.pdf', $student) }}";
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closePdfModal() {
        const modal = document.getElementById('pdfModal');
        const iframe = document.getElementById('pdfFrame');
        iframe.src = '';
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    @if($showPdf)
    document.addEventListener('DOMContentLoaded', function() {
        openPdfModal();
    });
    @endif
</script>
@endsection