<?php  namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use View;
use Input;
use Hash;
use Validator;
use Redirect;
use App\User;
use App\Label;

class UserController extends Controller {

	protected $restfull = true;

	public function get_login(){
		if (Auth::user())
			//echo 'ingelogd';
			return Redirect::route('homepage');

		$view = View::make('login');
		$view->title = 'Login';
		return $view;
	}

	public function post_login(){
		$input = Input::all();
		$rules = array(
			'email' => 'required',
			'password' => 'required'
		);

		$validator = Validator::make($input, $rules);

		if($validator->fails()) {
			return Redirect::route('user_login')->withErrors($validator);
		}

		else {
			$credentials = array(
				'email' => $input['email'],
				'password' => $input['password']
			);

			if (Auth::attempt($credentials))
				return Redirect::route('homepage');
			else
				return Redirect::route('user_login')->withErrors($validator);
		}
	}

	public function get_register(){
		$view = View::make('register');
		$view->title = 'Registreren';

		return $view;
	}

	public function admin_create_user(){
		$view = View::make('register');
		$view->title = 'Registreren';

		return $view;
	}

	public function post_register(){
		$input = Input::all();
		$rules = array(
			'firstname' => 'required',
			'lastname' => 'required',
			'email' => 'required|unique:users|email',
			'password' => 'required|min:3|confirmed',
			'password_confirmation' => 'required|min:3',
			'role' => '1'
		);

		$validator = Validator::make($input, $rules);

		if($validator->passes()){

			$password = $input['password'];
			$password = Hash::make($password);

			$user = new User();
			$user->firstname = $input['firstname'];
			$user->lastname = $input['lastname'];
			$user->email = $input['email'];
			$user->password = $password;
			$user->save();

			return Redirect::route('homepage');
		}else {
			return Redirect::route('register')->withInput()->withErrors($validator);
		}
	}

	public function get_logout(){
		Auth::logout();
		return Redirect::route('homepage');
		//return Redirect::back();
	}

	public function edit_user($id){
		$view = View::make('user.edit');
		$view->title = 'Instellingen';
		$view->user = User::find($id);
        $view->available_labels = Label::all()->lists("name", "id");

		return $view;
	}

	public function put_user($id){
		$input = Input::all();

		$password = $input['password'];
		$rules = array(
			'firstname' => 'required',
			'email' => 'required',
			'google_calendar_id' => 'required'
		);

		if ($input['email'] != User::find($id)->email)
			$rules['email'] = 'required|unique:users|email';

		$validator = Validator::make($input, $rules);

		if($validator->passes()){

			if(!isset($input['is_team'])) $input['is_team'] = 0;

			$user = User::find($id);
			$user->firstname = $input['firstname'];
			$user->lastname = $input['lastname'];
			$user->email = $input['email'];
			$user->is_team = $input['is_team'];
			$user->google_calendar_id = $input['google_calendar_id'];

			if ($password && Hash::make($password) != $user->password)
				$user->password = Hash::make($password);

			$user->save();

			return Redirect::route('edit_user', $id);
		}else {
			return Redirect::route('edit_user', $id)->withInput()->withErrors($validator);
		}
	}

	public function remove_user($id) {
		User::find($id)->delete();
		return Redirect::back();
	}


}

?>
