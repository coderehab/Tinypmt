@if(Auth::user())

<div class='user-info'>

    <div class='picture'>
        <img src="{{ Auth::user()->gravatar }}" width="90" height="90"/>
    </div>
    <h2>{{Auth::user()->fullname}}</h2>
    <h3>Task killer</h3>

</div>

<h3>Inbox</h3>
<h3>Conflicten</h3>
<br />
<h3>Projecten</h3>

<ul>

    <?php $indent = 1; ?>
    @foreach($projects as $project)

    @if($project->indent>$indent) <ul> @endif
    @if($project->indent<$indent) </ul> @endif

    <li><a href="/project/{{ $project->id }}"><i class="fa fa-tasks"></i>{{ $project->name }}</a></li>

    <?php $indent = $project->indent; ?>
    @endforeach

</ul>

<h3>Instellingen</h3>
@endif
