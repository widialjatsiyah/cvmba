<!-- business information here -->


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- <link rel="stylesheet" href="style.css"> -->
    <title>Receipt-{{$receipt_details->invoice_no}}</title>
</head>

<body>
    <div class="ticket">
        @if(!empty($receipt_details->logo))
        <div class="text-box centered">
            <img style="max-height: 100px; width: auto;" src="{{$receipt_details->logo}}" alt="Logo">
        </div>
        @endif
        <div class="text-box">
            <p class="centered">
                <!-- Header text -->
                @if(!empty($receipt_details->header_text))
                <span class="headings">{!! $receipt_details->header_text !!}</span>
                <br />
                @endif

                <!-- business information here -->
                @if(!empty($receipt_details->display_name))
                <span class="headings">
                    {{$receipt_details->display_name}}
                </span>
                <br />
                @endif

                @if(!empty($receipt_details->address))
                {!! $receipt_details->address !!}
                <br />
                @endif

                @if(!empty($receipt_details->contact))
                <br />{!! $receipt_details->contact !!}
                @endif
                @if(!empty($receipt_details->contact) && !empty($receipt_details->website))
                ,
                @endif
                @if(!empty($receipt_details->website))
                {{ $receipt_details->website }}
                @endif
                @if(!empty($receipt_details->location_custom_fields))
                <br>{{ $receipt_details->location_custom_fields }}
                @endif

                @if(!empty($receipt_details->sub_heading_line1))
                {{ $receipt_details->sub_heading_line1 }}<br />
                @endif
                @if(!empty($receipt_details->sub_heading_line2))
                {{ $receipt_details->sub_heading_line2 }}<br />
                @endif
                @if(!empty($receipt_details->sub_heading_line3))
                {{ $receipt_details->sub_heading_line3 }}<br />
                @endif
                @if(!empty($receipt_details->sub_heading_line4))
                {{ $receipt_details->sub_heading_line4 }}<br />
                @endif
                @if(!empty($receipt_details->sub_heading_line5))
                {{ $receipt_details->sub_heading_line5 }}<br />
                @endif

                @if(!empty($receipt_details->tax_info1))
                <br><b>{{ $receipt_details->tax_label1 }}</b> {{ $receipt_details->tax_info1 }}
                @endif

                @if(!empty($receipt_details->tax_info2))
                <b>{{ $receipt_details->tax_label2 }}</b> {{ $receipt_details->tax_info2 }}
                @endif
            </p>
        </div>
        @if(!empty($receipt_details->letter_head))
        <div class="text-box">
            <img style="width: 100%;margin-bottom: 10px;" src="{{$receipt_details->letter_head}}">
        </div>
        @endif
        <div class="border-top textbox-info">
            <p class="f-left"><strong>{!! $receipt_details->invoice_no_prefix !!}</strong></p>
            <p class="f-right">
                {{$receipt_details->invoice_no}}
            </p>
        </div>
        <div class="textbox-info">
            <p class="f-left"><strong>{!! $receipt_details->date_label !!}</strong></p>
            <p class="f-right">
                {{$receipt_details->invoice_date}}
            </p>
        </div>

        @if(!empty($receipt_details->due_date_label))
        <div class="textbox-info">
            <p class="f-left"><strong>{{$receipt_details->due_date_label}}</strong></p>
            <p class="f-right">{{$receipt_details->due_date ?? ''}}</p>
        </div>
        @endif

        @if(!empty($receipt_details->sales_person_label))
        <div class="textbox-info">
            <p class="f-left"><strong>{{$receipt_details->sales_person_label}}</strong></p>

            <p class="f-right">{{$receipt_details->sales_person}}</p>
        </div>
        @endif
        @if(!empty($receipt_details->commission_agent_label))
        <div class="textbox-info">
            <p class="f-left"><strong>{{$receipt_details->commission_agent_label}}</strong></p>

            <p class="f-right">{{$receipt_details->commission_agent}}</p>
        </div>
        @endif

        @if(!empty($receipt_details->brand_label) || !empty($receipt_details->repair_brand))
        <div class="textbox-info">
            <p class="f-left"><strong>{{$receipt_details->brand_label}}</strong></p>

            <p class="f-right">{{$receipt_details->repair_brand}}</p>
        </div>
        @endif

        @if(!empty($receipt_details->device_label) || !empty($receipt_details->repair_device))
        <div class="textbox-info">
            <p class="f-left"><strong>{{$receipt_details->device_label}}</strong></p>

            <p class="f-right">{{$receipt_details->repair_device}}</p>
        </div>
        @endif

        @if(!empty($receipt_details->model_no_label) || !empty($receipt_details->repair_model_no))
        <div class="textbox-info">
            <p class="f-left"><strong>{{$receipt_details->model_no_label}}</strong></p>

            <p class="f-right">{{$receipt_details->repair_model_no}}</p>
        </div>
        @endif

        @if(!empty($receipt_details->serial_no_label) || !empty($receipt_details->repair_serial_no))
        <div class="textbox-info">
            <p class="f-left"><strong>{{$receipt_details->serial_no_label}}</strong></p>

            <p class="f-right">{{$receipt_details->repair_serial_no}}</p>
        </div>
        @endif

        @if(!empty($receipt_details->repair_status_label) || !empty($receipt_details->repair_status))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    {!! $receipt_details->repair_status_label !!}
                </strong></p>
            <p class="f-right">
                {{$receipt_details->repair_status}}
            </p>
        </div>
        @endif

        @if(!empty($receipt_details->repair_warranty_label) || !empty($receipt_details->repair_warranty))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    {!! $receipt_details->repair_warranty_label !!}
                </strong></p>
            <p class="f-right">
                {{$receipt_details->repair_warranty}}
            </p>
        </div>
        @endif

        <!-- Waiter info -->
        @if(!empty($receipt_details->service_staff_label) || !empty($receipt_details->service_staff))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    {!! $receipt_details->service_staff_label !!}
                </strong></p>
            <p class="f-right">
                {{$receipt_details->service_staff}}
            </p>
        </div>
        @endif

        @if(!empty($receipt_details->table_label) || !empty($receipt_details->table))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    @if(!empty($receipt_details->table_label))
                    <b>{!! $receipt_details->table_label !!}</b>
                    @endif
                </strong></p>
            <p class="f-right">
                {{$receipt_details->table}}
            </p>
        </div>
        @endif

        @if (!empty($receipt_details->sell_custom_field_1_value))
        <div class="textbox-info">
            <p class="f-left"><strong>{!! $receipt_details->sell_custom_field_1_label !!}</strong></p>
            <p class="f-right">
                {{$receipt_details->sell_custom_field_1_value}}
            </p>
        </div>
        @endif
        @if (!empty($receipt_details->sell_custom_field_2_value))
        <div class="textbox-info">
            <p class="f-left"><strong>{!! $receipt_details->sell_custom_field_2_label !!}</strong></p>
            <p class="f-right">
                {{$receipt_details->sell_custom_field_2_value}}
            </p>
        </div>
        @endif
        @if (!empty($receipt_details->sell_custom_field_3_value))
        <div class="textbox-info">
            <p class="f-left"><strong>{!! $receipt_details->sell_custom_field_3_label !!}</strong></p>
            <p class="f-right">
                {{$receipt_details->sell_custom_field_3_value}}
            </p>
        </div>
        @endif
        @if (!empty($receipt_details->sell_custom_field_4_value))
        <div class="textbox-info">
            <p class="f-left"><strong>{!! $receipt_details->sell_custom_field_4_label !!}</strong></p>
            <p class="f-right">
                {{$receipt_details->sell_custom_field_4_value}}
            </p>
        </div>
        @endif

        <!-- customer info -->

        <div class="textbox-info mb-5">
            <p class="f-left">
                @if(!empty($receipt_details->customer_info))
                {!! $receipt_details->customer_info !!}

                @endif
            </p>
        </div>


        @if(!empty($receipt_details->client_id_label))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    {{ $receipt_details->client_id_label }}
                </strong></p>
            <p class="f-right">
                {{ $receipt_details->client_id }}
            </p>
        </div>
        @endif

        @if(!empty($receipt_details->customer_tax_label))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    {{ $receipt_details->customer_tax_label }}
                </strong></p>
            <p class="f-right">
                {{ $receipt_details->customer_tax_number }}
            </p>
        </div>
        @endif

        @if(!empty($receipt_details->customer_custom_fields))
        <div class="textbox-info">
            <p class="centered">
                {!! $receipt_details->customer_custom_fields !!}
            </p>
        </div>
        @endif

        @if(!empty($receipt_details->customer_rp_label))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    {{ $receipt_details->customer_rp_label }}
                </strong></p>
            <p class="f-right">
                {{ $receipt_details->customer_total_rp }}
            </p>
        </div>
        @endif
        @if(!empty($receipt_details->shipping_custom_field_1_label))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    {!!$receipt_details->shipping_custom_field_1_label!!}
                </strong></p>
            <p class="f-right">
                {!!$receipt_details->shipping_custom_field_1_value ?? ''!!}
            </p>
        </div>
        @endif
        @if(!empty($receipt_details->shipping_custom_field_2_label))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    {!!$receipt_details->shipping_custom_field_2_label!!}
                </strong></p>
            <p class="f-right">
                {!!$receipt_details->shipping_custom_field_2_value ?? ''!!}
            </p>
        </div>
        @endif
        @if(!empty($receipt_details->shipping_custom_field_3_label))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    {!!$receipt_details->shipping_custom_field_3_label!!}
                </strong></p>
            <p class="f-right">
                {!!$receipt_details->shipping_custom_field_3_value ?? ''!!}
            </p>
        </div>
        @endif
        @if(!empty($receipt_details->shipping_custom_field_4_label))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    {!!$receipt_details->shipping_custom_field_4_label!!}
                </strong></p>
            <p class="f-right">
                {!!$receipt_details->shipping_custom_field_4_value ?? ''!!}
            </p>
        </div>
        @endif
        @if(!empty($receipt_details->shipping_custom_field_5_label))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    {!!$receipt_details->shipping_custom_field_5_label!!}
                </strong></p>
            <p class="f-right">
                {!!$receipt_details->shipping_custom_field_5_value ?? ''!!}
            </p>
        </div>
        @endif
        @if(!empty($receipt_details->sale_orders_invoice_no))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    @lang('restaurant.order_no')
                </strong></p>
            <p class="f-right">
                {!!$receipt_details->sale_orders_invoice_no ?? ''!!}
            </p>
        </div>
        @endif

        @if(!empty($receipt_details->sale_orders_invoice_date))
        <div class="textbox-info">
            <p class="f-left"><strong>
                    @lang('lang_v1.order_dates')
                </strong></p>
            <p class="f-right">
                {!!$receipt_details->sale_orders_invoice_date ?? ''!!}
            </p>
        </div>
        @endif

        <div class="border-top textbox-info">
            <!--<div class="bb-lg mt-1 mb-3"></div>-->
            <table style="padding-top: 0px !important; border:none;" class=" width-100 table-f-12 mb-0">
                <tbody>

                    @foreach($receipt_details->lines as $line)
                    @if($line['quantity']>0)
                    <tr class="bb-lg">
                        <td class="description">
                            <div style="display:flex; width: 100%;">
                                <p class="m-0 mt-1" style="white-space: nowrap;">#{{$loop->iteration}}.&nbsp;</p>
                                <p class="text-left m-0 mt-1 pull-left">{{$line['name']}}

                                    <br>

                                </p>
                            </div>
                            <div style="display:flex; width: 100%;">
                                <p class="text-left width-60 quantity m-0 bw" style="direction: ltr;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{$line['quantity']}} {{$line['units']}}

                                    x {{ $line['unit_price']}}


                                </p>
                                <p class="text-right width-40 price m-0 bw">{{$line['line_total']}}</p>

                            </div>
                        </td>
                    </tr>
                    @endif

                    @endforeach
                </tbody>
            </table>




            <div class="flex-box">
                <p class="left text-left">
                    <strong>{!! $receipt_details->subtotal_label !!}</strong>
                </p>
                <p class="width-50 text-right">
                    <strong>{{$receipt_details->subtotal}}</strong>
                </p>
            </div>


            <!-- Discount -->
            @if( !empty($receipt_details->discount) )
            <div class="flex-box">
                <p class="width-50 text-left">
                    {!! $receipt_details->discount_label !!}
                </p>

                <p class="width-50 text-right">
                    (-) {{$receipt_details->discount}}
                </p>
            </div>
            @endif


            @if( !empty($receipt_details->tax) )
            <div class="flex-box">
                <p class="width-50 text-left">
                    {!! $receipt_details->tax_label !!}
                </p>
                <p class="width-50 text-right">
                    (+) {{$receipt_details->tax}}
                </p>
            </div>
            @endif
            <div class="flex-box">
                <p class="width-50 text-left">
                    <strong>	Total </strong>
                </p>
                <p class="width-50 text-right">
                    <strong>{{$receipt_details->total}}</strong>
                </p>
            </div>


            <br>
            <br>

            @if(!empty($receipt_details->footer_text))
            <p class="centered">
                {!! $receipt_details->footer_text !!}
            </p>
            @endif
        </div>
    </div>
        <!-- <button id="btnPrint" class="hidden-print">Print</button>
        <script src="script.js"></script> -->
</body>

</html>

<style type="text/css">
    .f-8 {
        font-size: 8px !important;
    }

    body {
        color: #000000;
    }

    @media print {

        * {
            font-family: "Courier New";
            font-size: 10px;
            word-break: break-all;
            line-height: 95%;
            font-weight: bolder;
        }

        .f-8 {
            font-size: 8px !important;
        }

        .headings {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .sub-headings {
            font-size: 12px;
            font-weight: 700;
        }

        .border-top {
            border-top: 1px solid #242424;
        }

        .border-bottom {
            border-bottom: 1px solid #242424;
        }

        .border-bottom-dotted {
            border-bottom: 1px dotted darkgray;
        }

        td.serial_number,
        th.serial_number {
            width: 5%;
            max-width: 5%;
        }

        td.description,
        th.description {
            width: 35%;
            max-width: 35%;
        }

        td.quantity,
        th.quantity {
            width: 15%;
            max-width: 15%;
            word-break: break-all;
        }

        td.unit_price,
        th.unit_price {
            width: 25%;
            max-width: 25%;
            word-break: break-all;
        }

        td.price,
        th.price {
            width: 20%;
            max-width: 20%;
            word-break: break-all;
        }

        .centered {
            text-align: center;
            align-content: center;
        }

        .ticket {
            width: 100%;
            max-width: 100%;
        }

        img {
            max-width: inherit;
            width: auto;
        }

        .hidden-print,
        .hidden-print * {
            display: none !important;
        }
    }

    .table-info {
        width: 100%;
    }

    .table-info tr:first-child td,
    .table-info tr:first-child th {
        padding-top: 8px;
    }

    .table-info th {
        text-align: left;
    }

    .table-info td {
        text-align: right;
    }

    .logo {
        float: left;
        width: 35%;
        padding: 10px;
    }

    .text-with-image {
        float: left;
        width: 65%;
    }

    .text-box {
        width: 100%;
        height: auto;
    }

    .m-0 {
        margin: 0;
    }

    .textbox-info {
        clear: both;
    }

    .textbox-info p {
        margin-bottom: -5px
    }

    .flex-box {
        display: flex;
        width: 100%;
    }

    .flex-box p {
        width: 50%;
        margin-bottom: -8px;
        white-space: nowrap;
    }

    .table-f-12 th,
    .table-f-12 td {
        font-size: 10px;
        word-break: break-word;
    }

    .bw {
        word-break: break-word;
    }

    .bb-lg {
        border-bottom: 1px solid lightgray;
    }
</style>