<div class="modal fade" id="PackingModal" tabindex="-1" role="dialog" 
    aria-labelledby="PackingModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! Form::open(['url' => action([\App\Http\Controllers\PackingController::class, 'store']), 'method' => 'post', 'id' => 'Packing_form']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.Packing_details')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('packing_name', __( 'lang_v1.packing_name' ) . ':*') !!}
                            {!! Form::text('packing_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.packing_name' )]); !!}
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('packing_date', __( 'lang_v1.packing_date' ) . ':*') !!}
                            {!! Form::date('packing_date', \Carbon\Carbon::now(), ['class' => 'form-control', 'required']); !!}
                        </div>
                    </div>
                </div>
                
                <!-- Multiple transaction IDs -->
                @if(isset($transactions) && is_array($transactions))
                    @foreach($transactions as $transaction)
                        <input type="hidden" name="transaction_id[]" value="{{ $transaction->id }}">
                    @endforeach
                @else
                    <input type="hidden" name="transaction_id" value="{{ $transactions->id ?? request()->route()->parameter('transactionIds') }}">
                @endif
                
                <div class="row">
                    <div class="col-md-12">
                        <h4>{{ __('lang_v1.Packing_items') }}</h4>
                        
                        @if(isset($transactions) && is_array($transactions) && count($transactions) > 1)
                            <div class="alert alert-info">
                                <strong>{{ __('lang_v1.multiple_transactions_selected') }}:</strong>
                                @foreach($transactions as $transaction)
                                    <span class="label label-primary">{{ $transaction->invoice_no }}</span>
                                @endforeach
                            </div>
                        @endif
                        
                        <table class="table table-condensed table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('sale.product')</th>
                                    <th>@lang('sale.invoice_no')</th>
                                    <th>@lang('sale.quantity')</th>
                                    <th>@lang('lang_v1.quantity_packed')</th>
                                    <th>@lang('lang_v1.notes')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sellLines as $line)
                                    <tr>
                                        <td>
                                            {{ $line->product->name }}
                                            @if($line->variations)
                                                - {{ $line->variations->name }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $line->transaction->invoice_no }}
                                        </td>
                                        <td>
                                            {{ $line->quantity }}
                                            {{ $line->sub_unit->short_name ?? '' }}
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   name="quantities_packed[]" 
                                                   class="form-control input-sm" 
                                                   min="0" 
                                                   max="{{ $line->quantity }}"
                                                   placeholder="@lang('lang_v1.quantity_packed')"
                                                   value="{{ $line->quantity }}">
                                            <input type="hidden" name="sell_line_ids[]" value="{{ $line->id }}">
                                        </td>
                                        <td>
                                            <textarea name="notes[]" class="form-control input-sm" rows="1" placeholder="@lang('lang_v1.notes_optional')"></textarea>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">@lang('lang_v1.no_items_found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang('messages.submit')</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        // Initialize the modal
        $('#PackingModal').modal('show');
        
        // Handle form submission
        $('#Packing_form').submit(function(e){
            e.preventDefault();
            
            var formData = $(this).serialize();
            
            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: formData,
                beforeSend: function(xhr) {
                    $('.box').append('<div class="overlay"><i class="fas fa-sync fa-spin"></i></div>');
                },
                success: function(result) {
                    if (result.success == true) {
                        $('div#PackingModal').modal('hide');
                        toastr.success(result.msg);
                        // Reload the page or update the UI as needed
                        location.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(xhr, status, error) {
                    $('div#PackingModal').modal('hide');
                    toastr.error(__('messages.something_went_wrong'));
                },
                complete: function() {
                    $('.overlay').remove();
                }
            });
        });
    });
</script>