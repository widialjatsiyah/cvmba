@php
	$subtype = '';
@endphp
@if(!empty($transaction_sub_type))
	@php
		$subtype = '?sub_type='.$transaction_sub_type;
	@endphp
@endif

@if(!empty($transactions))
	<table class="table table-slim no-border">
		@foreach ($transactions as $transaction)
			<tr class="cursor-pointer" 
	    		title="Customer: {{$transaction->contact?->name}} 
		    		@if(!empty($transaction->contact->mobile) && $transaction->contact->is_default == 0)
		    			<br/>Mobile: {{$transaction->contact->mobile}}
		    		@endif
	    		" >
				<td>
					{{ $loop->iteration}}.
				</td>
				<td>
					{{ $transaction->invoice_no }} ({{$transaction->contact?->name}})
					@if(!empty($transaction->table))
						- {{$transaction->table->name}}
					@endif
				</td>
				<td class="display_currency">
					{{ $transaction->final_total }}
				</td>
				<td>
					@if(auth()->user()->can('sell.update') || auth()->user()->can('direct_sell.update'))
					<a href="{{action([\App\Http\Controllers\SellPosController::class, 'edit'], [$transaction->id]).$subtype}}">
	    				<i class="fas fa-pen text-muted" aria-hidden="true" title="{{__('lang_v1.click_to_edit')}}"></i>
	    			</a>
	    			@endif
	    			@if(auth()->user()->can('sell.delete') || auth()->user()->can('direct_sell.delete'))
	    			<a href="{{action([\App\Http\Controllers\SellPosController::class, 'destroy'], [$transaction->id])}}" class="delete-sale" style="padding-left: 20px; padding-right: 20px"><i class="fa fa-trash text-danger" title="{{__('lang_v1.click_to_delete')}}"></i></a>
	    			@endif

				

	    			<a href="{{action([\App\Http\Controllers\SellPosController::class, 'printInvoice'], [$transaction->id])}}" class="print-invoice-link btn btn-primary">
	    				<i class="fa fa-print" aria-hidden="true" title="{{__('lang_v1.click_to_print')}}"></i> Print
	    			</a>
				</td>
			</tr>
		@endforeach
	</table>
@else
	<p>@lang('sale.no_recent_transactions')</p>
@endif