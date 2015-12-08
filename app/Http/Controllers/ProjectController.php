<?php

namespace App\Http\Controllers;
use App\Http\Controllers\PlanningController;
use Illuminate\Support\Facades\View;

use Request;

use App\Project;
use App\Todo;

class ProjectController extends PlanningController
{
    public function get_index() {

    }

    public function get_single(Request $request, $id) {
        $view = view::make("project");

        $view->projects = Project::all();
        $view->active_project = Project::find($id);

        $this->updatePlanning($view->active_project);

        return $view;
    }

    public function put_update() {

    }
}
