@extends('layouts.app_mobile')
@section('title', __('lang_v1.my_profile'))

@section('content')

<!-- Content Header (Page header) -->

<div class="card bg-primary">
    <div class="card-body">
        <span style="font-weight: bolder;"> @lang('lang_v1.my_profile') </span>
    </div>
</div>

<!-- Main content -->
<section class="content">

    <div class="accordion accordion-flush" id="accordionFlushExample" style="margin-bottom: 100px;">
        <div class="accordion-item">
            <h2 class="accordion-header" id="flush-headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                    Profile
                </button>
            </h2>
            <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">


                    {!! Form::open(['url' => action([\App\Http\Controllers\UserController::class, 'updateProfileMobile']), 'method' => 'post', 'id' => 'edit_user_profile_form', 'files' => true ]) !!}
                    <div class="row">
                        <div class="col-md-12">
                            
                            @if(!empty($user->media))
                            <div class="col-md-12 text-center">
                                {!! $user->media->thumbnail([150, 150], 'img-circle') !!}
                            </div>
                            @endif
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('profile_photo', __('lang_v1.upload_image') . ':') !!}
                                    {!! Form::file('profile_photo', ['id' => 'profile_photo', 'accept' => 'image/*']); !!}
                                    <small>
                                        <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])</p>
                                    </small>
                                </div>
                            </div>
                           
                        </div>
                        <div class="col-md-12">
                            <div class="card card-solid"> <!--business info card start-->
                              
                                    <div class="form-group col-md-2">
                                        {!! Form::label('surname', __('business.prefix') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('surname', $user->surname, ['class' => 'form-control','placeholder' => __('business.prefix_placeholder')]); !!}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-5">
                                        {!! Form::label('first_name', __('business.first_name') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('first_name', $user->first_name, ['class' => 'form-control','placeholder' => __('business.first_name'), 'required']); !!}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-5">
                                        {!! Form::label('last_name', __('business.last_name') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::text('last_name', $user->last_name, ['class' => 'form-control','placeholder' => __('business.last_name')]); !!}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('email', __('business.email') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::email('email', $user->email, ['class' => 'form-control','placeholder' => __('business.email') ]); !!}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('language', __('business.language') . ':') !!}
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-info"></i>
                                            </span>
                                            {!! Form::select('language',$languages, $user->language, ['class' => 'form-control select2']); !!}
                                        </div>
                                    
                            </div>
                        </div>

                    </div>
                        <div class="col-md-12 text-center mt-5">
                            <button type="submit" class="btn btn-success btn-block btn-lg">@lang('messages.update')</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="flush-headingThree">
                <button class="accordion-button collapsed"  style="border-top:1px solid #337ab7;" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                    Ubah Password
                </button>
            </h2>
            <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
                    {!! Form::open(['url' => action([\App\Http\Controllers\UserController::class, 'updatePasswordMobile']), 'method' => 'post', 'id' => 'edit_password_form',
                    'class' => 'form-horizontal' ]) !!}
                   
                            <div class="form-group">
                                {!! Form::label('current_password', __('user.current_password') . ':', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-lock"></i>
                                        </span>
                                        {!! Form::password('current_password', ['class' => 'form-control','placeholder' => __('user.current_password'), 'required']); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('new_password', __('user.new_password') . ':', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-lock"></i>
                                        </span>
                                        {!! Form::password('new_password', ['class' => 'form-control','placeholder' => __('user.new_password'), 'required']); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('confirm_password', __('user.confirm_new_password') . ':', ['class' => 'col-md-12 control-label']) !!}
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-lock"></i>
                                        </span>
                                        {!! Form::password('confirm_password', ['class' => 'form-control','placeholder' => __('user.confirm_new_password'), 'required']); !!}
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn rounded-pill btn-primary btn-block">@lang('messages.update')</button>
                        

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
@endsection