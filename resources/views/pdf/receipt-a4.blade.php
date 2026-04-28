<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kwitansi A4 — #{{ strtoupper(substr($payment->id, 0, 8)) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #fff;
            padding: 36px 40px;
        }

        /* ── HEADER ─────────────────────────────────── */
        .header {
            display: table;
            width: 100%;
            border-bottom: 3px double #0f3460;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .header-text { display: table-cell; vertical-align: middle; }
        .pesantren-name {
            font-size: 20px;
            font-weight: bold;
            color: #0f3460;
            letter-spacing: 0.5px;
        }
        .pesantren-sub {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }
        .receipt-meta {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }
        .receipt-title {
            font-size: 22px;
            font-weight: bold;
            color: #0f3460;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .receipt-number { font-size: 9px; color: #777; margin-top: 3px; }
        .receipt-date   { font-size: 9px; color: #777; margin-top: 2px; }

        /* ── STATUS BADGE ─────────────────────────────── */
        .badge-confirmed {
            display: inline-block;
            margin-top: 5px;
            padding: 3px 12px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ── SECTION TITLE ───────────────────────────── */
        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #0f3460;
            border-left: 4px solid #0f3460;
            padding-left: 8px;
            margin: 18px 0 10px;
        }

        /* ── INFO TABLE ──────────────────────────────── */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 5px 8px; vertical-align: top; font-size: 11px; }
        .info-table tr:nth-child(even) td { background: #f5f7fc; }
        .info-table .lbl  { width: 33%; color: #555; }
        .info-table .sep  { width: 3%;  color: #999; }
        .info-table .val  { font-weight: bold; color: #1a1a2e; }

        /* ── AMOUNT BOX ──────────────────────────────── */
        .amount-box {
            background: linear-gradient(135deg, #0f3460, #16213e);
            color: #fff;
            border-radius: 8px;
            padding: 16px 20px;
            margin-top: 20px;
            display: table;
            width: 100%;
        }
        .amount-lbl { display: table-cell; font-size: 11px; opacity: 0.75; vertical-align: middle; }
        .amount-val { display: table-cell; text-align: right; font-size: 20px; font-weight: bold; vertical-align: middle; letter-spacing: 0.5px; }

        /* ── SUMMARY GRID ────────────────────────────── */
        .summary-grid {
            display: table;
            width: 100%;
            margin-top: 12px;
            border: 1px solid #dde3f0;
            border-radius: 6px;
        }
        .summary-cell {
            display: table-cell;
            width: 50%;
            padding: 10px 16px;
        }
        .summary-cell:first-child { border-right: 1px solid #dde3f0; }
        .s-label { font-size: 8px; color: #999; text-transform: uppercase; letter-spacing: 0.8px; }
        .s-value { font-size: 13px; font-weight: bold; margin-top: 3px; }
        .s-value.paid   { color: #155724; }
        .s-value.unpaid { color: #842029; }
        .s-value.zero   { color: #155724; }

        /* ── FOOTER ──────────────────────────────────── */
        .footer {
            display: table;
            width: 100%;
            margin-top: 36px;
            padding-top: 16px;
            border-top: 1px dashed #ccc;
        }
        .footer-note { display: table-cell; width: 60%; vertical-align: bottom; }
        .footer-note p { font-size: 8.5px; color: #999; line-height: 1.7; }
        .footer-sign { display: table-cell; width: 40%; text-align: center; vertical-align: top; }
        .sign-date  { font-size: 9px; color: #666; }
        .sign-line  { margin-top: 44px; border-top: 1px solid #1a1a2e; padding-top: 5px; font-size: 10.5px; font-weight: bold; display: inline-block; min-width: 130px; }
        .sign-role  { font-size: 8.5px; color: #777; margin-top: 2px; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-text">
            <div class="pesantren-name">{{ config('app.pesantren_name', 'Pondok Pesantren') }}</div>
            <div class="pesantren-sub">{{ config('app.pesantren_address', 'Jl. Pesantren No. 1') }}</div>
        </div>
        <div class="receipt-meta">
            <div class="receipt-title">Kwitansi</div>
            <div class="receipt-number">No: {{ strtoupper(substr($payment->id, 0, 8)) }}</div>
            <div class="receipt-date">{{ $payment->created_at?->translatedFormat('d F Y') }}</div>
            <div class="badge-confirmed">Pembayaran Dikonfirmasi</div>
        </div>
    </div>

    {{-- STUDENT INFO --}}
    <div class="section-title">Informasi Santri</div>
    <table class="info-table">
        <tr>
            <td class="lbl">Nama Santri</td>
            <td class="sep">:</td>
            <td class="val">{{ $student?->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">NIS</td>
            <td class="sep">:</td>
            <td class="val">{{ $student?->nis ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Status Mukim</td>
            <td class="sep">:</td>
            <td class="val">{{ $student?->residency_status?->getLabel() ?? '-' }}</td>
        </tr>
    </table>

    {{-- PAYMENT DETAIL --}}
    <div class="section-title">Detail Pembayaran</div>
    @if($payment->items->isNotEmpty())
        {{-- Table for Bulk Payments --}}
        <table class="info-table" style="margin-top: 5px; border: 1px solid #dde3f0;">
            <thead>
                <tr style="background: #0f3460; color: white;">
                    <th style="padding: 10px; text-align: left; font-size: 10px; border-radius: 4px 0 0 0;">Jenis Biaya / Deskripsi</th>
                    <th style="padding: 10px; text-align: right; font-size: 10px; border-radius: 0 4px 0 0;">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment->items as $item)
                    <tr>
                        <td style="padding: 8px 10px; border-bottom: 1px solid #eee;">
                            <div style="font-weight: bold; color: #1a1a2e;">{{ $item->bill?->fee?->name ?? 'Tagihan' }}</div>
                            <div style="font-size: 9px; color: #666; margin-top: 2px;">Periode: {{ $item->bill?->period_name ?? 'Sekali Bayar' }}</div>
                        </td>
                        <td style="padding: 8px 10px; text-align: right; font-weight: bold; border-bottom: 1px solid #eee; vertical-align: middle;">
                            {{ \Illuminate\Support\Number::currency((float) $item->amount, 'IDR') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        {{-- Standard View for Single Payments --}}
        <table class="info-table">
            <tr>
                <td class="lbl">Jenis Biaya</td>
                <td class="sep">:</td>
                <td class="val">{{ $payment->bill?->fee?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="lbl">Periode</td>
                <td class="sep">:</td>
                <td class="val">{{ $payment->bill?->period_name ?? '-' }}</td>
            </tr>
        </table>
    @endif

    <table class="info-table" style="margin-top: 10px;">
        <tr>
            <td class="lbl">Metode Pembayaran</td>
            <td class="sep">:</td>
            <td class="val">
                @if($payment->method === 'cash')
                    Tunai (Cash)
                @elseif($payment->method === 'midtrans')
                    Midtrans (Online)
                @else
                    Transfer / Lainnya
                @endif
            </td>
        </tr>
        @if($payment->transaction_id)
        <tr>
            <td class="lbl">ID Transaksi</td>
            <td class="sep">:</td>
            <td class="val">{{ $payment->transaction_id }}</td>
        </tr>
        @endif
        <tr>
            <td class="lbl">Tanggal & Waktu Bayar</td>
            <td class="sep">:</td>
            <td class="val">{{ $payment->created_at?->translatedFormat('d F Y, H:i') ?? '-' }}</td>
        </tr>
    </table>

    {{-- AMOUNT --}}
    <div class="amount-box">
        <div class="amount-lbl">Total Pembayaran</div>
        <div class="amount-val">
            {{ \Illuminate\Support\Number::currency((float) ($payment->amount ?? 0), 'IDR') }}
        </div>
    </div>

    {{-- TRANSPARENCY --}}
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
    @endphp
    <div class="summary-grid">
        <div class="summary-cell">
            <div class="s-label">Total Kewajiban</div>
            <div class="s-value">
                {{ \Illuminate\Support\Number::currency($finalAmount, 'IDR') }}
            </div>
        </div>
        <div class="summary-cell">
            <div class="s-label">Sisa Tunggakan (Setelah Transaksi Ini)</div>
            <div class="s-value {{ $remaining <= 0 ? 'paid' : 'unpaid' }}">
                {{ \Illuminate\Support\Number::currency($remaining < 0 ? 0.0 : $remaining, 'IDR') }}
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-note">
            <p>
                * Kwitansi ini merupakan bukti pembayaran yang sah.<br>
                * Harap simpan kwitansi ini sebagai bukti pelunasan.<br>
                * Dokumen dicetak secara otomatis oleh sistem pada: {{ now()->translatedFormat('d F Y, H:i') }}
            </p>
        </div>
        <div class="footer-sign">
            <div class="sign-date">{{ now()->translatedFormat('d F Y') }}</div>
            <br>
            <div class="sign-line">Bendahara</div>
            <div class="sign-role">Petugas Keuangan</div>
        </div>
    </div>

</body>
</html>
