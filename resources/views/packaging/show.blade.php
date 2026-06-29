<div class="modal fade" id="showPackingModal" tabindex="-1" role="dialog" 
    aria-labelledby="showPackingModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.Packing_details')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('lang_v1.packing_name') }}:</label>
                            <p>{{ $packing->packing_name }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('lang_v1.packing_date') }}:</label>
                            <p>{{ @format_date($packing->packing_date) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('sale.invoice_no') }}:</label>
                            <p>{{ $packing->transaction->invoice_no }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('messages.date') }}:</label>
                            <p>{{ @format_datetime($packing->transaction->transaction_date) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('sale.customer_name') }}:</label>
                            <p>{{ $packing->transaction->contact->name ?? '' }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('lang_v1.created_by') }}:</label>
                            <p>{{ $packing->createdBy->user_full_name ?? '' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <h4>{{ __('lang_v1.Packing_items') }}</h4>
                        <table class="table table-condensed table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('sale.product')</th>
                                    <th>@lang('sale.quantity')</th>
                                    <th>@lang('lang_v1.quantity_packed')</th>
                                    <th>@lang('lang_v1.notes')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packing->details as $detail)
                                    <tr>
                                        <td>
                                            {{ $detail->sellLine->product->name }}
                                            @if($detail->sellLine->variations)
                                                - {{ $detail->sellLine->variations->name }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $detail->sellLine->quantity }}
                                            {{ $detail->sellLine->sub_unit->short_name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $detail->quantity_packed }}
                                            {{ $detail->sellLine->sub_unit->short_name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $detail->notes }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#showPackingModal').modal('show');
    });
</script>