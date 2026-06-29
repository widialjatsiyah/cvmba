<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Packing Invoices - {{ $packing->packing_name }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <style type="text/css">
        @media print {

            .no-print,
            .no-print * {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .invoice-page {
                page-break-after: always;
            }

            .packing-details-page {
                page-break-before: always;
            }
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .invoice-page {
            margin: 20px;
        }

        .packing-details-page {
            margin: 20px;
            page-break-before: always;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .invoice-table th {
            background-color: #f2f2f2;
        }

        .packing-summary {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        .btn-print {
            padding: 10px 20px;
            background-color: #3c8dbc;
            color: white;
            border: none;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .btn-print:hover {
            background-color: #367fa9;
        }
    </style>
</head>

<body>

    <!-- Print each related invoice -->
    @foreach ($transactions as $transaction)
        <div class="invoice-page">
            <div class="header">
                <h2>Invoice</h2>
                <h3>{{ $transaction->invoice_no }}</h3>
                <p><strong>Date:</strong> {{ @format_date($transaction->transaction_date) }}</p>
                <p><strong>Customer:</strong> {{ $transaction->contact->name }}</p>
            </div>

            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaction->sell_lines as $sell_line)
                        <tr>
                            <td>
                                {{ $sell_line->product->name }}
                                @if ($sell_line->product->type == 'variable')
                                    - {{ $sell_line->variations->product_variation->name }} -
                                    {{ $sell_line->variations->name }}
                                @endif
                            </td>
                            <td>{{ @num_format($sell_line->quantity) }}
                                {{ $sell_line->product->unit->short_name ?? '' }}</td>
                            <td>{{ @num_format($sell_line->unit_price_inc_tax) }}</td>
                            <td>{{ @num_format($sell_line->line_total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 20px;">
                <p><strong>Total Amount:</strong> {{ @num_format($transaction->final_total) }}</p>
                <p><strong>Payment Status:</strong> {{ __('lang_v1.' . $transaction->payment_status) }}</p>
            </div>
        </div>
    @endforeach

    <!-- Packing Summary Page -->
    <div class="packing-details-page">
        <div class="header">
            <h2>Packing Details</h2>
            <h3>{{ $packing->packing_name }}</h3>
            <p><strong>Packing Date:</strong> {{ @format_date($packing->packing_date) }}</p>
        </div>

        <h4>Related Invoices</h4>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Customer</th>
                    {{-- <th>Date</th> --}}
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($packing_details as $detail)
                    <tr>
                        <td>{{ $detail->transaction->invoice_no ?? '-' }}</td>
                        <td>{{ $detail->transaction->contact->name ?? '-' }}</td>
                        <td>{{ $detail->transaction->contact->landline ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="packing-summary">
            <h4>Consolidated Products</h4>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>SKU</th>
                        <th>Total Quantity</th>
                        <th>Converted Quantity</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aggregated_products as $product_data)
                        <tr>
                            <td>{{ $product_data['product']->name ?? 'N/A' }}</td>
                            <td>{{ $product_data['product']->sku ?? 'N/A' }}</td>
                            <td>{{ @num_format($product_data['total_quantity']) }}</td>
                            <td>
                                @if (!empty($product_data['conversion_details']))
                                    @foreach ($product_data['conversion_details'] as $conv)
                                        {{ $conv['unit_count'] }} {{ $conv['unit_name'] }}
                                    @endforeach
                                @else
                                    {{ @num_format($product_data['total_quantity']) }}
                                @endif
                            </td>
                            <td>{{ $product_data['unit']->short_name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <script type="text/javascript">
        setTimeout(function() {
            window.print();
        }, 1000);
    </script>
</body>

</html>
