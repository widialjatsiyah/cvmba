@extends('layouts.app')

@section('title', 'Detail Stock Opname')

@section('content')
<section class="content">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Detail Stock Opname</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Ref No:</strong> {{ $stock_opname->ref_no }}
                </div>
                <div class="col-md-4">
                    <strong>Tanggal:</strong> {{ @format_datetime($stock_opname->transaction_date) }}
                </div>
                <div class="col-md-4">
                    <strong>Lokasi:</strong> {{ $stock_opname->location->name }}
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Tipe:</strong> {{ __('stock_adjustment.'.$stock_opname->adjustment_type) }}
                </div>
                <div class="col-md-4">
                    <strong>Dibuat oleh:</strong> {{ $stock_opname->created_by_user->user_full_name ?? '' }}
                </div>
                <div class="col-md-4">
                    <strong>Catatan:</strong> {{ $stock_opname->additional_notes }}
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Total Dikembalikan:</strong> {{ @num_format($stock_opname->total_amount_recovered) }}
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Variasi</th>
                            <th>Satuan</th>
                            <th>Stok Awal</th>
                            <th>Stok Fisik</th>
                            <th>Selisih</th>
                            <th>Harga Satuan</th>
                            <th>Nilai Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opname_lines as $line)
                        <tr>
                            <td>{{ $line->sku .' - '. $line->product_name }}</td>
                            <td>{{ $line->variation_name }}</td>
                            <td>{{ $line->unit_name ?: '-' }}</td>
                            <td>{{ @num_format($line->current_qty) }}</td>
                            <td>{{ @num_format($line->actual_qty) }}</td>
                            <td>{{ @num_format($line->difference) }}</td>
                            <td>{{ @num_format($line->unit_price) }}</td>
                            <td>{{ @num_format(abs($line->difference) * $line->unit_price) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer">
            <a href="{{action([\App\Http\Controllers\StockOpnameController::class, 'index'])}}" class="btn btn-default">Kembali Ke Daftar</a>
        </div>
    </div>
</section>
@endsection