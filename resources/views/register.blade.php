@extends('template.default')

@section('page-content')

<h2>Registreren</h2>


{!! Form::open(array('route'=>'user_registration')) !!}

@if ($errors->any())
<div id='login-errors' class='errors'>
    <a href='#' class='close' data-dismiss='alert'>&times;</a>
    {{ implode('', $errors->all("<li class='error'>:message</li>")) }}
</div>
@endif
{!! Form::text('firstname', '', array('placeholder' => 'Voornaam', 'class'=>'form-control'))!!}
{!! Form::text('lastname', '', array('placeholder' => 'Achternaam', 'class'=>'form-control'))!!}
{!! Form::text('email', '', array('placeholder' => 'Email', 'class'=>'form-control'))!!}
{!! Form::password('password', array('placeholder' => 'Wachtwoord', 'class'=>'form-control'))!!}
{!! Form::password('password_confirmation', array('placeholder' => 'Wachtwoord herhalen', 'class'=>'form-control'))!!}

{!! Form::submit('Registreren', array('class' =>'btn btn-success'))!!}
{!! Form::close() !!}
@stop
