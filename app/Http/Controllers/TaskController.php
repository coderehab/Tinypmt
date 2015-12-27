<?php

namespace App\Http\Controllers;
use App\Http\Controllers\PlanningController;
use Illuminate\Support\Facades\View;

use Input;
use Request;
use Redirect;

use App\Project;
use App\Todo;


class TaskController extends PlanningController
{
    public function index() {
    }

    public function update_estimate(Request $request, $id){

        $todo = Todo::find($id);
        $todo->estimated_time = Input::get('estimate');
        $todo->update();

        return Redirect::back();
    }
}
