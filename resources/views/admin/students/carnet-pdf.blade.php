<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carnet - {{ $student->fullName() }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
        }
        .carnet {
            width: 85.6mm;
            height: 54mm;
            padding: 2mm;
            position: relative;
            overflow: hidden;
            page-break-inside: avoid;
            page-break-after: avoid;
        }
        .carnet-front {
            width: 85.6mm;
            height: 54mm;
            border: 1px solid #000;
            padding: 2mm;
            position: relative;
            page-break-inside: avoid;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid #000;
            padding-bottom: 1mm;
            margin-bottom: 1mm;
            page-break-inside: avoid;
        }
        .info h2 {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 0.5mm;
        }
        .info p {
            font-size: 7px;
            margin: 0.3mm 0;
        }
        .photo {
            width: 14mm;
            height: 18mm;
            border: 1px solid #000;
            object-fit: cover;
        }
        .codes {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1mm;
            gap: 2mm;
            page-break-inside: avoid;
        }
        .code-box {
            text-align: center;
        }
        .code-box p {
            font-size: 6px;
            margin-bottom: 0.5mm;
            font-weight: bold;
        }
        .qr-box {
            width: 14mm;
            height: 14mm;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-box img {
            width: 100%;
            height: 100%;
        }
        .barcode-box {
            width: 18mm;
            height: 14mm;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .barcode-box img {
            width: 100%;
            height: 100%;
        }
        .bio-box {
            width: 16mm;
            height: 14mm;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            font-weight: bold;
        }
        .footer {
            position: absolute;
            bottom: 1mm;
            left: 2mm;
            right: 2mm;
            text-align: center;
            font-size: 6px;
            border-top: 1px solid #000;
            padding-top: 0.5mm;
        }
        @page {
            size: 85.6mm 54mm;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="carnet">
        <div class="carnet-front">
            <div class="header">
                <div class="info">
                    <h2>{{ $schoolName }}</h2>
                    <p>{{ $schoolAddress }}</p>
                    <p>{{ $schoolPhone }}</p>
                    <p style="margin-top: 1mm;"><strong>{{ $student->fullName() }}</strong></p>
                    <p>DNI: {{ $student->dni }} · Código: {{ $student->code }}</p>
                    <p>Grado: {{ $student->gradeSection->name ?? '' }}</p>
                </div>
                @if($student->photo)
                <img src="{{ public_path('storage/' . $student->photo) }}" class="photo" alt="Foto">
                @endif
            </div>

            <div class="codes">
                @if($attendanceMethod === 'qr' || $attendanceMethod === 'both')
                <div class="code-box">
                    <p>QR</p>
                    <div class="qr-box">
                        <img src="{{ $qrBase64 }}" alt="QR">
                    </div>
                </div>
                @endif

                @if($attendanceMethod === 'barcode' || $attendanceMethod === 'both')
                <div class="code-box">
                    <p>BARRAS</p>
                    <div class="barcode-box">
                        <img src="{{ $barcodeBase64 }}" alt="Barcode">
                    </div>
                </div>
                @endif

                @if($attendanceMethod === 'biometric')
                <div class="code-box">
                    <p>BIOMÉTRICO</p>
                    <div class="bio-box">
                        {{ $student->biometric_id }}
                    </div>
                </div>
                @endif
            </div>

            <div class="footer">
                {{ $schoolName }}
            </div>
        </div>
    </div>
</body>
</html>