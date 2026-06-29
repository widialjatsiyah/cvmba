@extends('layouts.app')

@section('title', 'Daftar Stock Opname')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Daftar Stock Opname</h4>
            <a href="{{action([\App\Http\Controllers\StockOpnameController::class, 'create'])}}" class="btn btn-primary pull-right">
                <i class="fa fa-plus"></i> Tambah Stock Opname Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="stock_opname_table">
                    <thead>
                        <tr>
                            <th>Ref No.</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Tipe</th>
                            <th>Total Dikembalikan</th>
                            <th>Catatan</th>
                            <th>Ditambahkan Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade so_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function(){
    // DataTable initialization
    stock_opname_table = $('#stock_opname_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/stock-opname',
        columnDefs: [{
            "targets": [7],
            "orderable": false,
            "searchable": false
        }],
        columns: [
            { data: 'ref_no' },
            { data: 'transaction_date' },
            { data: 'location_name' },
            { data: 'adjustment_type' },
            { data: 'total_amount_recovered' },
            { data: 'additional_notes' },
            { data: 'added_by' },
            { data: 'action' }
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#stock_opname_table'));
        },
    });
});
</script>
@endsection