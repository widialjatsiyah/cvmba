@extends('layouts.app_mobile')
@section('title', __('home.home'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header content-header-custom " style="margin-bottom:100px;">
    <div class="row">
        <div class="col-md-5 col-xs-12 mb-3">
            <span style="font-weight: bolder;">Selamat Datang, {{ Session::get('user.first_name')}}
            </span>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-12">

            <div class="form-group ">
                <div class="input-group">
                    <button type="button" class="btn btn-block btn-dark" id="dashboard_date_filter">
                        <span>
                            <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                        </span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">

            <div class="info-box info-box-new-style bg-primary">
                <span class="info-box-icon bg-blue-light"><i class="ion ion-ios-cart-outline text-white"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text" style="color:#fff">{{ __('home.total_sell') }}</span>
                    <span class="info-box-number total_sell" style="color:#fff"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">

            <div class="info-box info-box-new-style bg-success">
                <span class="info-box-icon bg-blue-light"><i class="ion ion-stats-bars text-white"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text" style="color:#fff">Profit</span>
                    <span class="info-box-number total_profit" style="color:#fff"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
    </div>

    <div class="row" >
        <div class="col-sm-12">
            <div class="card bg-light p-3" style="box-shadow: 7px 7px 4px lightblue;">
                <div class="card-body">
                    <div class="row text-center mb-2">
                        <div class="col-sm-12">
                        <span class="text-secondary"> Detail Penjualan</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6" style="width: 50%;">
                            <span class="info-box-text">DC Pagaden</span>
                            <span class="info-box-number total_sell_dc" style="font-size: 12px;"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                        </div>
                        <div class="col-xs-6" style="width: 50%;">
                            <span class="info-box-text">Arzun Grosir</span>
                            <span class="info-box-number total_sell_grosir" style="font-size: 12px;"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-xs-6" style="width: 50%;">
                            <span class="info-box-text">Arzun Mart</span>
                            <span class="info-box-number total_sell_mart" style="font-size: 12px;"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                        </div>
                        <div class="col-xs-6" style="width: 50%;">
                            <span class="info-box-text">Le Pari Shop</span>
                            <span class="info-box-number total_sell_lepari" style="font-size: 12px;"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                        </div>
                    </div>


                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
    </div>

    <div class="row mt-3" >
        <div class="col-sm-12">
            <div class="card bg-light p-3 border-top text-success" style="box-shadow: 7px 7px 4px lightblue;">
                <div class="card-body">
                    <div class="row text-center mb-2">
                        <div class="col-sm-12">
                        <span class="text-success"> Detail Profit</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6" style="width: 50%;">
                            <span class="info-box-text">DC Pagaden</span>
                            <span class="info-box-number total_profit_dc" style="font-size: 12px;"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                        </div>
                        <div class="col-xs-6" style="width: 50%;">
                            <span class="info-box-text">Arzun Grosir</span>
                            <span class="info-box-number total_profit_grosir" style="font-size: 12px;"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-xs-6" style="width: 50%;">
                            <span class="info-box-text">Arzun Mart</span>
                            <span class="info-box-number total_profit_mart" style="font-size: 12px;"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                        </div>
                        <div class="col-xs-6" style="width: 50%;">
                            <span class="info-box-text">Le Pari Shop</span>
                            <span class="info-box-number total_profit_lepari" style="font-size: 12px;"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                        </div>
                    </div>


                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
    </div>

        <div class="card bg-light p-3  mt-3" style="box-shadow: 7px 7px 4px lightblue;">
            <div class="card-header bg-light" >
                <div class="row  mb-2">
                    <div class="col-sm-12 text-center">
                        <span class="text-secondary">Nilai Stok</span>
                    </div>
                </div>
                <div class="row  mb-3">
                    <div class="col-sm-12 text-center">
                    <div class="form-group">
                    {!! Form::select('dashboard_location', $all_locations, null, ['class' => 'form-select select2  ', 'placeholder' => __('lang_v1.select_location'), 'id' => 'dashboard_location']); !!}
              
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6" style="width: 50%;">
                        <span class="info-box-text">@lang('lang_v1.by_purchase_price')</span>
                        <span class="info-box-number closing_stock_by_pp" style="font-size: 12px;"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                    </div>
                    <div class="col-xs-6" style="width: 50%;">
                        <span class="info-box-text">@lang('lang_v1.by_sale_price')</span>
                        <span class="info-box-number closing_stock_by_sp" style="font-size: 12px;"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-xs-6" style="width: 50%;">
                        <span class="info-box-text">@lang('lang_v1.potential_profit')</span>
                        <span class="info-box-number potential_profit" style="font-size: 12px;"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                    </div>
                    <div class="col-xs-6" style="width: 50%;">
                        <span class="info-box-text">@lang('lang_v1.profit_margin')</span>
                        <span class="info-box-number profit_margin" style="font-size: 12px;"><i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>
                    </div>
                </div>
            </div>
        </div>

        
</section>



@stop
@section('javascript')

<script src="{{ asset('js/home_mobile.js?v=' . $asset_v+8) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

<script type="text/javascript">
    $(document).ready(function() {



    });
</script>
@endsection