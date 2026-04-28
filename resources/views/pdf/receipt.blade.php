<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kwitansi #{{ $payment->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #ffffff;
            padding: 20px 24px;
        }

        /* ── Header ─────────────────────────────────── */
        .header {
            display: table;
            width: 100%;
            border-bottom: 2.5px solid #16213e;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header-logo {
            display: table-cell;
            width: 60px;
            vertical-align: middle;
        }
        .header-logo img {
            width: 52px;
            height: 52px;
        }
        .header-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 10px;
        }
        .pesantren-name {
            font-size: 16px;
            font-weight: bold;
            color: #16213e;
            letter-spacing: 0.5px;
        }
        .pesantren-subtitle {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }
        .receipt-label {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 140px;
        }
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f3460;
            letter-spacing: 1px;
        }
        .receipt-number {
            font-size: 8px;
            color: #777;
            margin-top: 3px;
            word-break: break-all;
        }

        /* ── Status Badge ────────────────────────────── */
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-top: 4px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* ── Section Title ───────────────────────────── */
        .section-title {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f3460;
            border-left: 3px solid #0f3460;
            padding-left: 6px;
            margin-bottom: 7px;
            margin-top: 12px;
        }

        /* ── Info Table ──────────────────────────────── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 10.5px;
        }
        .info-table .label {
            width: 36%;
            color: #555;
            font-weight: normal;
        }
        .info-table .colon {
            width: 4%;
            color: #555;
        }
        .info-table .value {
            font-weight: bold;
            color: #1a1a2e;
        }
        .info-table tr:nth-child(even) td {
            background: #f8f9fc;
        }

        /* ── Amount Highlight ────────────────────────── */
        .amount-box {
            background: #0f3460;
            color: #ffffff;
            border-radius: 6px;
            padding: 10px 14px;
            margin-top: 14px;
            display: table;
            width: 100%;
        }
        .amount-box-label {
            display: table-cell;
            font-size: 10px;
            vertical-align: middle;
            opacity: 0.8;
        }
        .amount-box-value {
            display: table-cell;
            text-align: right;
            font-size: 15px;
            font-weight: bold;
            vertical-align: middle;
            letter-spacing: 0.5px;
        }

        /* ── Summary Grid ────────────────────────────── */
        .summary-row {
            display: table;
            width: 100%;
            margin-top: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
        }
        .summary-cell {
            display: table-cell;
            width: 50%;
            padding: 8px 12px;
            vertical-align: top;
        }
        .summary-cell:first-child {
            border-right: 1px solid #e0e0e0;
        }
        .summary-cell .s-label {
            font-size: 8.5px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.7px;
        }
        .summary-cell .s-value {
            font-size: 11.5px;
            font-weight: bold;
            margin-top: 2px;
        }
        .s-value.paid   { color: #155724; }
        .s-value.unpaid { color: #842029; }

        /* ── Divider ─────────────────────────────────── */
        .divider {
            border: none;
            border-top: 1px dashed #ccc;
            margin: 14px 0;
        }

        /* ── Footer ──────────────────────────────────── */
        .footer {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .footer-left {
            display: table-cell;
            width: 65%;
            vertical-align: bottom;
        }
        .footer-note {
            font-size: 8px;
            color: #888;
            line-height: 1.5;
        }
        .footer-right {
            display: table-cell;
            width: 35%;
            text-align: center;
            vertical-align: top;
        }
        .signature-date {
            font-size: 9px;
            color: #555;
        }
        .signature-name {
            margin-top: 40px;
            font-size: 10px;
            font-weight: bold;
            border-top: 1px solid #1a1a2e;
            padding-top: 4px;
            display: inline-block;
            min-width: 100px;
        }
        .signature-role {
            font-size: 8.5px;
            color: #777;
            margin-top: 1px;
        }
    </style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-text">
            <div class="pesantren-name">{{ config('app.pesantren_name', 'Pondok Pesantren') }}</div>
            <div class="pesantren-subtitle">{{ config('app.pesantren_address', 'Jl. Pesantren No. 1') }}</div>
        </div>
        <div class="receipt-label">
            <div class="receipt-title">KWITANSI</div>
            <div class="receipt-number">No: {{ strtoupper(substr($payment->id, 0, 8)) }}</div>
            <div class="status-badge">Lunas</div>
        </div>
    </div>

    {{-- ── STUDENT INFO ── --}}
    <div class="section-title">Informasi Santri</div>
    <table class="info-table">
        <tr>
            <td class="label">Nama Santri</td>
            <td class="colon">:</td>
            <td class="value">{{ $payment->bill?->student?->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIS</td>
            <td class="colon">:</td>
            <td class="value">{{ $payment->bill?->student?->nis ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Mukim</td>
            <td class="colon">:</td>
            <td class="value">{{ $payment->bill?->student?->residency_status?->getLabel() ?? '-' }}</td>
        </tr>
    </table>

    {{-- ── PAYMENT DETAIL ── --}}
    <div class="section-title">Detail Pembayaran</div>
    <table class="info-table">
        <tr>
            <td class="label">Jenis Biaya</td>
            <td class="colon">:</td>
            <td class="value">{{ $payment->bill?->fee?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td class="colon">:</td>
            <td class="value">{{ $payment->bill?->period_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Metode Pembayaran</td>
            <td class="colon">:</td>
            <td class="value">{{ $payment->method === 'cash' ? 'Tunai (Cash)' : 'Transfer Bank' }}</td>
        </tr>
        @if($payment->transaction_id)
        <tr>
            <td class="label">ID Transaksi</td>
            <td class="colon">:</td>
            <td class="value">{{ $payment->transaction_id }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Tanggal Bayar</td>
            <td class="colon">:</td>
            <td class="value">{{ $payment->created_at?->translatedFormat('d F Y, H:i') ?? '-' }}</td>
        </tr>
    </table>

    {{-- ── AMOUNT HIGHLIGHT ── --}}
    <div class="amount-box">
        <div class="amount-box-label">Jumlah Dibayar</div>
        <div class="amount-box-value">
            {{ \Illuminate\Support\Number::currency((float) ($payment->amount ?? 0), 'IDR') }}
        </div>
    </div>

    {{-- ── TRANSPARENCY SUMMARY ── --}}
    <hr class="divider">
    <div class="summary-row">
        <div class="summary-cell">
            <div class="s-label">Total Tagihan</div>
            <div class="s-value">
                {{ \Illuminate\Support\Number::currency((float) ($payment->bill?->final_amount ?? 0), 'IDR') }}
            </div>
        </div>
        <div class="summary-cell">
            <div class="s-label">Sisa Tagihan Setelah Bayar</div>
            @php
                $remaining = (float) ($payment->bill?->remaining_balance ?? 0);
            @endphp
            <div class="s-value {{ $remaining <= 0 ? 'paid' : 'unpaid' }}">
                {{ \Illuminate\Support\Number::currency($remaining, 'IDR') }}
            </div>
        </div>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-left">
            <div class="footer-note">
                * Kwitansi ini merupakan bukti pembayaran yang sah.<br>
                * Dokumen ini dicetak secara otomatis oleh sistem.<br>
                * Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}
            </div>
        </div>
        <div class="footer-right">
            <div class="signature-date">{{ now()->translatedFormat('d F Y') }}</div>
            <br>
            <div class="signature-name">Bendahara</div>
            <div class="signature-role">Petugas Keuangan</div>
        </div>
    </div>

</body>
</html>
