@extends('template.default')

@section('page-content')

<h2>{{ $user->firstname}}</h2>


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
{!! Form::submit('Bijwerken', array('class' =>'cols-6 btn btn-success'))!!}
{!! Form::close() !!}


<h3>Labels</h3>


<hr>
<div id="user_labels" class="row">
    @foreach($user->labels()->get() as $label)
    @if(isset($label->name))
    <label>
        {{$label->name}}
        <span class='remove'>{!! link_to_route('remove_label_from_user', 'x', ["user_id" => $user->id, "label_id" => $label->id]) !!}</span>
    </label>
    @endif
    @endforeach
</div>
<h5>Add label</h5>
{!! Form::open(array('route' => array('add_label_to_user', $user->id))) !!}
{!! Form::select('label_id', $available_labels, null, ['class' => 'cols-4', 'placeholder' => 'Selecteer label']) !!}
{!! Form::submit('Toevoegen', array('class' =>'cols-3 btn btn-success'))!!}
{!! Form::close() !!}


{!! Form::open(array('update_user', $user->id)) !!}
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
{!! Form::close() !!}


@stop
