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

<h3>Tijdschema's</h3>
<table width="100%">
	@foreach($user->availability->get() as $timesheet)
	<tr>
		<td>{{$timesheet->date_string}}</td><td>:</td>
		<td>
			{!! Form::text('timesheet_' . $timesheet->id . '_start', $timesheet->starttime, array('placeholder' => 'Tijdstip', 'class'=>'form-control'))!!}
		</td>
		<td>
			{!! Form::text('timesheet_' . $timesheet->id . '_end', $timesheet->endtime, array('placeholder' => 'Tijdstip', 'class'=>'form-control'))!!}
		</td>
	</tr>
	@endforeach
</table>
<div class='row'>
	<div class="cols-6" style="height:20px;"> </div>
	{!! Form::submit('Bijwerken', array('class' =>'cols-6 btn btn-success'))!!}
</div>
{!! Form::close() !!}

@stop
