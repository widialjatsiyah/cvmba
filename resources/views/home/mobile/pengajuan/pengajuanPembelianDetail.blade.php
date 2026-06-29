@extends('layouts.app_mobile')
@section('title','Pengajuan Pembelian')

@section('content')

<!-- Content Header (Page header) -->


<!-- Main content -->
<div class="card bg-primary">
    <div class="card-body">
    <a href="javascript:history.go(-1)">
    <svg width="20px" height="20px" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <defs> <style>.cls-1{fill:none;stroke:#ffffff;stroke-linecap:round;stroke-linejoin:round;stroke-width:25.088;}</style> </defs> <g data-name="Layer 2" id="Layer_2"> <g data-name="E421, Back, buttons, multimedia, play, stop" id="E421_Back_buttons_multimedia_play_stop"> <circle class="cls-1" cx="256" cy="256" r="246"></circle> <line class="cls-1" x1="352.26" x2="170.43" y1="256" y2="256"></line> <polyline class="cls-1" points="223.91 202.52 170.44 256 223.91 309.48"></polyline> </g> </g> </g></svg>
    </a>
    <span style="font-weight: bolder; margin-left: 5px"> Pengajuan Pembelian Detail </span>
    </div>
</div>

<section class="content" style="margin-bottom: 100px;">
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-md-6 col-xs-6">
                    {!! $status_pengajuan !!}
                </div>
                <div class="col-md-6 col-xs-6">
                    <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}</p>
                </div>
            </div>
            <div class="row invoice-info" style="font-size: 12px;">
                <div class="col-xs-6 invoice-col">
                    @lang('purchase.supplier'):
                    <address>
                        {!! $purchase->contact->contact_address !!}
                        @if(!empty($purchase->contact->tax_number))
                        <br>@lang('contact.tax_no'): {{$purchase->contact->tax_number}}
                        @endif
                        @if(!empty($purchase->contact->mobile))
                        <br>@lang('contact.mobile'): {{$purchase->contact->mobile}}
                        @endif
                        @if(!empty($purchase->contact->email))
                        <br>@lang('business.email'): {{$purchase->contact->email}}
                        @endif
                    </address>
                    @if($purchase->document_path)

                    <a href="{{$purchase->document_path}}"
                        download="{{$purchase->document_name}}" class="btn btn-sm btn-success pull-left no-print">
                        <i class="fa fa-download"></i>
                        &nbsp;{{ __('purchase.download_document') }}
                    </a>
                    @endif
                </div>

                <div class="col-xs-6 invoice-col" style="font-size: 12px;">
                    @lang('business.business'):
                    <address>
                        <strong>{{ $purchase->location->name  }}</strong>
                        @if(!empty($purchase->location->landmark))
                        <br>{{$purchase->location->landmark}}
                        @endif
                    </address>
                </div>

                <div class="col-sm-4 invoice-col" style="font-size: 12px;">
                    <b>@lang('purchase.ref_no'):</b> #{{ $purchase->ref_no }}<br />
                    <b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}<br />
                    @if(!empty($purchase->status))
                    <b>@lang('purchase.purchase_status'):</b> @if($purchase->type == 'purchase_order'){{$po_statuses[$purchase->status]['label'] ?? ''}} @else {{ __('lang_v1.' . $purchase->status) }} @endif<br>
                    @endif
                    @if(!empty($purchase->payment_status))
                    <b>@lang('purchase.payment_status'):</b> {{ __('lang_v1.' . $purchase->payment_status) }}
                    @endif

                    @if(!empty($custom_labels['purchase']['custom_field_1']))
                    <br><strong>{{$custom_labels['purchase']['custom_field_1'] ?? ''}}: </strong> {{$purchase->custom_field_1}}
                    @endif

                    @if(!empty($purchase_order_dates))
                    <br>
                    <strong>@lang('lang_v1.order_dates'):</strong>
                    {{$purchase_order_dates}}
                    @endif
                    @if($purchase->type == 'purchase_order')
                    @php
                    $custom_labels = json_decode(session('business.custom_labels'), true);
                    @endphp
                    <strong>@lang('sale.shipping'):</strong>
                    <span class="label @if(!empty($shipping_status_colors[$purchase->shipping_status])) {{$shipping_status_colors[$purchase->shipping_status]}} @else {{'bg-gray'}} @endif">{{$shipping_statuses[$purchase->shipping_status] ?? '' }}</span><br>
                    @if(!empty($purchase->shipping_address()))
                    {{$purchase->shipping_address()}}
                    @else
                    {{$purchase->shipping_address ?? '--'}}
                    @endif
                    @if(!empty($purchase->delivered_to))
                    <br><strong>@lang('lang_v1.delivered_to'): </strong> {{$purchase->delivered_to}}
                    @endif
                    @if(!empty($purchase->shipping_custom_field_1))
                    <br><strong>{{$custom_labels['shipping']['custom_field_1'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_1}}
                    @endif
                    @if(!empty($purchase->shipping_custom_field_2))
                    <br><strong>{{$custom_labels['shipping']['custom_field_2'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_2}}
                    @endif
                    @if(!empty($purchase->shipping_custom_field_3))
                    <br><strong>{{$custom_labels['shipping']['custom_field_3'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_3}}
                    @endif
                    @if(!empty($purchase->shipping_custom_field_4))
                    <br><strong>{{$custom_labels['shipping']['custom_field_4'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_4}}
                    @endif
                    @if(!empty($purchase->shipping_custom_field_5))
                    <br><strong>{{$custom_labels['shipping']['custom_field_5'] ?? ''}}: </strong> {{$purchase->shipping_custom_field_5}}
                    @endif
                    @php
                    $medias = $purchase->media->where('model_media_type', 'shipping_document')->all();
                    @endphp
                    @if(count($medias))
                    @include('sell.partials.media_table', ['medias' => $medias])
                    @endif
                    @endif
                </div>
            </div>

            <br>
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table bg-gray" style="font-size: 10px;">
                            <thead>
                                <tr class="bg-primary">
                                    <th>#</th>
                                    <th>@lang('product.product_name')</th>
                                    <!-- <th>@lang('product.sku')</th> -->
                                    @if($purchase->type == 'purchase_order')
                                    <th class="text-right hide">@lang( 'lang_v1.quantity_remaining' )</th>
                                    @endif
                                    <th class="text-right">@if($purchase->type == 'purchase_order') @lang('lang_v1.order_quantity') @else @lang('purchase.purchase_quantity') @endif</th>
                                    <th class="no-print text-right">@lang('purchase.unit_cost_before_tax')</th>
                                    <th class="no-print text-right">@lang('purchase.subtotal_before_tax')</th>
                                    @if($purchase->type != 'purchase_order')
                                    @if(session('business.enable_lot_number'))
                                    <th>@lang('lang_v1.lot_number')</th>
                                    @endif

                                    @endif
                                    <th class="text-right">@lang('sale.subtotal') Harga</th>
                                </tr>
                            </thead>
                            @php
                            $total_before_tax = 0.00;
                            @endphp
                            @foreach($purchase->purchase_lines as $purchase_line)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <?php
                                    $location_id =  $purchase->location_id;
                                    $vld = $purchase_line->variations->variation_location_details->firstWhere('location_id', $location_id);
                                   
                                    ?>
                                    {{ $purchase_line->product->name }}
                                    @if( $purchase_line->product->type == 'variable')
                                    - {{ $purchase_line->variations->product_variation->name}}
                                    - {{ $purchase_line->variations->name}}
                                    @endif
                                    <br>
                                    @if( $purchase_line->product->type == 'variable')
                                    {{ $purchase_line->variations->sub_sku}}
                                    @else
                                    {{ $purchase_line->product->sku }}
                                    @endif
                                    <b>Stock : <span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $vld->qty_available }}</span> {{$purchase_line->product->unit->short_name}} </b>
                                </td>
                                @if($purchase->type == 'purchase_order')
                                <td class="hide">
                                    <span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->quantity - $purchase_line->po_quantity_purchased }}</span>

                                    @if(!empty($purchase_line->sub_unit)) {{$purchase_line->sub_unit->short_name}} @else {{$purchase_line->product->unit->short_name}} @endif
                                </td>
                                @endif
                                <td><span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->quantity }}</span>
                                    @if(!empty($purchase_line->sub_unit)) {{$purchase_line->sub_unit->short_name}} @else {{$purchase_line->product->unit->short_name}} @endif

                                    @if(!empty($purchase_line->product->second_unit) && $purchase_line->secondary_unit_quantity != 0)

                                    <span class="display_currency" data-is_quantity="true" data-currency_symbol="false">{{ $purchase_line->secondary_unit_quantity }}</span> {{$purchase_line->product->second_unit->short_name}}
                                    @endif

                                </td>
                                <td class="no-print text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price }}</span></td>
                                <td class="no-print text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->quantity * $purchase_line->purchase_price }}</span></td>
                                @if($purchase->type != 'purchase_order')
                                @if(session('business.enable_lot_number'))
                                <td>{{$purchase_line->lot_number}}</td>
                                @endif


                                @endif
                                <td class="text-right"><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price_inc_tax * $purchase_line->quantity }}</span></td>
                            </tr>
                            @php
                            $total_before_tax += ($purchase_line->quantity * $purchase_line->purchase_price);
                            @endphp
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <div class="row" style="font-size: 12px;">

                <div class="col-md-12 col-sm-12 col-xs-12 @if($purchase->type == 'purchase_order') col-md-offset-6 @endif">
                    <div class="table-responsive">
                        <table class="table" style="font-size: 12px;">
                            <!-- <tr class="hide">
            <th>@lang('purchase.total_before_tax'): </th>
            <td></td>
            <td><span class="display_currency pull-right">{{ $total_before_tax }}</span></td>
          </tr> -->
                            <tr>
                                <th>@lang('purchase.net_total_amount'): </th>
                                <td></td>
                                <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $total_before_tax }}</span></td>
                            </tr>
                            <tr>
                                <th>@lang('purchase.discount'):</th>
                                <td>
                                    <b>(-)</b>
                                    @if($purchase->discount_type == 'percentage')
                                    ({{$purchase->discount_amount}} %)
                                    @endif
                                </td>
                                <td>
                                    <span class="display_currency pull-right" data-currency_symbol="true">
                                        @if($purchase->discount_type == 'percentage')
                                        {{$purchase->discount_amount * $total_before_tax / 100}}
                                        @else
                                        {{$purchase->discount_amount}}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>@lang('purchase.purchase_tax'):</th>
                                <td><b>(+)</b></td>
                                <td class="text-right">
                                    @if(!empty($purchase_taxes))
                                    @foreach($purchase_taxes as $k => $v)
                                    <strong><small>{{$k}}</small></strong> - <span class="display_currency pull-right" data-currency_symbol="true">{{ $v }}</span><br>
                                    @endforeach
                                    @else
                                    0.00
                                    @endif
                                </td>
                            </tr>
                            @if( !empty( $purchase->shipping_charges ) )
                            <tr>
                                <th>@lang('purchase.additional_shipping_charges'):</th>
                                <td><b>(+)</b></td>
                                <td><span class="display_currency pull-right">{{ $purchase->shipping_charges }}</span></td>
                            </tr>
                            @endif
                            @if( !empty( $purchase->additional_expense_value_1 ) && !empty( $purchase->additional_expense_key_1 ))
                            <tr>
                                <th>{{ $purchase->additional_expense_key_1 }}:</th>
                                <td><b>(+)</b></td>
                                <td><span class="display_currency pull-right">{{ $purchase->additional_expense_value_1 }}</span></td>
                            </tr>
                            @endif
                            @if( !empty( $purchase->additional_expense_value_2 ) && !empty( $purchase->additional_expense_key_2 ))
                            <tr>
                                <th>{{ $purchase->additional_expense_key_2 }}:</th>
                                <td><b>(+)</b></td>
                                <td><span class="display_currency pull-right">{{ $purchase->additional_expense_value_2 }}</span></td>
                            </tr>
                            @endif
                            @if( !empty( $purchase->additional_expense_value_3 ) && !empty( $purchase->additional_expense_key_3 ))
                            <tr>
                                <th>{{ $purchase->additional_expense_key_3 }}:</th>
                                <td><b>(+)</b></td>
                                <td><span class="display_currency pull-right">{{ $purchase->additional_expense_value_3 }}</span></td>
                            </tr>
                            @endif
                            @if( !empty( $purchase->additional_expense_value_4 ) && !empty( $purchase->additional_expense_key_4 ))
                            <tr>
                                <th>{{ $purchase->additional_expense_key_4 }}:</th>
                                <td><b>(+)</b></td>
                                <td><span class="display_currency pull-right">{{ $purchase->additional_expense_value_4 }}</span></td>
                            </tr>
                            @endif
                            <tr>
                                <th>@lang('purchase.purchase_total'):</th>
                                <td></td>
                                <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $purchase->final_total }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>



        </div>
        @if($purchase->status == "ordered")
        <div class="card-footer">
            <!-- <div class="btn btn-group"> -->
            <button class="btn btn-block btn-success btn-md" id="accept_pengajuan"><i class="fa fa-check"></i> Setujui</button>
            <button class="btn btn-block btn-danger  btn-md" id="decline_pengajuan"><i class="fa fa-times-circle"></i> Tolak</button>
            <!-- </div> -->
        </div>
        @endif
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<script type="text/javascript">
    $(document).ready(function() {
        var id = '{{$purchase->id}}'
        $('#accept_pengajuan').on('click', function() {
            $.ajax({
                method: "GET",
                url: '{{ url("update-pengajuan-acc") }}/' + id,
                dataType: "json",
                success: function(result) {
                    if (result.success) {
                        swal("Berhasil Disetujui", {
                            buttons: false,
                            icon: "success",
                            closeOnClickOutside: false,
                        });

                    } else {
                        swal("Gagal Menyetujui", {
                            buttons: false,
                            icon: "error",
                            closeOnClickOutside: false,
                        });
                    }

                    setTimeout(function() {
                        location.reload();
                    }, 500);
                }
            });
        })

        $('#decline_pengajuan').on('click', function() {
            $.ajax({
                method: "GET",
                url: '{{ url("update-pengajuan-dec") }}/' + id,
                dataType: "json",
                success: function(result) {
                    if (result.success) {
                        swal("Berhasil Ditolak", {
                            buttons: false,
                            icon: "success",
                            closeOnClickOutside: false,
                        });
                    } else {
                        swal("Gagal Menyetujui", {
                            buttons: false,
                            icon: "error",
                            closeOnClickOutside: false,
                        });
                    }
                    
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                }
            });
        })
    });
</script>
@endsection