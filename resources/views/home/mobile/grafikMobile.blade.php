@extends('layouts.app_mobile')
@section('title', __('home.home'))

@section('content')

<!-- Main content -->
<div class="card bg-primary">
    <div class="card-body">
        <span style="font-weight: bolder;"> Grafik </span>
    </div>
</div>
<!-- Main content -->
<section class="content" style="margin-bottom: 100px;">
    <br>
   
    <div class="row">
    <div class="col-md-12 col-lg-6">
    @component('components.widget', ['class' => 'box-danger', 'title' => 'Penjualan 7 Hari Terakhir'])
            {!! $sells_chart_1->container() !!}
            @endcomponent
        </div>
        <div class="col-md-12 col-lg-6">
            @component('components.widget', ['class' => 'box-danger', 'title' => 'Penjualan 30 Hari Terakhir'])
            {!! $sells_chart_1->container() !!}
            @endcomponent

        </div>

    </div>

    
    <!-- can('dashboard.data') end -->
</section>
@stop
@section('javascript')

<script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

{!! $sells_chart_1->script() !!}
{!! $sells_chart_2->script() !!}

<script type="text/javascript">
  
</script>
@endsection