<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Tagihan — {{ $student->full_name }}</title>
    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans Mono', 'Courier New', Courier, monospace;
            font-size: 8pt;
            color: #000;
            background: #fff;
            width: 54mm; /* Adjusted for 58mm paper */
            padding: 2mm 4mm 2mm 2mm; /* Safety zones */
            overflow-x: hidden;
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .mt-1 { margin-top: 1mm; }
        .mt-2 { margin-top: 2mm; }

        .header {
            border-bottom: 1px dashed #000;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }

        .pesantren-name {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .student-info {
            margin-bottom: 2mm;
            font-size: 7.5pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2mm;
        }

        th {
            border-bottom: 1px solid #000;
            text-align: left;
            padding-bottom: 1mm;
            font-size: 7pt;
        }

        td {
            padding: 1mm 0;
            vertical-align: top;
            font-size: 7.5pt;
        }

        .total-section {
            border-top: 1.5px solid #000;
            padding-top: 2mm;
            margin-top: 1mm;
        }

        .footer {
            margin-top: 4mm;
            font-size: 7pt;
            text-align: center;
        }

        .signature {
            margin-top: 6mm;
            text-align: center;
        }

        .sign-line {
            margin-top: 10mm;
            border-top: 1px solid #000;
            display: inline-block;
            width: 25mm;
        }

        @media print {
            body { width: 54mm; }
        }
    </style>
</head>
<body>
    <div class="header center">
        <div class="pesantren-name">{{ config('app.pesantren_name', 'PP Al-Pondok') }}</div>
        <div class="mt-1" style="font-size: 7pt;">RINCIAN TAGIHAN SANTRI</div>
    </div>

    <div class="student-info">
        <div><strong>Nama:</strong> {{ $student->full_name }}</div>
        <div><strong>NIS:</strong> {{ $student->nis ?? '-' }}</div>
        <div style="font-size: 7pt; color: #444;">Dicetak: {{ now()->translatedFormat('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 60%">Biaya</th>
                <th style="width: 40%" class="right">Sisa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bills as $bill)
                <tr>
                    <td>
                        {{ $bill->fee?->name ?? 'Tagihan' }}<br>
                        <small style="font-size: 6.5pt; color: #666;">{{ $bill->period_name }}</small>
                    </td>
                    <td class="right">
                        {{ number_format((float) $bill->remaining_balance, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div style="display: flex; justify-content: space-between; font-weight: bold;">
            <span>TOTAL:</span>
            <span>Rp {{ number_format((float) $total, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        <p class="bold">Harap segera dilunasi.</p>
        <p>Jazakumullah Khairan.</p>
    </div>

    <div class="signature">
        <p>Bendahara,</p>
        <div class="sign-line"></div>
        <p style="font-size: 6pt; margin-top: 1mm;">( {{ auth()->user()->name ?? 'Petugas Keuangan' }} )</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</body>
</html>
