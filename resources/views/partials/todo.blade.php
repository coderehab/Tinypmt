<li id='item-{{$todo->id}}' class='todo priority-{{$todo->priority}}'>

    <div class="checkbox">
        <input type="checkbox" id='item-checkbox-{{$todo->todoist_id}}' name='item-checkbox-{{$todo->id}}' value='{{$todo->id}}' {{($todo->checked) ? 'checked="' . $todo->checked . '"' : ''}} />
        <label for="item-checkbox-{{$todo->todoist_id}}"><i class="fa fa-check"></i></label>
    </div>
    <p>{{$todo->content}}</p>

    <span class='avatar' style="background-image:url({{($todo->user) ? $todo->user->gravatar : ''}})">
    </span>
    <span class='estimated'>Geschatte tijd: <strong>{{$todo->estimated_time}} uur</strong></span>

</li>
