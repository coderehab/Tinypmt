<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

use Input;
use Request;
use Redirect;

use App\Todo;
use App\Label;
use App\User;

use Auth;

class LabelController extends Controller
{
    public function get_index() {
        $view = view::make("labels.index");

        $view->labels = Label::all();

        return $view;
    }

    public function post_label(Request $request) {

        $label = new Label();
        $label->name = Input::get('name');
        $label->save();

        return Redirect::back();
    }

    public function remove_label($id) {
        if(Auth::user() && $id) Label::find($id)->delete();
        return Redirect::back();
    }

    public function add_label_to_user($user_id) {
        $user = User::find($user_id);
        $user->labels()->attach(Input::get('label_id'));
        return Redirect::back();
    }

    public function remove_label_from_user($user_id, $label_id) {
        $user = User::find($user_id);
        $user->labels()->detach($label_id);
        return Redirect::back();
    }
}
