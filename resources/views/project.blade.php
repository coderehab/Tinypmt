@extends('template.default')

@section('page-content')

<h2>{{$active_project->name}}</h2>



<form>
    <h4>Afspraken</h4>
    <ul class="tasklist">
        @foreach($active_project->todos as $todo)
        @if($todo->date_string != '' && date ('G i s', strtotime($todo->due_date)) != "22 59 59")

        {{ date('G i s',strtotime($todo->due_date))}}
        @include('partials.todo')
        @endif
        @endforeach
    </ul>

    <h4>Openstaande taken</h4>
    <ul class="tasklist">
        <?php $tmpdate = ""?>
        @foreach($active_project->todos as $todo)
        @if($todo->date_string != '' && date ('G i s', strtotime($todo->due_date)) == "22 59 59")
        @unless ($tmpdate == date('l j F',strtotime($todo->due_date)) )
        <?php $tmpdate = date('l j F',strtotime($todo->due_date)) ?>

        <h5>{{date('l j F',strtotime($todo->due_date))}}</h5>
        @endunless


        @include('partials.todo')
        @endif
        @endforeach
    </ul>

    <h4>Niet ingeplande taken</h4>
    <ul class="tasklist">

        @foreach($active_project->todos as $todo)
        @if($todo->date_string == '' && !$todo->checked)
        @include('partials.todo')
        @endif
        @endforeach
    </ul>

</form>

@stop
