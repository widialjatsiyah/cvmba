<div class="modal fade" id="PackingHistoryModal" tabindex="-1" role="dialog" 
    aria-labelledby="PackingHistoryModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.Packing_history')</h4>
            </div>
            <div class="modal-body">
                @if(count($packings) > 0)
                    @foreach($packings as $packing)
                        <div class="box box-solid box-default">
                            <div class="box-header">
                                <h4 class="box-title">{{ $packing->packing_name }} - {{ @format_date($packing->packing_date) }}</h4>
                            </div>
                            <div class="box-body">
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
                    @endforeach
                @else
                    <p>@lang('lang_v1.no_Packing_found')</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#PackingHistoryModal').modal('show');
    });
</script>