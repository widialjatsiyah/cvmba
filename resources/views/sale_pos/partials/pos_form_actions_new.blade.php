@php
$is_mobile = isMobile();
@endphp
<!-- <div class="row"> -->
<!-- <div class="pos-form-actions"> -->
<div class="card m-1  bg-navy" style="box-shadow: 3px 12px 10px rgba(220,110,110,0.5);">

	<div class="card-body  pos-total text-white">
		<span class="text" style="width: 100%;">@lang('sale.total_payable')</span><br>
		<input type="hidden" name="final_total"
			id="final_total_input" value=0>
		<span id="total_payable" class="number pull-right" style="font-size: 35px;margin-top:40px">0</span>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		
	</div>
</div>
<div class="row" style="margin-top: 40px;">
	<div class="col-md-12 ">

		@if(!Gate::check('disable_pay_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
		<button type="button" class="btn bg-red-gradient col-md-12 btn-default btn-lg @if(!$is_mobile) @endif btn-flat no-print @if($pos_settings['disable_pay_checkout'] != 0) hide @endif @if($is_mobile) col-xs-6 @endif" id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')"><i class="fas fa-money-check-alt" aria-hidden="true"></i> @lang('lang_v1.checkout_multi_pay') </button>
		@endif
        
		@if(!Gate::check('disable_draft') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
		<button type="button" class="@if($is_mobile) col-xs-6 @endif btn bg-info text-white btn-default btn-flat @if($pos_settings['disable_draft'] != 0) hide @endif" id="pos-draft" @if(!empty($only_payment)) disabled @endif><i class="fas fa-edit"></i> @lang('sale.draft')</button>
		@endif
		@if(!Gate::check('disable_quotation') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
		<!--<button type="button" class="btn btn-default bg-yellow btn-flat col-md-4 @if($is_mobile) col-xs-6 @endif" id="pos-quotation" @if(!empty($only_payment)) disabled @endif><i class="fas fa-edit"></i> @lang('lang_v1.quotation')</button>-->
		@endif
		@if(!Gate::check('disable_suspend_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
		@if(empty($pos_settings['disable_suspend']))
		<button type="button"
			class="@if($is_mobile) col-xs-4 @endif  col-md-4 btn bg-red btn-default btn-flat no-print pos-express-finalize"
			data-pay_method="suspend"
			title="@lang('lang_v1.tooltip_suspend')" @if(!empty($only_payment)) disabled @endif>
			<i class="fas fa-pause" aria-hidden="true"></i>
			@lang('lang_v1.suspend')
		</button>
		@endif
		@endif
		
		@if(!Gate::check('disable_credit_sale') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
		@if(empty($pos_settings['disable_credit_sale_button']))
		<input type="hidden" name="is_credit_sale" value="0" id="is_credit_sale">
		<button type="button"
			class="btn bg-purple btn-default btn-flat col-md-4 no-print pos-express-finalize @if($is_mobile) col-xs-6 @endif"
			data-pay_method="credit_sale"
			title="@lang('lang_v1.tooltip_credit_sale')" @if(!empty($only_payment)) disabled @endif>
			<i class="fas fa-check" aria-hidden="true"></i> @lang('lang_v1.credit_sale')
		</button>
		@endif
		@endif
			@if(!isset($pos_settings['hide_recent_trans']) || $pos_settings['hide_recent_trans'] == 0)
		<button type="button" class="pull-right btn btn-primary btn-flat col-md-4 @if($is_mobile) col-xs-6 @endif" data-toggle="modal" data-target="#recent_transactions_modal" id="recent-transactions"> <i class="fas fa-clock"></i> @lang('lang_v1.recent_transactions')</button>
		@endif

		@if(!Gate::check('disable_card') )
		<!--<button type="button"-->
		<!--	class="btn bg-maroon btn-default btn-flat no-print @if(!empty($pos_settings['disable_suspend'])) @endif pos-express-finalize @if(!array_key_exists('card', $payment_types)) hide @endif @if($is_mobile) col-xs-6 @endif"-->
		<!--	data-pay_method="card"-->
		<!--	title="@lang('lang_v1.tooltip_express_checkout_card')">-->
		<!--	<i class="fas fa-credit-card" aria-hidden="true"></i> @lang('lang_v1.express_checkout_card')-->
		<!--</button>-->
		@endif



		@if(!Gate::check('disable_express_checkout') || auth()->user()->can('superadmin') || auth()->user()->can('admin'))
		<button type="button" class="btn btn-success @if(!$is_mobile) @endif btn-flat no-print @if($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif pos-express-finalize @if($is_mobile) col-xs-6 @endif" data-pay_method="cash" title="@lang('tooltip.express_checkout')"> <i class="fas fa-money-bill-alt" aria-hidden="true"></i> @lang('lang_v1.express_checkout_cash')</button>
		@endif


		@if(empty($edit))
		<button type="button" class="bg-red-gardient text-white btn-default btn-flat col-md-12 p-1  btn-flat @if($is_mobile) col-xs-6 @else  @endif" id="pos-cancel"> <i class="fas fa-window-close"></i> @lang('sale.cancel')</button>
		@else
		<button type="button" class="btn btn-danger btn-flat hide @if($is_mobile) col-xs-6 @else btn-xs @endif" id="pos-delete" @if(!empty($only_payment)) disabled @endif> <i class="fas fa-trash-alt"></i> @lang('messages.delete')</button>
		@endif


	

	</div>
</div>

<!-- </div> -->
<!-- </div> -->
@if(isset($transaction))
@include('sale_pos.partials.edit_discount_modal', ['sales_discount' => $transaction->discount_amount, 'discount_type' => $transaction->discount_type, 'rp_redeemed' => $transaction->rp_redeemed, 'rp_redeemed_amount' => $transaction->rp_redeemed_amount, 'max_available' => !empty($redeem_details['points']) ? $redeem_details['points'] : 0])
@else
@include('sale_pos.partials.edit_discount_modal', ['sales_discount' => $business_details->default_sales_discount, 'discount_type' => 'percentage', 'rp_redeemed' => 0, 'rp_redeemed_amount' => 0, 'max_available' => 0])
@endif

@if(isset($transaction))
@include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $transaction->tax_id])
@else
@include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $business_details->default_sales_tax])
@endif

@include('sale_pos.partials.edit_shipping_modal')