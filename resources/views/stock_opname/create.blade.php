@extends('layouts.app')

@section('title', 'Stock Opname')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Stock Opname (Stock Count)</h4>
        </div>
        <div class="card-body">
            {!! Form::open(['url' => action([\App\Http\Controllers\StockOpnameController::class, 'store']), 'method' => 'post', 'id' => 'stock_opname_form']) !!}
            
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('location_id', __('purchase.business_location').':*') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('ref_no', __('purchase.ref_no').':') !!}
                        {!! Form::text('ref_no', null, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('transaction_date', __('messages.date') . ':*') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('adjustment_type', __('Jenis Stok Opname') . ':*') !!} @show_tooltip(__('tooltip.adjustment_type'))
                        {!! Form::select('adjustment_type', [ 'normal' =>  __('stock_adjustment.normal')], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                    </div>
                </div>
            </div>
            
            <div class="box box-solid">
                <div class="box-header">
                    <h3 class="box-title">{{ __('stock_adjustment.search_products') }} - {{ __('Stock Opname') }}</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-search"></i>
                                    </span>
                                    {!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product_for_stock_opname', 'placeholder' => __('stock_adjustment.search_product'), 'disabled']); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-10 col-sm-offset-1">
                            <input type="hidden" id="product_row_index" value="0">
                            <input type="hidden" id="total_amount" name="final_total" value="0">
                            <div class="table-responsive">
                            <table class="table table-bordered table-striped table-condensed" 
                            id="stock_opname_product_table">
                                <thead>
                                    <tr>
                                        <th class="col-sm-3 text-center">	
                                            @lang('sale.product')
                                        </th>
                                        <th class="col-sm-2 text-center">
                                            Stok Saat Ini
                                        </th>
                                        <th class="col-sm-2 text-center">
                                            Stok Real
                                        </th>
                                        <th class="col-sm-2 text-center">
                                            Harga
                                        </th>
                                        <th class="col-sm-2 text-center">
                                            Silisih Stok
                                        </th>
                                        <th class="col-sm-1 text-center">
                                            @lang('messages.action')
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr class="text-center">
                                        <td colspan="4"></td>
                                        <td>
                                            <div class="pull-right">
                                                <b>@lang('stock_adjustment.total_amount'): </b> 
                                                <span id="total_opname_difference">0.00</span>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('total_amount_recovered', __('stock_adjustment.total_amount_recovered') . ':') !!} @show_tooltip(__('tooltip.total_amount_recovered'))
                                {!! Form::text('total_amount_recovered', 0, ['class' => 'form-control input_number', 'placeholder' => __('stock_adjustment.total_amount_recovered')]); !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('additional_notes', __('stock_adjustment.reason_for_stock_adjustment') . ':') !!}
                                {!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'placeholder' => __('stock_adjustment.reason_for_stock_adjustment'), 'rows' => 3]); !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-primary btn-big">@lang('messages.save')</button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<div id="overlay" class="d-none">
    <div class="overlay-box text-center">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="mt-2 text-white">
            Menyimpan data, mohon tunggu...
        </div>
    </div>
</div>
@endsection

@section('javascript')
    <script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            
              // Disable tombol saat submit (mencegah double input)
    $('#stock_opname_form').on('submit', function () {

        // Jika sudah pernah submit, hentikan
        if ($(this).data('submitted') === true) {
            return false;
        }

        // Tandai sudah submit
        $(this).data('submitted', true);

        // Disable tombol submit
        $(this).find('button[type="submit"]')
            .prop('disabled', true)
            .text('Saving...');
            
    // Tampilkan overlay
    $('#overlay').removeClass('d-none');

    });
    
            // Disable search initially until location is selected
            $('#location_id').change(function(){
                if ($(this).val().length > 0) {
                    $('#search_product_for_stock_opname').prop('disabled', false).focus();
                } else {
                    $('#search_product_for_stock_opname').prop('disabled', true);
                }
            });
            
            // Initialize the search product field after location is selected
            if ($('#location_id').val().length > 0) {
                $('#search_product_for_stock_opname').prop('disabled', false);
            }
            
            // Update total when quantities change
            $(document).on('change', '.actual_qty_input, .unit_price_input', function(){
                updateTotalOpnameDifference();
            });
            
            // Handle product search
            $('#search_product_for_stock_opname').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "/stock-opname/get-products",
                        dataType: "json",
                        data: {
                            term: request.term,
                            location_id: $('#location_id').val()
                        },
                        success: function(data) {
                            if(data.length > 0) {
                                response(data);
                            } else {
                                // Jika tidak ada hasil, tampilkan pesan
                                response([{id: 0, name: 'Tidak ada produk ditemukan'}]);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('Error: ', xhr.responseText);
                            // Tampilkan pesan kesalahan
                            response([{id: 0, name: 'Error saat mencari produk'}]);
                        }
                    });
                },
                minLength: 2,
                delay: 300,
                select: function(event, ui) {
                    if(ui.item.id !== 0) { // Pastikan bukan item error
                        addNewRow($('#location_id').val(), ui.item);
                        $(this).val('');
                    }
                    return false;
                },
                focus: function(event, ui) {
                    // Hindari auto selection
                    return false;
                }
            }).autocomplete('instance')._renderItem = function(ul, item) {
                // Custom rendering untuk item autocomplete
                if(item.id === 0) {
                    // Item error atau tidak ditemukan
                    return $('<li>').append('<div>' + item.name + '</div>').appendTo(ul);
                } else {
                    // Item produk
                    var string = '<div class="ac-product-item">' + item.name;
                    if(item.variation) {
                        string += ' - ' + item.variation;
                    }
                    if(item.sub_sku) {
                        string += ' (' + item.sub_sku + ')';
                    }
                    string += ' | Stok: ' + item.qty_available + '</div>';
                    return $('<li>').append(string).appendTo(ul);
                }
            };
        });
        
        function updateTotalOpnameDifference() {
            var total = 0;
            $('.product_row').each(function(){
                var current_stock = parseFloat($(this).find('.current_stock').text()) || 0;
                var actual_stock = parseFloat($(this).find('.actual_qty_input').val()) || 0;
                var unit_price = parseFloat($(this).find('.unit_price_input').val()) || 0;
                
                var difference = actual_stock - current_stock;
                var difference_amount = Math.abs(difference) * unit_price;
                
                total += difference_amount;
            });
            
            $('#total_opname_difference').text(number_format(total));
        }
        
        // Function to add a product row similar to stock adjustment
        function addNewRow(location_id, product_details) {
            var row_index = parseInt($('#product_row_index').val());
            var new_row = '<tr class="product_row">';
            new_row += '<td>';
            new_row += product_details.name;
            if(product_details.variation) {
                new_row += ' - ' + product_details.variation;
            }
            if(product_details.sub_sku) {
                new_row += ' (' + product_details.sub_sku + ')';
            }
            new_row += '<input type="hidden" name="products[' + row_index + '][product_id]" value="' + product_details.product_id + '">';
            new_row += '<input type="hidden" name="products[' + row_index + '][variation_id]" value="' + product_details.variation_id + '">';
            new_row += '<input type="hidden" name="products[' + row_index + '][current_qty]" value="' + product_details.qty_available + '">';
            new_row += '</td>';
            
            new_row += '<td class="current_stock">' + product_details.qty_available + '</td>';
            
            new_row += '<td><input type="text" name="products[' + row_index + '][actual_qty]" class="form-control input_number actual_qty_input" value="' + product_details.qty_available + '" data-row_index="' + row_index + '" placeholder="Actual Stock"></td>';
            
            new_row += '<td><input type="text" name="products[' + row_index + '][unit_price]" class="form-control input_number unit_price_input" value="' + product_details.default_sell_price + '" data-row_index="' + row_index + '"></td>';
            
            new_row += '<td class="difference_td"><span class="difference">0</span></td>';
            
            new_row += '<td>';
            new_row += '<button type="button" class="btn btn-danger btn-xs remove_product_row" data-row_index="' + row_index + '"><i class="fa fa-trash" aria-hidden="true"></i></button>';
            new_row += '</td>';
            
            new_row += '</tr>';
            
            $('#stock_opname_product_table tbody').append(new_row);
            $('#product_row_index').val(row_index + 1);
            
            // Update difference calculation after adding row
            setTimeout(function(){
                updateTotalOpnameDifference();
                updateRowDifference();
            }, 100);
        }
        
        // Update difference for each row
        function updateRowDifference() {
            $('.product_row').each(function(){
                var current_stock = parseFloat($(this).find('.current_stock').text()) || 0;
                var actual_stock = parseFloat($(this).find('.actual_qty_input').val()) || 0;
                
                var difference = actual_stock - current_stock;
                $(this).find('.difference').text(difference);
            });
        }
        
        // Event listener for updating difference when actual_qty changes
        $(document).on('input change', '.actual_qty_input', function(){
            updateRowDifference();
            updateTotalOpnameDifference();
        });
        
        // Event listener for updating difference when unit_price changes
        $(document).on('input change', '.unit_price_input', function(){
            updateTotalOpnameDifference();
        });
        
        // Event listener for removing product rows
        $(document).on('click', '.remove_product_row', function(){
            $(this).closest('tr').remove();
            updateTotalOpnameDifference();
            updateRowDifference();
        });
        
        __page_leave_confirmation('#stock_opname_form');
    </script>
    <style>
        .ac-product-item {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        
        .d-none {
            display: none;
        }
        
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
        
            width: 100%;
            height: 100%;
        
            background: rgba(0,0,0,0.5);
        
            z-index: 9999;
        }
        
        .overlay-box {
            position: absolute;
        
            top: 50%;
            left: 50%;
        
            transform: translate(-50%, -50%);
        }
    </style>
@endsection