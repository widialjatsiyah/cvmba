<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Packing Invoices - {{ $packing->packing_name ?? 'Packing' }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <style type="text/css">
        @media print {
            .no-print, .no-print * { display: none !important; }
            body { margin: 0; padding: 0; }
            .invoice-page { page-break-after: always; }
            .packing-details-page { page-break-after: always; }
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .packing-details-page, .invoice-page {
            margin: 20px;
            padding: 10px;
            box-sizing: border-box;
        }
        .header-company {
            text-align: center;
            margin-bottom: 15px;
        }
        .header-company h2 { margin: 0; font-size: 18px; }
        .header-company p { margin: 0; font-size: 12px; }
        .invoice-to p { margin: 3px 0; }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }
        .invoice-table th, .invoice-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: left;
        }
        .invoice-table th { background-color: #f2f2f2; font-weight: bold; }
        .invoice-table td.right { text-align: right; }
        .invoice-table tfoot td { font-weight: bold; }
        .summary-block { margin-top: 10px; padding: 5px 0; }
        .summary-block p { margin: 3px 0; }
        .notes-block { margin-top: 15px; font-size: 11px; }
        .notes-block ul { padding-left: 20px; margin: 5px 0; }
        .signature-block {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-block div { width: 45%; }
        .packing-details-page .meta { margin-bottom: 10px; }
        .packing-details-page .meta p { margin: 3px 0; }
        .packing-details-page .keterangan { margin-top: 15px; }
        .packing-details-page .keterangan div { margin: 5px 0; }
        .btn-print {
            padding: 10px 20px;
            background-color: #3c8dbc;
            color: white;
            border: none;
            cursor: pointer;
            margin: 10px;
        }
        .btn-print:hover { background-color: #367fa9; }
        .no-print { text-align: center; }
    </style>
</head>
<body>

    <!-- ========================================================= -->
    <!-- PACKING DETAILS PAGE (mirip Pengepakan PB)                 -->
    <!-- ========================================================= -->
    <div class="packing-details-page">
        <div class="header-company">
            <h2>CV. Mutiara Berkah Abadi</h2>
            <p>Boulevard No.7 Kel. Sukamanah, Kec. Cipedes Kota Tasikmalaya 46131 Indonesia</p>
        </div>

        <div class="meta">
            <p><strong>Nomor :</strong> {{ $packing->packing_name ?? 'PB-202602-00017' }}</p>
            <p><strong>Tanggal :</strong> {{ @format_date($packing->packing_date ?? now()) }}</p>
            <p><strong>Area :</strong> {{ $packing->area ?? 'SUDAH CETAK ACCURATE - SUDAH INPUT SISTEM BARU' }}</p>
        </div>

        <h3>Daftar Invoice</h3>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Alamat</th>
                    <th>Area</th>
                    {{-- <th>Sales</th> --}}
                    <th>Tgl Faktur</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($packing_details as $detail)
                    @php $t = $detail->transaction; @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $t->invoice_no ?? '-' }}</td>
                        <td>{{ $t->contact->name ?? '-' }}</td>
                        <td>{{ $t->contact->address ?? '' }}</td>
                        <td>{{ $t->area ?? '' }}</td>
                        {{-- <td>{{ $t->sales_person ?? '' }}</td> --}}
                        <td>{{ @format_date($t->transaction_date) }}</td>
                        <td class="right">{{ @num_format((float)($t->final_total ?? 0)) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8">Tidak ada invoice</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right;"><strong>Total Piutang</strong></td>
                    <td class="right">
                        <strong>
                            {{ @num_format($packing_details->sum(fn($d) => (float)($d->transaction->final_total ?? 0))) }}
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table>

        <h3>Barang Gabungan</h3>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kode</th>
                    <th>Qty</th>
                    <th>UOM</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($aggregated_products as $product_data)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $product_data['product']->name ?? 'N/A' }}</td>
                        <td>{{ $product_data['product']->sku ?? 'N/A' }}</td>
                        <td class="right">{{ @num_format((float)($product_data['total_quantity'] ?? 0)) }}</td>
                        <td>{{ $product_data['unit']->short_name ?? '' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Tidak ada produk</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="keterangan">
            <p><strong>Keterangan</strong></p>
             <div style="margin-top:10px;">
                <span>Dibuat Oleh : ( ) &nbsp;&nbsp;&nbsp; Tgl : </span>
                <span style="margin-left:40px;">Disiapkan Oleh : ( ) &nbsp;&nbsp;&nbsp; Tgl : </span>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- INVOICE PAGES (mirip PB-202602-00017)                      -->
    <!-- ========================================================= -->
    @forelse ($packing_details as $detail)
        @php
            $transaction = $detail->transaction;
            if (!$transaction) continue;
            $contact = $transaction->contact;
            $sell_lines = $transaction->sell_lines ?? collect();

            // Ambil nilai summary dari transaction (jika ada)
            $sub_total = (float)($transaction->sub_total ?? 0);
            $discount_amount = (float)($transaction->discount_amount ?? 0);
            $tax_amount = (float)($transaction->tax_amount ?? 0);
            $final_total = (float)($transaction->final_total ?? 0);

            // Jika sub_total tidak ada, hitung dari lines
            if ($sub_total == 0 && $sell_lines->count()) {
                $sub_total = $sell_lines->sum(function($line) {
                    return (float)($line->line_total ?? 0);
                });
            }
        @endphp
        <div class="invoice-page">
            <div class="header-company">
                <h2>CV. Mutiara Berkah Abadi</h2>
                <p>Boulevard No.7 Kel. Sukamanah, Kec. CipedesKota Tasikmalaya 46131</p>
            </div>

            <div class="invoice-to">
                <p><strong>Kepada :</strong> {{ $contact->name ?? '-' }}</p>
                <p>{{ $contact->address ?? '' }}</p>
                <p><strong>NPWP :</strong> {{ $contact->tax_number ?? '-' }}</p>
                {{-- <p><strong>Sales :</strong> {{ $transaction->sales_person ?? '' }}</p> --}}
                <p><strong>No Faktur :</strong> {{ $transaction->invoice_no ?? '-' }}</p>
                <p><strong>Tanggal :</strong> {{ @format_date($transaction->transaction_date) }}</p>
                <p><strong>Jatuh Tempo :</strong> {{ @format_date($transaction->due_date) }}</p>
                <p><strong>Status :</strong> {{ $transaction->status ?? 'Draft' }}</p>
            </div>

            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>@Harga</th>
                        <th>Disc</th>
                        <th>Netto</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sell_lines as $line)
                        @php
                            // Ambil harga sebelum diskon (jika ada)
                            $harga_before = (float)($line->unit_price ?? 0);
                            // Ambil netto (harga setelah diskon) – coba beberapa field umum
                            $netto = (float)($line->sell_price_inc_tax ?? 0);
                            if ($netto == 0) {
                                $netto = (float)($line->unit_price_after_discount ?? 0);
                            }
                            if ($netto == 0) {
                                $netto = $harga_before; // fallback
                            }
                            // Diskon dalam bentuk string (misal "42+16.5%")
                            $disc_str = $line->discount_percent ?? '';
                            // Quantity
                            $qty = (float)($line->quantity ?? 0);
                            // Total per line = qty * netto
                            $total_line = $qty * $netto;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $line->product->name ?? 'N/A' }}</td>
                            <td class="right">{{ @num_format($qty) }}</td>
                            <td class="right">{{ @num_format($harga_before) }}</td>
                            <td class="right">{{ $disc_str ?: '-' }}</td>
                            <td class="right">{{ @num_format($netto) }}</td>
                            <td class="right">{{ @num_format($total_line) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7">Tidak ada item</td></tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Terbilang (gunakan helper jika ada) -->
            <div style="margin:5px 0;">
                <strong>Terbilang:</strong>
                {{ function_exists('terbilang') ? terbilang($final_total) : 'Rp ' . number_format($final_total, 0, ',', '.') }}
            </div>

            <!-- Summary -->
            @if ($sub_total > 0 || $discount_amount > 0 || $tax_amount > 0)
                <div class="summary-block" style="text-align: right; width: 50%; margin-left: auto;">
                    <p><strong>Sub Total</strong> : {{ @num_format($sub_total) }}</p>
                    @if ($discount_amount != 0)
                        <p><strong>Diskon</strong> : {{ @num_format($discount_amount) }}</p>
                    @endif
                    @if ($tax_amount != 0)
                        <p><strong>PPN 11%</strong> : {{ @num_format($tax_amount) }}</p>
                    @endif
                    <p><strong>TOTAL</strong> : {{ @num_format($final_total) }}</p>
                </div>
            @endif

            <!-- Catatan & Rekening -->
            <div class="notes-block">
                <p><strong>Catatan:</strong></p>
                <ul>
                    <li>Complain setelah tanda tangan diluar tanggung jawab kami.</li>
                    <li>Pembayaran transfer / giro ke rekening perusahaan CV MUTIARA BERKAH ABADI</li>
                    <li>Rek: BRI 010001696969569 (CV MBA)</li>
                    <li>BCA 3210573088 A/N Dedeh Faridah / Teti Runingsih</li>
                    <li>BNI 0999996900 A/N CV MBA</li>
                </ul>
            </div>

            <!-- Tanda tangan -->
            <div class="signature-block">
                <div>
                    <p>Hormat Kami,</p>
                    <p>Pengirim,</p>
                    <p style="margin-top:40px;">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</p>
                </div>
                <div>
                    <p>Penerima,</p>
                    <p style="margin-top:40px;">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</p>
                </div>
            </div>
        </div>
    @empty
        <div class="invoice-page">
            <p>Tidak ada invoice terkait packing ini.</p>
        </div>
    @endforelse

    <!-- Tombol cetak -->
    {{-- <div class="no-print" style="text-align:center; margin-top:20px;">
        <button class="btn-print" onclick="window.print()">Cetak / Print</button>
    </div> --}}

    <script type="text/javascript">
        // Uncomment untuk auto print setelah 1,5 detik
        setTimeout(function() { window.print(); }, 1500);
    </script>
</body>
</html>