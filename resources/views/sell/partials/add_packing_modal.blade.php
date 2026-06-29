<div class="modal fade" id="add_packing_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {!! Form::open([
                'url' => action([\App\Http\Controllers\PackingController::class, 'addBulkPacking']),
                'method' => 'post',
                'id' => 'add_packing_form',
            ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">packing Information</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::hidden('transactions', null, ['id' => 'sell_to_packing']) !!}
                    <label for="Nama packing">Nama Packing</label>
                    <input type="text" class="form-control" id="packing_name" name="packing_name"
                        placeholder="Nama packing">
                </div>
                <div class="form-group">
                    <label for="packing_date">Tanggal Packing</label>
                    <input type="date" class="form-control" id="packing_date" name="packing_date"
                        placeholder="Tanggal Packing">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="submit_packing">@lang('messages.save')</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
