<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Label;

class Todo extends Model
{
    protected $table = 'todos';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function project()
    {
			return $this->belongsTo('App\Project', 'project_id', 'todoist_id');
    }

    public function labels() {
        return $this->belongsToMany('App\Label');
    }

}
