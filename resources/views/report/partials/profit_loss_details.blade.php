<div class="col-xs-6">
        <table class="table table-striped">
            <tr>
                <th>{{ __('report.opening_stock') }} <br><small class="text-muted">(@lang('lang_v1.by_purchase_price'))</small>:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['opening_stock']}}</span>
                </td>
            </tr>
            
            <tr>
                <th>{{ __('home.total_purchase') }}:<br><small class="text-muted">(@lang('product.exc_of_tax'), @lang('sale.discount'))</small></th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_purchase']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('report.total_stock_adjustment') }}:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_adjustment']}}</span>
                </td>
            </tr> 
            <tr>
                <th>{{ __('report.total_expense') }}:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_expense']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_purchase_shipping_charge') }}:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_purchase_shipping_charge']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.purchase_additional_expense') }}:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_purchase_additional_expense']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_transfer_shipping_charge') }}:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_transfer_shipping_charges']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_sell_discount') }}:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_sell_discount']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_reward_amount') }}:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_reward_amount']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_sell_return') }}:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_sell_return']}}</span>
                </td>
            </tr>
            @foreach($data['left_side_module_data'] as $module_data)
                <tr>
                    <th>{{ $module_data['label'] }}:</th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{ $module_data['value'] }}</span>
                    </td>
                </tr>
            @endforeach
        </table>
</div>

<div class="col-xs-6">
        <table class="table table-striped">
            <tr>
                <th>{{ __('report.closing_stock') }} <br><small class="text-muted">(@lang('lang_v1.by_purchase_price'))</small>:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['closing_stock']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('report.closing_stock') }} <br><small class="text-muted">(@lang('lang_v1.by_sale_price'))</small>:</th>
                <td>
                    <span id="closing_stock_by_sp"><i class="fa fa-sync fa-spin fa-fw "></i></span>
                </td>
            </tr>
            <tr>
                <th>{{ __('home.total_sell') }}: <br>
                    <!-- sub type for total sales -->
                    @if(count($data['total_sell_by_subtype']) > 1)
                    <ul>
                        @foreach($data['total_sell_by_subtype'] as $sell)
                            <li>
                                <span class="display_currency" data-currency_symbol="true">
                                    {{$sell->total_before_tax}}    
                                </span>
                                @if(!empty($sell->sub_type))
                                    &nbsp;<small class="text-muted">({{ucfirst($sell->sub_type)}})</small>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    @endif
                    <small class="text-muted"> 
                        (@lang('product.exc_of_tax'), @lang('sale.discount'))
                    </small>
                </th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_sell']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_sell_shipping_charge') }}:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_sell_shipping_charge']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.sell_additional_expense') }}:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_sell_additional_expense']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('report.total_stock_recovered') }}:</th>
                <td>
                     <span class="display_currency" data-currency_symbol="true">{{$data['total_recovered']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_purchase_return') }}:</th>
                <td>
                     <span class="display_currency" data-currency_symbol="true">{{$data['total_purchase_return']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_purchase_discount') }}:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_purchase_discount']}}</span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_sell_round_off') }}:</th>
                <td>
                    <span class="display_currency" data-currency_symbol="true">{{$data['total_sell_round_off']}}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                &nbsp;
                </td>
            </tr>
            @foreach($data['right_side_module_data'] as $module_data)
                <tr>
                    <th>{{ $module_data['label'] }}:</th>
                    <td>
                        <span class="display_currency" data-currency_symbol="true">{{ $module_data['value'] }}</span>
                    </td>
                </tr>
            @endforeach
        </table>
</div>
<br>
<div class="col-xs-12">
        <h3 class="text-muted mb-0">
            {{ __('lang_v1.gross_profit') }}: 
            <span class="display_currency" data-currency_symbol="true">{{$data['gross_profit']}}</span>
        </h3>
        

        <h3 class="text-muted mb-0">
            {{ __('report.net_profit') }}: 
            <span class="display_currency" data-currency_symbol="true">{{$data['net_profit']}}</span>
        </h3>
        
</div>