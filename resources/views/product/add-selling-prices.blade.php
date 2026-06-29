@extends('layouts.app')
@section('title', __('lang_v1.add_selling_price_group_prices'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('lang_v1.add_selling_price_group_prices')</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row mb-3">
		<div class="col-sm-12">
			<a class="btn btn-primary" style="float: right;" data-toggle="modal" data-target="#TambahSatuan"><i class="fa fa-plus"></i> Tambah Baru</a>

			<!-- The Modal -->
			<div class="modal" id="TambahSatuan">
				<div class="modal-dialog">
					
				{!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'saveSellingPricesSatuan']), 'method' => 'post', 'id' => 'add_selling_price_form' ]) !!}
					<div class="modal-content">

						<!-- Modal Header -->
						<div class="modal-header">
							<h4 class="modal-title">{{$product->name}} ({{$product->sku}})</h4>
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>

						<!-- Modal body -->
						{!! Form::hidden('product_id', $product->id); !!}
						<div class="modal-body">
							<div class="form-group">
								<label for="unit">Harga Group *</label>
								{!! Form::select(
								"price_group_id",
								$price_groups_dropdown,
								'',
								[
								'class' => 'form-control select2',
								'style' => 'width:100%',
								'id' => 'product_list_filter_unit_id',
								'required' =>true,
								'placeholder' => __('lang_v1.all')
								]
								) !!}
							</div>
							<div class="form-group">
								<label for="unit">Satuan *</label>
								{!! Form::select(
								"unit_id",
								$units,
								'',
								[
								'class' => 'form-control select2',
								'style' => 'width:100%',
								'required' =>true,
								'id' => 'product_list_filter_unit_id',
								'placeholder' => __('lang_v1.all')
								]
								) !!}
							</div>
							<input type="hidden" name="group_id">

							<div class="form-group">
								<label for="unit">Konversi/Isi *</label>
								<input type="number" class="form-control input_number input-sm" name="konversi" required>
							</div>

							
							<div class="form-group">
								<label for="unit">Harga Jual *</label>
								<input type="number" class="form-control input_number input-sm" name="price_inc_tax" required>
							</div>

							<div class="form-group">
								<label for="unit">Default *</label>
								<select name="is_main" class="form-control" required>
									<option value="">-- Pilih ---</option>
									<option value="1">Ya</option>
									<option value="0">Tidak</option>
								</select>
							</div>

						</div>

						<!-- Modal footer -->
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary" >Simpan</button>
							<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
						</div>

					</div>
					
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>

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
						<div class="col-md-9">

							@foreach ($groupedData as $groupId => $prices)

							@foreach ($price_groups as $price_group)
							@if ($price_group->id == $groupId)
							<h3>{{ $price_group->name }}</h3>
							@endif
							@endforeach

							<table style="width: 100%;" class="table table-condensed table-bordered text-center table-striped">
								<thead>
									<tr class="bg-green">
										<th rowspan="2" style="width:20%; vertical-align: middle;">HARGA</th>
										<th rowspan="2" style="width:10%;vertical-align: middle;">SATUAN</th>
										<th rowspan="2" style="width:10%;vertical-align: middle;">KONVERSI / ISI</th>
										<th rowspan="2" style="width:15%;vertical-align: middle;">DEFAULT</th>
										<th style="width:25%">HARGA SATUAN</th>
										<th rowspan="2"  style="width:5%;vertical-align: middle;">#</th>
									</tr>
									<tr class="bg-green">
									   <th>TOTAL PROFIT</th>
									</tr>

								</thead>
								<tbody>
									@foreach ($prices as $price)
									<tr>
										<td>
											<input type="text" class="form-control input_number input-sm" name="group_prices[{{$price['group_price_id']}}][price]" value="{{@num_format($price['price'])}}">

										</td>
										<td>
											<input type="hidden" name="group_prices[{{$price['group_price_id']}}][product_id]" value="{{$price['variation_id']}}">
											<input type="hidden" name="group_prices[{{$price['group_price_id']}}][group_id]" value="{{$price['group_id']}}">
											{!! Form::select(
											"group_prices[{$price['group_price_id']}][unit_id]",
											$units,
											$price['unit_id'],
											[
											'class' => 'form-control select2',
											'style' => 'width:100%',
											'id' => 'product_list_filter_unit_id',
											'placeholder' => __('lang_v1.all')
											]
											) !!}

										</td>
										<td>
											<input type="number" class="form-control input_number input-sm" name="group_prices[{{$price['group_price_id']}}][konversi]" value="{{$price['konversi']}}">
										</td>
										<td>
											<select name="group_prices[{{$price['group_price_id']}}][is_main]" class="form-control">
												<option value="">-- Pilih ---</option>
												<option value="1" {{( $price["is_main"]==1)?'selected':''}}>Ya</option>
												<option value="0" {{( $price["is_main"]!=1)?'selected':''}}>Tidak</option>
											</select>
										</td>
											<td>
											    <?php
											    $harga = $price['price'] ? $price['price'] : 1;
											    $konversi = $price['konversi'] ? $price['konversi'] : 1;
											    $hargaSatuan = $harga / $konversi;
											    $hargaJualDefault = $product->variations->first()->sell_price_inc_tax * $konversi;
											    $selisih =  $harga  - $hargaJualDefault;
											    ?>
											Rp. {{@num_format( $hargaSatuan )}}<br>
											<b> Rp. {{ @num_format($selisih) }} </b>
										</td>
										<td>
											<a href="#" onclick="hapusSatuan(this)" data-id="{{$price['group_price_id']}}" class="btn btn-danger"><i class="fa fa-window-close"></i></a>
										</td>

									
									</tr>
									@endforeach




								</tbody>
							</table>

							@endforeach

						</div>
						<div class="col-md-3">
							<br>
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
				<!-- <div class="btn-group"> -->
				<!-- <button id="opening_stock_button" @if($product->enable_stock == 0) disabled @endif type="submit" value="submit_n_add_opening_stock" class="btn bg-purple submit_form btn-big">@lang('lang_v1.save_n_add_opening_stock')</button> -->
				<!-- <button type="submit" value="save_n_add_another" class="btn bg-maroon submit_form btn-big">@lang('lang_v1.save_n_add_another')</button> -->
				<button type="submit" value="submit" class="btn btn-primary submit_form btn-big">Edit</button>
				<!-- </div> -->
			</div>
		</div>
	</div>

	{!! Form::close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
	function hapusSatuan(elem){
    // alert("ada");
	var id = $(elem).data('id');
	$.ajax({
        method: 'get',
        url: '/products/delete-satuan/'+id,
        dataType: 'json',
        success: function(result) {
           console.log(result);
		   if(result){

			toastr.success(result.message);
		   }else{

			toastr.error(result.message);
		   }
		   window.setTimeout(function() {  location.reload();}, 1000);
        },
    });
}

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