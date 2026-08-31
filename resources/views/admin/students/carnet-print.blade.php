<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnet - {{ $student->fullName() }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #carnet-print, #carnet-print * {
                visibility: visible;
            }
            #carnet-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            @page {
                size: 85.6mm x 54mm;
                margin: 0;
            }
        }
        .carnet-size {
            width: 85.6mm;
            min-height: 54mm;
        }
    </style>
</head>
<body class="bg-gray-100 p-4">
    <div class="mb-4 print:hidden">
        <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            🖨️ Imprimir carnet
        </button>
        <a href="{{ route('admin.students.carnet', $student) }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 ml-2">
            ← Volver
        </a>
    </div>

    <div id="carnet-print" class="carnet-size bg-white rounded-lg shadow-lg p-4 mx-auto">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <h2 class="text-lg font-bold text-gray-900">{{ $student->fullName() }}</h2>
                <p class="text-xs text-gray-600">DNI: {{ $student->dni }}</p>
                <p class="text-xs text-gray-600">Código: {{ $student->code }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $student->gradeSection->name ?? '' }}</p>
            </div>
            @if($student->photo)
            <img src="{{ asset("storage/{$student->photo}") }}" class="w-16 h-16 rounded-full object-cover ml-2">
            @endif
        </div>

        <div class="border-t pt-3 mt-3">
            <div class="flex items-center justify-between">
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-700 mb-1">QR</p>
                    <div id="qrcode" class="bg-white p-1 border rounded"></div>
                </div>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-700 mb-1">Barras</p>
                    <svg id="barcode" class="bg-white p-1 border rounded"></svg>
                </div>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-700 mb-1">Biométrico</p>
                    <div class="bg-gray-100 border rounded p-2">
                        <p class="text-sm font-mono font-bold text-gray-800">{{ $student->biometric_id }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 pt-2 border-t text-center">
            <p class="text-xs text-gray-500">Sistema Escolar</p>
        </div>
    </div>

    <script>
        // Generar QR
        const qrCode = new QRCode(document.getElementById("qrcode"), {
            text: "{{ $student->qr_token }}",
            width: 80,
            height: 80,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });

        // Generar código de barras
        JsBarcode("#barcode", "{{ $student->barcode }}", {
            format: "CODE128",
            width: 1.5,
            height: 40,
            displayValue: true,
            fontSize: 10,
            margin: 2
        });
    </script>
</body>
</html>