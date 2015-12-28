@extends('template.default')

@section('page-content')

<h2>{{$active_project->name}}</h2>
<h3>Projectinformatie</h3>

<ul>
    <li>Startdatum: {{date('Y-m-d')}}</li>
    <li>Einddatum: {{$active_project->due_date}}</li>
    <li>Benodigde uren: {{$active_project->estimated_time}}</li>
</ul>

<br />

<h4>Afspraken</h4>
<ul class="tasklist">
    @foreach($active_project->todos as $todo) @if($todo->checked == 0)

    @if($todo->estimated_time != 0 && date('G i s', strtotime($todo->due_date)) != "0 00 00")
    {{ date('G i s',strtotime($todo->due_date))}}
    @include('partials.todo')
    @endif

    @endif @endforeach
</ul>

<h4>Openstaande taken</h4>
<ul class="tasklist">
    <?php $tmpdate = ""?>
    @foreach($active_project->todos as $todo) @if($todo->checked == 0)
    @if($todo->estimated_time != 0 && date('G i s', strtotime($todo->due_date)) == "0 00 00")

    @unless ($tmpdate == date('l j F - Y',strtotime($todo->due_date)) )
    <?php $tmpdate = date('l j F - Y',strtotime($todo->due_date)) ?>
    <h5>{{date('l j F - Y',strtotime($todo->due_date))}}</h5>
    @endunless

    @include('partials.todo')
    @endif

    @endif @endforeach
</ul>

<h4>Niet ingeplande taken</h4>
<ul class="tasklist">
    @foreach($active_project->todos as $todo)

	@if($todo->estimated_time == 0 && !$todo->checked  && substr($todo->content, 0, 1) != "*")
    @include('partials.todo')
    @endif

    @endforeach
</ul>

<h4>Afgeronde taken</h4>
<ul class="tasklist">
    @foreach($active_project->todos as $todo) @if($todo->checked == 1)
    @include('partials.todo')
    @endif @endforeach
</ul>

@stop
