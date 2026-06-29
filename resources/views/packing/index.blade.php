@extends('layouts.app')
@section('title', 'Packing List')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Packing List 
        <small>Manage Your Packing Operations</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => "All Packing Records"])
        @can('packing.create')
            @slot('tool')
                <div class="box-tools">
                <button type="button" class="btn btn-primary" data-href="{{action([\App\Http\Controllers\PackingController::class, 'create'])}}" data-container=".packing_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
        @can('packing.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="packing_table">
                    <thead>
                        <tr>
                            <th>Packing Name</th>
                            <th>Packing Date</th>
                            <th>Created At</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade packing_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript') 
<script type="text/javascript">
    
    //packing table
    var packing_table = $('#packing_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'packing',
        },
        columns: [
            { data: 'packing_name', name: 'packing_name' },
            { data: 'packing_date', name: 'packing_date' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
    $(document).on('click', 'button.show_packing_button', function() {
        $('div.packing_modal').load($(this).data('href'), function() {
            $(this).modal('show');
        })
    });

    $(document).on('click', 'button.edit_packing_button', function() {
        $('div.packing_modal').load($(this).data('href'), function() {
            $(this).modal('show');

            $('form#packing_edit_form').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var data = form.serialize();

                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    beforeSend: function(xhr) {
                        __disable_submit_button(form.find('button[type="submit"]'));
                    },
                    success: function(result) {
                        if (result.success == true) {
                            $('div.packing_modal').modal('hide');
                            toastr.success(result.msg);
                            packing_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });
    });

    $(document).on('click', 'button.delete_packing_button', function() {
        swal({
            title: LANG.sure,
            text: "Are you sure delete Packing Record?",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'GET',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            packing_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

</script>
@endsection
