<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

use Request;

use App\Project;
use App\Todo;

class ProjectController extends Controller
{
    public function get_index() {

    }

    public function get_single(Request $request, $id) {
        var_dump($id);

        $view = view::make("project");

        $view->projects = Project::all();
        $view->active_project = Project::find($id);

        return $view;
    }

    public function put_update() {

    }
}
