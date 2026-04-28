<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kwitansi Thermal — {{ strtoupper(substr($payment->id, 0, 8)) }}</title>
    <style>
        /*
         * DomPDF 58mm thermal ruleset.
         *
         * Paper in controller: [0, 0, 158.00, 600]
         * 158pt / 72dpi * 25.4 = ~55.7mm physical page.
         *
         * Strategy:
         *   1. @page {margin:0}  — page edge = PDF canvas edge.
         *   2. body width = 54mm — 54mm of the 55.7mm page = 1.7mm natural gap.
         *   3. .wrapper padding-right = 4mm — extra right guard.
         *   4. ALL widths in % or mm — zero px units.
         *   5. table-layout:fixed + word-wrap on every td/th — overflow impossible.
         *   6. Font 7pt body — keeps IDR currency strings within 50% column.
         */

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
            font-size: 7pt;
            color: #000;
            background: #fff;
            width: 54mm;
            overflow: hidden;
        }

        /* ── SAFETY WRAPPER ───────────────────────────── */
        /*
         * Left 2mm / Right 4mm: asymmetric padding because thermal printers
         * often have a wider dead-zone on the right sprocket side.
         */
        .wrapper {
            width: 100%;
            padding-left: 2mm;
            padding-right: 4mm;
            box-sizing: border-box;
        }

        /* ── UTILITY ──────────────────────────────────── */
        .center  { text-align: center; }
        .right   { text-align: right; }
        .bold    { font-weight: bold; }
        .mt-1    { margin-top: 1mm; }
        .mt-2    { margin-top: 2mm; }

        /* ── HEADER ───────────────────────────────────── */
        .store-name {
            font-size: 9pt;
            font-weight: bold;
            text-align: center;
            word-wrap: break-word;
            overflow-wrap: break-word;
            padding-top: 2mm;
        }
        .store-addr {
            font-size: 6.5pt;
            text-align: center;
            color: #333;
            margin-top: 0.8mm;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .receipt-label {
            font-size: 8.5pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin-top: 1.5mm;
        }
        .receipt-no {
            font-size: 6.5pt;
            text-align: center;
            color: #444;
            word-wrap: break-word;
            overflow-wrap: break-word;
            margin-top: 0.5mm;
        }

        /* ── DIVIDERS ─────────────────────────────────── */
        /*
         * Use border-bottom on a block element, NOT <hr>.
         * DomPDF renders <hr> at inconsistent widths — a <div class="dash">
         * with border-bottom respects the wrapper width exactly.
         */
        .dash {
            border: none;
            border-bottom: 1px dashed #000;
            margin: 1.5mm 0;
            width: 100%;
        }
        .solid {
            border: none;
            border-bottom: 1.5px solid #000;
            margin: 1.5mm 0;
            width: 100%;
        }

        /* ── DATA TABLE ───────────────────────────────── */
        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        td, th {
            font-size: 7pt;
            padding: 0.4mm 0;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Label column: 36% | Separator: 6% | Value: 58% */
        .col-lbl { width: 36%; color: #333; }
        .col-sep { width: 6%;  color: #555; text-align: center; }
        .col-val {
            width: 58%;
            font-weight: bold;
            text-align: right;
            padding-right: 2mm;  /* extra guard so bold text doesn't hug the edge */
        }

        /* Balance table: 50/50 */
        .b-lbl { width: 50%; color: #555; }
        .b-val {
            width: 50%;
            font-weight: bold;
            text-align: right;
            padding-right: 2mm;
        }
        .zero-val  { color: #0a5221; }
        .owing-val { color: #6b1219; }

        /* ── AMOUNT ───────────────────────────────────── */
        .amount-section { text-align: center; margin: 2mm 0 1.5mm; }
        .amount-label   { font-size: 6.5pt; color: #555; letter-spacing: 0.8pt; }
        .amount-value   {
            font-size: 11pt;
            font-weight: bold;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* ── FOOTER ───────────────────────────────────── */
        .footer-txt {
            font-size: 6.5pt;
            color: #555;
            text-align: center;
            line-height: 1.5;
            margin-top: 2mm;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .signature-area { text-align: center; margin-top: 5mm; padding-bottom: 3mm; }
        .sign-space {
            display: inline-block;
            margin-top: 8mm;
            border-top: 1px solid #000;
            min-width: 20mm;
            padding-top: 0.5mm;
            font-size: 7pt;
            font-weight: bold;
        }
        .sign-role { font-size: 6.5pt; color: #555; margin-top: 0.5mm; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- ── HEADER ── --}}
    <div class="store-name">{{ config('app.pesantren_name', 'PP Al-Pondok') }}</div>
    <div class="store-addr">{{ config('app.pesantren_address', 'Jl. Pesantren No. 1') }}</div>
    <div class="dash mt-1"></div>
    <div class="receipt-label">Kwitansi</div>
    <div class="receipt-no">No: {{ strtoupper(substr($payment->id, 0, 8)) }}</div>
    <div class="receipt-no">{{ $payment->created_at?->translatedFormat('d/m/Y H:i') ?? '-' }}</div>
    <div class="dash"></div>

    {{-- ── STUDENT ── --}}
    <table>
        <tr>
            <td class="col-lbl">Santri</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $student?->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-lbl">NIS</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $student?->nis ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-lbl">Mukim</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $student?->residency_status?->getLabel() ?? '-' }}</td>
        </tr>
    </table>
    <div class="dash"></div>

    {{-- ── PAYMENT DETAIL ── --}}
    @if($payment->items->isNotEmpty())
        <table>
            <tr style="text-align: left; font-weight: bold; text-decoration: underline;">
                <td colspan="3">Detail Tagihan:</td>
            </tr>
            @foreach($payment->items as $item)
                <tr>
                    <td colspan="2" style="width: 60%;">
                        {{ $item->bill?->fee?->name ?? 'Tagihan' }}<br>
                        <span style="font-size: 6pt; color: #444;">({{ $item->bill?->period_name ?? '-' }})</span>
                    </td>
                    <td style="width: 40%; text-align: right; vertical-align: middle;">
                        {{ number_format((float)$item->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <table>
            <tr>
                <td class="col-lbl">Biaya</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $payment->bill?->fee?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-lbl">Periode</td>
                <td class="col-sep">:</td>
                <td class="col-val">{{ $payment->bill?->period_name ?? '-' }}</td>
            </tr>
        </table>
    @endif

    <table>
        <tr>
            <td class="col-lbl">Metode</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $payment->method === 'cash' ? 'Tunai' : 'Online' }}</td>
        </tr>
        @if($payment->transaction_id)
        <tr>
            <td class="col-lbl">Ref</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $payment->transaction_id }}</td>
        </tr>
        @endif
    </table>
    <div class="solid"></div>

    {{-- ── AMOUNT ── --}}
    <div class="amount-section">
        <div class="amount-label">TOTAL DIBAYAR</div>
        <div class="amount-value">
            {{ \Illuminate\Support\Number::currency((float) ($payment->amount ?? 0), 'IDR') }}
        </div>
    </div>
    <div class="dash"></div>

    {{-- ── TRANSPARENCY ── --}}
    @php
        $finalAmount = 0;
        $remaining = 0;

        if ($payment->items->isNotEmpty()) {
            $finalAmount = (float) $payment->items->sum('bill.final_amount');
            $remaining = (float) $payment->items->sum('bill.remaining_balance');
        } else {
            $finalAmount = (float) ($payment->bill?->final_amount ?? 0);
            $remaining = (float) ($payment->bill?->remaining_balance ?? 0);
        }
        $remaining = $remaining < 0 ? 0.0 : $remaining;
    @endphp
    <table>
        <tr>
            <td class="b-lbl">Total Kewajiban</td>
            <td class="b-val">
                {{ \Illuminate\Support\Number::currency($finalAmount, 'IDR') }}
            </td>
        </tr>
        <tr>
            <td class="b-lbl">Sisa Tunggakan</td>
            <td class="b-val {{ $remaining <= 0 ? 'zero-val' : 'owing-val' }}">
                {{ \Illuminate\Support\Number::currency($remaining, 'IDR') }}
            </td>
        </tr>
    </table>
    <div class="dash"></div>

    {{-- ── FOOTER ── --}}
    <div class="footer-txt">
        Kwitansi ini sah sebagai bukti bayar.<br>
        Dicetak: {{ now()->translatedFormat('d/m/Y H:i') }}
    </div>

    <div class="signature-area">
        <div class="sign-space">Bendahara</div>
        <div class="sign-role">Petugas Keuangan</div>
    </div>

</div>{{-- /.wrapper --}}
</body>
</html>
