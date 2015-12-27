@extends('template.default')

@section('page-content')

<h2>Label</h2>
<table width="100%" class="list">
    <tr>
        <th align="left">label name</th>
        <th align="left">open tasks</th>
        <th align="left">closed tasks</th>
        <th></th>
        <th></th>
    </tr>

    @foreach ($labels as $label)

    <tr>
        <td>{{$label->name}}</td>
        <td>0</td>
        <td>0</td>
        <td width="30" align="right">edit</td>
        <td width="50" align="right">{!! link_to_route('remove_label', "Remove", ["id" => $label->id]) !!}</td>
    </tr>

    @endforeach
</table>

{!! Form::open(['route' => 'save_label'])!!}
{!! Form::text('name', '', ["placeholder" => "Label naam"])!!}
{!! Form::submit('Opslaan')!!}
{!! Form::close() !!}

@stop
