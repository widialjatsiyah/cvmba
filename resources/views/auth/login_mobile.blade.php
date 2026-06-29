@extends('layouts.auth2')
@section('title', __('lang_v1.login'))

@section('content')
<!-- <div class="row justify-content-center">
<div class=" col-md-4 col-xs-12"> -->
    
<div style=" display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px;">
    <div class="card  p-5" style="min-width: 350px; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19); ">
        <div class="card-body">
        <div class="text-center" style="margin-bottom: -30px;">
        <img src="{{asset('uploads/business_logos/1730917651_logo.jpg')}}" class="img-rounded" alt="Logo" width="150">
        </div>
        <p class="form-header text-danger">@lang('lang_v1.login')</p>
        <form method="POST" action="{{ route('auth_mobile') }}" id="login-form">
            {{ csrf_field() }}
            <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                @php
                    $username = old('username');
                    $password = null;
                   
                @endphp
                <input id="username" type="text" class="form-control" name="username" value="{{ $username }}" required autofocus placeholder="@lang('lang_v1.username')">
                <span class="fa fa-user form-control-feedback"></span>
                @if ($errors->has('username'))
                    <span class="help-block">
                        <strong>{{ $errors->first('username') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                <input id="password" type="password" class="form-control" name="password"
                value="{{ $password }}" required placeholder="@lang('lang_v1.password')">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
            <br>
            <div class="form-group">
                <button type="submit" class="btn btn-danger rounded-pill btn-block  btn-login">@lang('lang_v1.login')</button>
               
            </div>
        </form>
        </div>
    </div>
</div> 
    <!-- </div>
</div> -->
    
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        $('#change_lang').change( function(){
            window.location = "{{ route('login') }}?lang=" + $(this).val();
        });

        $('a.demo-login').click( function (e) {
           e.preventDefault();
           $('#username').val($(this).data('admin'));
           $('#password').val("{{$password}}");
           $('form#login-form').submit();
        });
    })
</script>
@endsection
