@extends('template.default')

@section('page-content')

<h2>Team settings</h2>
<table width="100%">
    @foreach($users as $user)

    <tr>
        <td width="10%"><img src="{{$user->gravatar}}" width="42"/></td>
        <td width="60%">{{$user->fullname}}</td>
        <td width="15%"><a href="{{ route('edit_user', ['id' => $user->id]) }}" >Bewerken</a></td>
        <td width="15%"><a href="{{ route('remove_user', ['id' => $user->id]) }}" >Verwijderen</a></td>
    </tr>

    @endforeach
</table>
@stop
