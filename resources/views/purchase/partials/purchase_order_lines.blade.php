
@if(!empty($purchase_order->purchase_lines) && count($purchase_order->purchase_lines) > 0)

    @foreach($purchase_order->purchase_lines as $purchase_line)
        @if($purchase_line->quantity - $purchase_line->po_quantity_purchased > 0)
            @include('purchase.partials.purchase_entry_row', [
                'variations' => [$purchase_line->variations],
                'product' => $purchase_line->product,
                'row_count' => $row_count,
                'variation_id' => $purchase_line->variation_id,
                'taxes' => $taxes,
                'currency_details' => $currency_details,
                'hide_tax' => $hide_tax,
                'sub_units' => $sub_units_array[$purchase_line->id] ?? [],
                'purchase_order_line' => $purchase_line,
                'purchase_order' => $purchase_order
            ])
            @php
                $row_count++;
            @endphp
        @endif
    @endforeach
@else
    @foreach($purchase_order->sell_lines as $purchase_line)
        @if($purchase_line->quantity - $purchase_line->po_quantity_purchased > 0)
            @include('purchase.partials.purchase_entry_row_sells', [
                'variations' => [$purchase_line->variations],
                'product' => $purchase_line->product,
                'row_count' => $row_count,
                'variation_id' => $purchase_line->variation_id,
                'taxes' => $taxes,
                'currency_details' => $currency_details,
                'hide_tax' => $hide_tax,
                'sub_units' => $sub_units_array[$purchase_line->id] ?? [],
                'purchase_order_line' => $purchase_line,
                'purchase_order' => $purchase_order
            ])
            @php
                $row_count++;
            @endphp
        @endif
    @endforeach
@endif
