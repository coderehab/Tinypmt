<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Auth;
use Redirect;

use App\Project;
use App\Todo;

class AppController extends Controller
{
    public function get_index() {

        if (!Auth::user())
            //echo 'ingelogd';
            return Redirect::route('user_login');

        $view = view::make("homepage");

        $view->projects = Project::all();
        $view->tasks = Todo::all();

        return $view;
    }
}
