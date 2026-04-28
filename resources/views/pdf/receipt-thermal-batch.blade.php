<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Struk #{{ $transactionId }}</title>
    <style>
        @page { 
            size: 58mm auto; 
            margin: 0; 
        }
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        body {
            font-family: 'Courier', 'DejaVu Sans Mono', monospace;
            font-size: 8pt;
            color: #000;
            background: #fff;
            width: 58mm;
            margin: 0;
            padding: 0;
        }
        .container { 
            width: 50mm; 
            margin: 0 auto; 
            padding: 2mm 1mm;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        
        .header-logo { 
            display: inline-block;
            max-width: 30mm; 
            max-height: 15mm; 
            object-fit: contain;
        }
        .institution-name { font-size: 9pt; font-weight: bold; text-align: center; text-transform: uppercase; }
        .institution-addr { font-size: 7.5pt; text-align: center; margin-top: 1mm; line-height: 1.2; }
        
        .title { font-size: 9pt; font-weight: bold; text-align: center; margin-top: 3mm; text-transform: uppercase; }
        
        .dash { border-bottom: 1px dashed #000; margin: 2mm 0; width: 100%; }
        .solid { border-bottom: 1.5px solid #000; margin: 2mm 0; width: 100%; }
        
        table { width: 100%; table-layout: fixed; border-collapse: collapse; }
        td { font-size: 8pt; padding: 0.8mm 0; vertical-align: top; word-wrap: break-word; }
        .label { width: 35%; }
        .sep { width: 5%; text-align: center; }
        .val { width: 60%; font-weight: bold; text-align: right; }
        
        .item-row td { padding: 1mm 0; }
        .item-name { width: 55%; }
        .item-amount { width: 45%; text-align: right; font-weight: bold; }

        .total-label { width: 55%; text-align: right; padding-right: 2mm; }
        .total-val { width: 45%; text-align: right; font-weight: bold; }
        
        .terbilang { font-size: 7pt; font-style: italic; text-align: center; margin: 2mm 0; line-height: 1.3; }
        .footer-msg { font-size: 7.5pt; text-align: center; margin-top: 4mm; line-height: 1.4; }
        
        .signature { text-align: center; margin-top: 6mm; }
        .sign-line { border-top: 1px solid #000; display: inline-block; min-width: 30mm; margin-top: 10mm; font-weight: bold; font-size: 8pt; }
    </style>
</head>
<body>
    @php
        $logoSrc = null;
        if ($settings->logo_path) {
            $logoPath = public_path('storage/' . $settings->logo_path);
            if (file_exists($logoPath)) {
                try {
                    // Programmatic conversion to monochrome B&W using GD
                    $info = getimagesize($logoPath);
                    $mime = $info['mime'];
                    
                    // Create image from file
                    $img = match($mime) {
                        'image/jpeg' => imagecreatefromjpeg($logoPath),
                        'image/png'  => imagecreatefrompng($logoPath),
                        'image/gif'  => imagecreatefromgif($logoPath),
                        default      => null,
                    };

                    if ($img) {
                        // 1. Convert to grayscale
                        imagefilter($img, IMG_FILTER_GRAYSCALE);
                        // 2. Boost contrast to push towards pure B&W
                        imagefilter($img, IMG_FILTER_CONTRAST, -50); // GD contrast: negative is more contrast
                        
                        // Output to buffer
                        ob_start();
                        imagepng($img);
                        $buffer = ob_get_clean();
                        $logoSrc = 'data:image/png;base64,' . base64_encode($buffer);
                        imagedestroy($img);
                    }
                } catch (\Exception $e) {
                    $logoSrc = null; // Fallback to no logo on error
                }
            }
        }
    @endphp
    <div class="container">
        {{-- Header centered via wrapper --}}
        @if($logoSrc)
            <div style="text-align: center; margin-bottom: 2mm;">
                <img src="{{ $logoSrc }}" class="header-logo">
            </div>
        @endif
        <div class="institution-name">{{ $settings->pesantren_name ?? 'ERP Pesantren' }}</div>
        <div class="institution-addr">{{ $settings->address ?? '' }}</div>
        
        <div class="dash"></div>
        <div class="title">Struk Pembayaran</div>
        <div class="center" style="font-size: 7.5pt; margin-top: 1mm;">
            ID: {{ $transactionId }}<br>
            {{ $date->translatedFormat('d/m/Y H:i') }}
        </div>
        <div class="dash"></div>

        {{-- Metadata --}}
        <table>
            <tr>
                <td class="label">Santri</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->full_name }}</td>
            </tr>
            <tr>
                <td class="label">NIS</td>
                <td class="sep">:</td>
                <td class="val">{{ $student->nis ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Metode</td>
                <td class="sep">:</td>
                <td class="val">{{ strtoupper($payment->method) }}</td>
            </tr>
        </table>
        
        <div class="dash"></div>

        {{-- Items --}}
        <table>
            @foreach($payments as $p)
                @if($p->items->isNotEmpty())
                    @foreach($p->items as $item)
                        <tr class="item-row">
                            <td class="item-name">
                                {{ $item->bill?->fee?->name ?? 'Tagihan' }}<br>
                                <span style="font-size: 7pt;">({{ $item->bill?->period_name ?? '-' }})</span>
                            </td>
                            <td class="item-amount">
                                Rp{{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="item-row">
                        <td class="item-name">
                            {{ $p->bill?->fee?->name ?? 'Tagihan' }}<br>
                            <span style="font-size: 7pt;">({{ $p->bill?->period_name ?? '-' }})</span>
                        </td>
                        <td class="item-amount">
                            Rp{{ number_format($p->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
            @endforeach
        </table>

        <div class="solid"></div>

        {{-- Calculations --}}
        <table>
            <tr>
                <td class="total-label">TOTAL :</td>
                <td class="total-val">Rp{{ number_format($totalPaid, 0, ',', '.') }}</td>
            </tr>
            @if($change > 0)
            <tr>
                <td class="total-label">BAYAR :</td>
                <td class="total-val">Rp{{ number_format($received, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="total-label">KEMBALI:</td>
                <td class="total-val">Rp{{ number_format($change, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>

        <div class="dash"></div>
        
        <div class="terbilang">
            "{{ ucwords(App\Helpers\NumberHelper::terbilang($totalPaid)) }} Rupiah"
        </div>

        <div class="footer-msg">
            Terima kasih atas pembayarannya.<br>
            Barakallahu fiikum.
        </div>

        <div class="signature">
            <div class="sign-line">{{ $bendahara }}</div>
            <div style="font-size: 7pt;">Kasir</div>
        </div>
        
        {{-- Extra space for physical tear-off --}}
        <div style="height: 15mm;"></div>
    </div>
</body>
</html>