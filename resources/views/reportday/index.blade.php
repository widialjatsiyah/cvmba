@extends('layouts.app')
@section('title', 'Day Report ')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Day Report 
        <small>Manage Your Day Report </small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => "All Your Day Report"])
        @can('brand.create')
            @slot('tool')
                <div class="box-tools">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
        @can('brand.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="reportday_table">
                    <thead>
                        <tr>
                            <th>Modal</th>
                            <th>Stok</th>
                            <th>Cash</th>
                            <th>Profit</th>
                            <th>Created At</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade reportday_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">

                    {!! Form::open(['url' => action([\App\Http\Controllers\ReportdayController::class, 'store']), 'method' => 'post' , 'id' => 'reportday_add_form' ]) !!}

                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Day Report</h4>
                    </div>

                    <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4"> 
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Date <span class="required">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="date" name="created_at" class="form-control tanggal"  required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4"> 
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Modal <span class="required">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="modal" class="form-control"  required/>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="row" style="margin-top:20px">
                                
                                <div class="col-md-4">                           
                                    <div class="form-group ">
                                        <label class="col-sm-3 control-label">Stok <span class="required">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="number" name="stok" class="form-control"  required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">                           
                                    <div class="form-group ">
                                        <label class="col-sm-3 control-label">Cash <span class="required">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="number" name="cash" class="form-control"  required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4"> 
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Untung <span class="required">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" name="untung" class="form-control"  required/>
                                        </div>
                                    </div>
                                </div>

                            </div> 
                            <hr>
                            <div class="row ">
                                <div class="col-sm-12">
                                <a href="#" class="btn btn-success" style="margin-left:10px" id="addItem"><i class="fa fa-plus"></i> Add Branch</a></td>
                                </div> 
                            </div>
                            <div class="row mt-5">
                                <div class="col-sm-12">
                                    <table class="table table-hover table-bordered" id="tableItem">
                                    <thead>
                                        <th>Cabang</th>
                                        <th>Pendapatan</th>
                                        <th>Setor</th>
                                        <th>Laba Kotor</th>
                                        <th>Laba Bersih</th>
                                        <th>Ongkos Mekanik</th>
                                        <th>Ongkos Bubut</th>
                                        <th>#</th>
                                    </thead>
                                    <tbody >

                                    </tbody>
                                    <table>
                                </div>
                            </div>
            

                    </div>

                    <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
                    </div>

                    {!! Form::close() !!}

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
    </div>



</section>
<!-- /.content -->

@endsection
