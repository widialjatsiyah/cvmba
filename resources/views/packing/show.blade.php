@extends('layouts.app')
@section('title', 'Detail Packing')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Detail Packing</h1>
        <!-- <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                <li class="active">Here</li>
            </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">

        @component('components.widget', ['class' => 'box-primary'])
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Packing Name</label>
                        <div class="col-sm-8">
                            {{ $packing->packing_name }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Packing Date</label>
                        <div class="col-sm-8">
                            {{ $packing->created_at }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <h4>List Invoice</h4>
                    <table class="table table-condensed table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Faktur</th>
                                <th>Pelanggan</th>
                                <th>Alamat</th>
                                <th>Wilayah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($packing_details as $detail)
                                <tr>
                                    <td>{{ $detail->transaction->invoice_no ?? 'N/A' }}</td>
                                    <td>{{ $detail->transaction->contact->name ?? 'N/A' }}</td>
                                    <td>{{ $detail->transaction->contact->landline ?? 'N/A' }}</td>
                                    <td>{{ $detail->transaction->contact->custom_field1 ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No packing details found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <h4>List Barang Gabungan</h4>
                    <table class="table table-condensed table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Kode Item</th>
                                <th>Qty Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aggregated_products as $product_data)
                                <tr>
                                    <td>{{ $product_data['product']->name ?? 'N/A' }}</td>
                                    <td>{{ $product_data['product']->sku ?? 'N/A' }}</td>
                                    <td>
                                        @if (!empty($product_data['conversion_details']))
                                            @foreach ($product_data['conversion_details'] as $conv)
                                                {{ $conv['unit_count'] }} {{ $conv['unit_name'] }}
                                            @endforeach
                                        @else
                                            {{ @num_format($product_data['total_quantity']) }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No products found in transactions.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mb-3">
                <div class="m-3  col-sm-12">
                    <button class="btn btn-secondary" onclick="window.history.back()">Back</button>
                    <a href="{{ url('packing/print-all-invoices/' . $packing->id) }}" target="_blank" class="btn btn-primary"><i class="fa fa-print" ></i> Print Packing</a>
                </div>
            </div>
        @endcomponent

    </section>
@endsection
