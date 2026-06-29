@extends('layouts.app')
@section('title', __('lang_v1.add_selling_price_group_prices'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('lang_v1.add_selling_price_group_prices')</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'saveSellingPrices']), 'method' => 'post', 'id' => 'selling_price_form' ]) !!}
	{!! Form::hidden('product_id', $product->id); !!}
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header">
					<h3 class="box-title">@lang('sale.product'): {{$product->name}} ({{$product->sku}})</h3>
				</div>

				<div class="box-body">
					<div class="row">
						<div class="col-xs-8">

							@foreach ($groupedData as $groupId => $prices)

							@foreach ($price_groups as $price_group)
							@if ($price_group->id == $groupId)
							<h3>{{ $price_group->name }}</h3>
							@endif
							@endforeach

							<table style="width: 90%;" class="table table-condensed table-bordered text-center table-striped">
								<thead>
									<tr class="bg-green">
										<th>Harga</th>
										<th>Satuan</th>
										<th>konversi</th>
										<th>utama</th>
									</tr>

								</thead>
								<tbody>
									@foreach ($prices as $price)
									<tr>
										<td>
											<input type="text" class="form-control input_number input-sm" name="group_price[{{$price['group_price_id']}}]" value="{{@num_format($price['price'])}}">

										</td>
										<td>
											{!! Form::select('unit_id', $units, $price['unit_id'], ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_unit_id', 'placeholder' => __('lang_v1.all')]); !!}


										</td>
										<td>
											<input type="number" class="form-control input_number input-sm" name="konversi[{{$price['group_price_id']}}]" value="{{$price['konversi']}}">
										</td>
										<td>
											<select name="is_main[{{$price['group_price_id']}}]" class="form-control">
												<option value="">-- Pilih ---</option>
												<option value="1">Ya</option>
												<option value="0">Tidak</option>
											</select>
										</td>
									</tr>
									@endforeach




								</tbody>
							</table>

							@endforeach

						</div>
						<div class="col-xs-4">
						<div class="table-responsive">
								<table class="table table-condensed table-bordered text-center table-striped">
									<thead>
										<tr>
											@if($product->type == 'variable')
											<th>
												@lang('lang_v1.variation')
											</th>
											@endif
											<th>@lang('lang_v1.default_selling_price_inc_tax')</th>
											
										</tr>
									</thead>
									<tbody>
										@foreach($product->variations as $variation)
										<tr>
											@if($product->type == 'variable')
											<td>
												{{$variation->product_variation->name}} - {{$variation->name}} ({{$variation->sub_sku}})
											</td>
											@endif
											<td><span class="display_currency" data-currency_symbol="true">{{$variation->sell_price_inc_tax}}</span></td>
											
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			{!! Form::hidden('submit_type', 'save', ['id' => 'submit_type']); !!}
			<div class="text-center">
				<div class="btn-group">
					<!-- <button id="opening_stock_button" @if($product->enable_stock == 0) disabled @endif type="submit" value="submit_n_add_opening_stock" class="btn bg-purple submit_form btn-big">@lang('lang_v1.save_n_add_opening_stock')</button> -->
					<button type="submit" value="save_n_add_another" class="btn bg-maroon submit_form btn-big">@lang('lang_v1.save_n_add_another')</button>
					<button type="submit" value="submit" class="btn btn-primary submit_form btn-big">@lang('messages.save')</button>
				</div>
			</div>
		</div>
	</div>

	{!! Form::close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
	$(document).ready(function() {
		$('button.submit_form').click(function(e) {
			e.preventDefault();
			$('input#submit_type').val($(this).attr('value'));

			if ($("form#selling_price_form").valid()) {
				$("form#selling_price_form").submit();
			}
		});
	});
</script>
@endsection