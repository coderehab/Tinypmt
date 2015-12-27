<li id='item-{{$todo->id}}' class='todo priority-{{$todo->priority}}'>

    <div class="checkbox">
        <input type="checkbox" id='item-checkbox-{{$todo->todoist_id}}' name='item-checkbox-{{$todo->id}}' value='{{$todo->id}}' {{($todo->checked) ? 'checked="' . $todo->checked . '"' : ''}} />
        <label for="item-checkbox-{{$todo->todoist_id}}"><i class="fa fa-check"></i></label>
    </div>

    <p>{{$todo->content}}

    @foreach($todo->labels()->get() as $label)
    @if(isset($label->name))<label>{{$label->name}}</label>@endif
    @endforeach
    </p>
    <span class='avatar' style="background-image:url({{($todo->user) ? $todo->user->gravatar : ''}})"></span>
    <span class='estimated'>Geschatte tijd: <strong>{{$todo->estimated_time}} uur </strong>

        {!! Form::open(['route' => array('save_todo_estimate', $todo->id), 'method' => 'post']) !!}
        {!! Form::text('estimate', $todo->estimated_time, ['class'=>"estimate-input"])!!}
        {!! Form::close() !!}

    </span>
</li>
