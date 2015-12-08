@extends('template.default')

@section('page-content')

<h2>Registreren</h2>


{!! Form::open(array('update_user', $user->id)) !!}

@if ($errors->any())
<div id='login-errors' class='errors'>
    <a href='#' class='close' data-dismiss='alert'>&times;</a>
    {{ implode('', $errors->all("<li class='error'>:message</li>")) }}
</div>
@endif

<h3>Basisgegevens</h3>
<div class='row'>
    {!! Form::text('firstname', $user->firstname, array('placeholder' => 'Voornaam', 'class'=>'cols-6 form-control'))!!}
    {!! Form::text('lastname', $user->lastname, array('placeholder' => 'Achternaam', 'class'=>'cols-6 form-control'))!!}
</div>
{!! Form::text('email', $user->email, array('placeholder' => 'Email', 'class'=>'form-control'))!!}
<div class='row'>
    {!! Form::password('password', array('placeholder' => 'Wachtwoord', 'class'=>'cols-6 form-control'))!!}
    {!! Form::password('password_confirmation', array('placeholder' => 'Wachtwoord herhalen', 'class'=>'cols-6 form-control'))!!}
</div>
<h3>Beschikbare uren</h3>
<table width="100%">

    <tr>
        <td>Maandag</td><td>:</td>
        <td>
            {!! Form::text('monday_default_available', $user->monday_default_available, array('placeholder' => 'Aantal uur', 'class'=>'form-control'))!!}
        </td>
    </tr>
    <tr>
        <td>Dinsdag</td><td>:</td>
        <td>
            {!! Form::text('tuesday_default_available', $user->tuesday_default_available, array('placeholder' => 'Aantal uur', 'class'=>'form-control'))!!}
        </td>
    </tr>
    <tr>
        <td>Woensdag</td><td>:</td>
        <td>
            {!! Form::text('wednesday_default_available', $user->wednesday_default_available, array('placeholder' => 'Aantal uur', 'class'=>'form-control'))!!}
        </td>
    </tr>
    <tr>
        <td>Donderdag</td><td>:</td>
        <td>
            {!! Form::text('thursday_default_available', $user->thursday_default_available, array('placeholder' => 'Aantal uur', 'class'=>'form-control'))!!}
        </td>
    </tr>
    <tr>
        <td>Vrijdag</td><td>:</td>
        <td>
            {!! Form::text('friday_default_available', $user->friday_default_available, array('placeholder' => 'Aantal uur', 'class'=>'form-control'))!!}
        </td>
    </tr>
    <tr>
        <td>Zaterdag</td><td>:</td>
        <td>
            {!! Form::text('saturday_default_available', $user->saturday_default_available, array('placeholder' => 'Aantal uur', 'class'=>'form-control'))!!}
        </td>
    </tr>
    <tr>
        <td>Zondag</td><td>:</td>
        <td>
            {!! Form::text('sunday_default_available', $user->sunday_default_available, array('placeholder' => 'Aantal uur', 'class'=>'form-control'))!!}
        </td>
    </tr>


</table>
<div class='row'>
    <div class="cols-6" style="height:20px;"> </div>
    {!! Form::submit('Bijwerken', array('class' =>'cols-6 btn btn-success'))!!}
</div>
{!! Form::close() !!}

@stop
