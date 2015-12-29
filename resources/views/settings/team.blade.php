@extends('template.default')

@section('page-content')

<h2>Het Team</h2>
<table width="100%">
    @foreach($users as $user)
		@if ($user->is_team)
    <tr>
        <td width="10%"><img src="{{$user->gravatar}}" width="42"/></td>
        <td width="60%">{{$user->fullname}}</td>
        <td width="15%"><a href="{{ route('edit_user', ['id' => $user->id]) }}" >Bewerken</a></td>
        <td width="15%"><a href="{{ route('remove_user', ['id' => $user->id]) }}" >Verwijderen</a></td>
    </tr>
		@endif
    @endforeach
</table>

<h2>Collaborators</h2>
<table width="100%">
	@foreach($users as $user)
	@if (!$user->is_team)
	<tr>
		<td width="10%"><img src="{{$user->gravatar}}" width="42"/></td>
		<td width="60%">{{$user->fullname}}</td>
		<td width="15%"><a href="{{ route('edit_user', ['id' => $user->id]) }}" >Bewerken</a></td>
		<td width="15%"><a href="{{ route('remove_user', ['id' => $user->id]) }}" >Verwijderen</a></td>
	</tr>
	@endif
	@endforeach
</table>
@stop
