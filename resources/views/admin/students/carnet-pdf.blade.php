<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carnet - {{ $student->fullName() }}</title>
    <style>
        /*
         * DomPDF does not honor `box-sizing: border-box`, so border + padding
         * are always added on top of a declared width/height (content-box
         * behavior) — sizes below are pre-shrunk to compensate, otherwise the
         * card silently overflows the page and prints a blank 2nd page.
         */
        * {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            color: #1e293b;
        }
        .carnet {
            width: 54mm;
            height: 85.6mm;
            position: relative;
            overflow: hidden;
        }
        .carnet-front {
            /* 54mm/85.6mm minus 2×(0.6mm border + 3mm padding) per axis */
            width: 46.8mm;
            height: 78.4mm;
            border: 0.6mm solid #1d3f8f;
            padding: 3mm;
            position: relative;
            text-align: center;
        }

        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            
        }
        .crest {
            width: 8mm;
            height: 8mm;
            border-radius: 50%;
            background: #1d3f8f;
            color: #fff;
            font-size: 8px;
            line-height: 8mm;
            font-weight: bold;
            text-align: center;
            border: 0.4mm solid #e08b2f;
        }
        .school-name {
            margin-top: 1mm;
            font-size: 6.5px;
            font-weight: bold;
            line-height: 1.1;
            text-transform: uppercase;
            color: #1d3f8f;
        }

        .photo-row {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            gap: 2mm;
            margin-top: 2mm;
        }
        .photo-frame {
            position: relative;
            width: 24mm;
            height: 24mm;
        }
        .photo-frame img,
        .photo-frame .photo-placeholder {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 2mm;
            background: #eef1f8;
        }
        .photo-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            color: #b7c0d8;
        }
        .corner {
            position: absolute;
            width: 4mm;
            height: 4mm;
            border-color: #e08b2f;
            border-style: solid;
            border-width: 0;
        }
        .corner-tl { top: -1mm; left: -1mm; border-top-width: 0.6mm; border-left-width: 0.6mm; }
        .corner-br { bottom: -1mm; right: -1mm; border-bottom-width: 0.6mm; border-right-width: 0.6mm; }

        .qr-box {
            width: 15mm;
            height: 15mm;
            border: 0.3mm solid #cbd5e1;
            border-radius: 1mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-box img { width: 100%; height: 100%; }

        .name {
            margin-top: 2mm;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.15;
            color: #0f172a;
        }
        .section {
            margin-top: 0.6mm;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            color: #e08b2f;
            letter-spacing: 0.3px;
        }

        .id-row {
            margin-top: 1.5mm;
            font-size: 6px;
            color: #64748b;
        }

        .barcode-box, .bio-box {
            margin: 2mm auto 0;
            width: 40mm;
            height: 10mm;
            border: 0.3mm solid #cbd5e1;
            border-radius: 1mm;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
            font-weight: bold;
        }
        .barcode-box img { width: 100%; height: 100%; }

        @page {
            size: 54mm 85.6mm;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="carnet">
        <div class="carnet-front">
            <div class="header">
                <div class="crest">{{ mb_substr($schoolName, 0, 1) }}</div>
                <div class="school-name">{{ $schoolName }}</div>
            </div>

            <div class="photo-row">
                <div class="photo-frame">
                    @if($student->photo)
                        <img src="{{ public_path('storage/' . $student->photo) }}" alt="Foto">
                    @else
                        <div class="photo-placeholder">{{ mb_substr($student->first_name, 0, 1) }}{{ mb_substr($student->last_name, 0, 1) }}</div>
                    @endif
                    <span class="corner corner-tl"></span>
                    <span class="corner corner-br"></span>
                </div>

                @if($attendanceMethod === 'qr' || $attendanceMethod === 'both')
                    <div class="qr-box">
                        <img src="{{ $qrBase64 }}" alt="QR">
                    </div>
                @endif
            </div>

            <div class="name">{{ $student->first_name }}<br>{{ $student->last_name }}</div>
            <div class="section">{{ $student->gradeSection->name ?? '' }}{{ $student->gradeSection?->level ? ' - ' . $student->gradeSection->level : '' }}</div>

            <div class="id-row">DNI {{ $student->dni }} · Código {{ $student->code }}</div>

            @if($attendanceMethod === 'barcode' || $attendanceMethod === 'both')
                <div class="barcode-box">
                    <img src="{{ $barcodeBase64 }}" alt="Barcode">
                </div>
            @endif

            @if($attendanceMethod === 'biometric')
                <div class="bio-box">Biométrico: {{ $student->biometric_id }}</div>
            @endif
        </div>
    </div>
</body>
</html>
