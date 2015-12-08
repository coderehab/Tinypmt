<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Auth;
use Redirect;

use App\Project;
use App\Todo;
use App\User;

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
    public function get_settings() {

        if (!Auth::user())
            //echo 'ingelogd';
            return Redirect::route('user_login');

        $view = view::make("settings.main");

        $view->projects = Project::all();
        $view->tasks = Todo::all();

        return $view;
    }
    public function get_team_settings() {

        if (!Auth::user())
            //echo 'ingelogd';
            return Redirect::route('user_login');

        $view = view::make("settings.team");

        $view->projects = Project::all();
        $view->users = User::all();

        return $view;
    }
    public function get_labels_settings() {

        if (!Auth::user())
            //echo 'ingelogd';
            return Redirect::route('user_login');

        $view = view::make("settings.labels");

        $view->projects = Project::all();
        $view->tasks = Todo::all();

        return $view;
    }
}
