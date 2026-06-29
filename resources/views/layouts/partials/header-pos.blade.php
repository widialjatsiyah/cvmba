<!-- default value -->
@php
    $go_back_url = action([\App\Http\Controllers\SellPosController::class, 'index']);
    $transaction_sub_type = '';
    $view_suspended_sell_url = action([\App\Http\Controllers\SellController::class, 'index']).'?suspended=1';
    $pos_redirect_url = action([\App\Http\Controllers\SellPosController::class, 'create']);
@endphp

@if(!empty($pos_module_data))
    @foreach($pos_module_data as $key => $value)
        @php
            if(!empty($value['go_back_url'])) {
                $go_back_url = $value['go_back_url'];
            }

            if(!empty($value['transaction_sub_type'])) {
                $transaction_sub_type = $value['transaction_sub_type'];
                $view_suspended_sell_url .= '&transaction_sub_type='.$transaction_sub_type;
                $pos_redirect_url .= '?sub_type='.$transaction_sub_type;
            }
        @endphp
    @endforeach
@endif
<input type="hidden" name="transaction_sub_type" id="transaction_sub_type" value="{{$transaction_sub_type}}">
@inject('request', 'Illuminate\Http\Request')
<div class="col-md-12 no-print pos-header">
  <input type="hidden" id="pos_redirect_url" value="{{$pos_redirect_url}}">
  <div class="row">
    <div class="col-md-6">
      <div class=" m-1" style="display: flex;">
        <p>
          @if(empty($transaction->location_id))
            @if(count($business_locations) > 1)
            <div style="width: 28%;">
               {!! Form::select('select_location_id', $business_locations, $default_location->id ?? null , ['class' => 'form-control input-sm',
                'id' => 'select_location_id', 
                'required', 'autofocus'], $bl_attributes); !!}
            </div>
            @else
              {{$default_location->name}}
            @endif
          @endif

          @if(!empty($transaction->location_id)) {{$transaction->location->name}} @endif &nbsp; <span class="curr_datetime">{{ @format_datetime('now') }}</span> &nbsp;
          <i class="fa fa-keyboard hover-q text-danger" style="font-size:22px" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('sale_pos.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i>
        </p>
      </div>
    </div>
    <div class="col-md-6">
      <a href="{{$go_back_url}}" title="{{ __('lang_v1.go_back') }}" class="btn btn-info  btn-xs m-1 pull-right">
        <strong><i class="fa fa-backward fa-lg"></i></strong>
      </a>
      @if(!empty($pos_settings['inline_service_staff']))
        <button type="button" id="show_service_staff_availability" title="{{ __('lang_v1.service_staff_availability') }}" class="btn btn-primary  btn-xs m-1 pull-right" data-container=".view_modal" 
          data-href="{{ action([\App\Http\Controllers\SellPosController::class, 'showServiceStaffAvailibility'])}}">
            <strong><i class="fa fa-users fa-lg"></i></strong>
        </button>
      @endif

      @can('close_cash_register')
      <button type="button" id="close_register" title="{{ __('cash_register.close_register') }}" class="btn btn-danger  btn-xs m-1 btn-modal pull-right" data-container=".close_register_modal" 
          data-href="{{ action([\App\Http\Controllers\CashRegisterController::class, 'getCloseRegister'])}}">
            <strong><i class="fa fa-window-close fa-lg"></i> TUTUP KASIR</strong>
      </button>
      @endcan
      
      @can('view_cash_register')
      <button type="button" id="register_details" title="{{ __('cash_register.register_details') }}" class="btn btn-success  btn-xs m-1 btn-modal pull-right" data-container=".register_details_modal" 
          data-href="{{ action([\App\Http\Controllers\CashRegisterController::class, 'getRegisterDetails'])}}">
            <strong><i class="fa fa-briefcase fa-lg" aria-hidden="true"></i></strong>
      </button>
      @endcan

      <button title="@lang('lang_v1.calculator')" id="btnCalculator" type="button" class="btn btn-success pull-right m-1 btn-xs m-10 popover-default" data-toggle="popover" data-trigger="click" data-content='@include("layouts.partials.calculator")' data-html="true" data-placement="bottom">
            <strong><i class="fa fa-calculator fa-lg" aria-hidden="true"></i></strong>
      </button>

      <button type="button" class="btn btn-danger  btn-xs m-1 pull-right popover-default" id="return_sale" title="@lang('lang_v1.sell_return')" data-toggle="popover" data-trigger="click" data-content='<div class="m-8"><input type="password" class="form-control" placeholder="masukan pin staff" id="pin_staff_verify"></div><div class="w-100 text-center"><button type="button" class="btn btn-danger" id="send_pin_staff_verify">@lang("lang_v1.send")</button></div>' data-html="true" data-placement="bottom">
            <strong><i class="fas fa-undo fa-lg"></i> RETUR</strong>
      </button>
      
      <!--<button type="button" class="btn btn-danger  btn-xs m-1 pull-right popover-default" id="return_sale" title="@lang('lang_v1.sell_return')" data-toggle="popover" data-trigger="click" data-content='<div class="m-8"><input type="text" class="form-control" placeholder="@lang("sale.invoice_no")" id="send_for_sell_return_invoice_no"></div><div class="w-100 text-center"><button type="button" class="btn btn-danger" id="send_for_sell_return">@lang("lang_v1.send")</button></div>' data-html="true" data-placement="bottom">-->
      <!--      <strong><i class="fas fa-undo fa-lg"></i></strong>-->
      <!--</button>-->

      <button type="button" title="{{ __('lang_v1.full_screen') }}" class="btn btn-primary  hidden-xs btn-xs m-1 pull-right" id="full_screen">
            <strong><i class="fa fa-window-maximize fa-lg"></i></strong>
      </button>

      <button type="button" id="view_suspended_sales" title="{{ __('lang_v1.view_suspended_sales') }}" class="btn bg-yellow  btn-xs m-1 btn-modal pull-right" data-container=".view_modal" 
          data-href="{{$view_suspended_sell_url}}">
            <strong><i class="fa fa-pause-circle fa-lg"></i> PENDING </strong>
      </button>
      @if(empty($pos_settings['hide_product_suggestion']) && isMobile())
        <button type="button" title="{{ __('lang_v1.view_products') }}"   
          data-placement="bottom" class="btn btn-success  btn-xs m-1 btn-modal pull-right" data-toggle="modal" data-target="#mobile_product_suggestion_modal">
            <strong><i class="fa fa-cubes fa-lg"></i></strong>
        </button>
      @endif

      @if(Module::has('Repair') && $transaction_sub_type != 'repair')
        @include('repair::layouts.partials.pos_header')
      @endif

        
        @can('expense.add')
        <button type="button" title="{{ __('expense.add_expense') }}"   
          data-placement="bottom" class="btn bg-purple  btn-xs m-1 btn-modal pull-right" id="add_expense">
            <strong><i class="fa fas fa-minus-circle"></i> @lang('expense.add_expense')</strong>
        </button>
        @endcan

    </div>
    
  </div>
</div>
