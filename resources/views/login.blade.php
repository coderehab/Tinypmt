@extends('template.default')

@section('page-content')

<div class="cols-4">
    <h2>Inloggen</h2>
    <p>Gebruik onderstaand formulier om in te loggen bij DeltaXs. Indien u nog geen account heeft kunt u zich {!! link_to_route('user_registration', 'hier registreren', null, array('class'=> '')) !!}</p>

    {!! Form::open(array('url' => '/login','method' => 'post')) !!}
    @if ($errors->any())
    <div id='login-errors' class='errors'>
        <a href='#' class='close' data-dismiss='alert'>&times;</a>
        {!! implode('', $errors->all("<li class='error'>:message</li>")) !!}
    </div>
    @endif
    {!! Form::text('email', '', array('placeholder' => 'E-mailadres', 'class'=>'form-control'))!!}
    {!! Form::password('password', array('placeholder' => 'Password', 'class'=>'form-control'))!!}
    {!! Form::submit('Login', array('class' =>'btn btn-primary btn-block'))!!}

    <span style="margin-left:10px;">{!! link_to_route('user_registration', ' of Registreren', null, array('class'=> '')) !!}</span>
    {!! Form::close() !!}

</div>
<div class="cols-8">&nbsp;</div>

@stop

