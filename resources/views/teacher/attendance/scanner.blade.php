@extends('layouts.app')
@section('title', 'Tomar asistencia - ' . $course->name)
@section('content')
<h1 class="text-xl font-bold mb-4">Asistencia — {{ $course->name }} ({{ $course->gradeSection->name }})</h1>

<div class="grid md:grid-cols-2 gap-6">
    {{-- Lector de cámara (QR) --}}
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="font-semibold mb-2">📷 Escanear QR con cámara</h2>
        <div id="qr-reader" style="width: 100%"></div>
    </div>

    {{-- Entrada manual / pistola lectora de código de barras (funciona como teclado) --}}
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="font-semibold mb-2">🔫 Código de barras / código manual</h2>
        <input id="barcode-input" type="text" autofocus placeholder="Escanea o escribe el código y presiona Enter"
               class="w-full border rounded px-3 py-3 text-lg">
        <p class="text-sm text-gray-500 mt-2">
            El lector de huella biométrica también puede enviar el ID capturado a este mismo cuadro
            (o directamente vía la API <code>/api/biometric/marcar</code>).
        </p>
    </div>
</div>

<div id="resultado" class="mt-6"></div>

<div class="mt-6">
    <form method="POST" action="{{ route('teacher.attendance.marcar-faltas', $course) }}"
          onsubmit="return confirm('¿Marcar como falta a todos los alumnos que no registraron asistencia hoy y notificar a sus padres por WhatsApp?')">
        @csrf
        <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
            Marcar faltas del día y notificar por WhatsApp
        </button>
    </form>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const courseId = {{ $course->id }};
const registrarUrl = "{{ route('teacher.attendance.registrar', $course) }}";
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function mostrarResultado(data) {
    const div = document.getElementById("resultado");
    const color = data.status === "presente" ? "green" : (data.status === "tardanza" ? "yellow" : "red");
    div.innerHTML = "<div class='bg-" + color + "-100 border border-" + color + "-300 text-" + color + "-800 rounded p-4'>" +
        "<strong>" + data.student + "</strong> — " + data.status.toUpperCase() + " a las " + data.time +
        "</div>";
}

async function enviarCodigo(code, method) {
    try {
        const res = await fetch(registrarUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken, "Accept": "application/json" },
            body: JSON.stringify({ code: code, method: method })
        });
        const data = await res.json();
        if (data.ok) mostrarResultado(data);
        else alert("Código no encontrado");
    } catch (e) {
        alert("Error al registrar asistencia");
    }
}

// Cámara QR
const qrRegion = new Html5Qrcode("qr-reader");
Html5Qrcode.getCameras().then(function (cameras) {
    if (cameras && cameras.length) {
        qrRegion.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            function (decodedText) { enviarCodigo(decodedText, "qr"); }
        );
    }
}).catch(function () { console.log("No se detectó cámara"); });

// Código de barras / manual (pistola lectora funciona como teclado + Enter)
document.getElementById("barcode-input").addEventListener("keypress", function (e) {
    if (e.key === "Enter" && this.value.trim() !== "") {
        enviarCodigo(this.value.trim(), "barcode");
        this.value = "";
    }
});
</script>
@endsection
