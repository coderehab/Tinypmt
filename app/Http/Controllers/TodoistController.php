<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use cURL;
use Illuminate\Http\Request;
use Redirect;
use Hash;
use App\Project;
use App\Todo;
use App\User;
use App\AvailabilityTimeSheet;

use GuzzleHttp\Client;

class TodoistController extends Controller
{

	public function test() {

		return redirect::to('https://todoist.com/oauth/authorize?client_id=de27417420bf4d14881b239ed8506e1d&scope=data:read_write&state=dce0445f47794e51ad85b70090524cb9');
	}

	public function syncdata(Request $request){
		/*$client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://todoist.com',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);

        $response = $client->request('POST', '/oauth/access_token', [
            "form_params" => [
                "client_id"=>"de27417420bf4d14881b239ed8506e1d" ,
                "client_secret"=>"dce0445f47794e51ad85b70090524cb9" ,
                "code"=> $request->input('code')
            ]
        ]);

        $token = json_decode($response->getBody()->getContents())->access_token;
        var_dump($token);*/

		$client = new Client();
		$response = $client->request('POST', 'https://todoist.com/API/v6/sync', [
			"form_params" => [
				"token"=>"008d2b4c885c1cfa2519476394b4df431320971f",
				"seq_no"=>"0" ,
				"resource_types"=> '["all"]'
			]
		]);

		$data = json_decode($response->getBody()->getContents());

		/*echo "<pre>";
        print_r($data->Collaborators);
        echo "<pre>";
        die;
*/
		$projects = array_values(array_sort($data->Projects, function($value){
			$value = (array) $value;
			return $value['item_order'];
		}));

		$todos = $data->Items;
		$user = $data->User;
		$collaborators = $data->Collaborators;

		$this->user = $user;
		$this->collaborators = $collaborators;
		$this->projects = $projects;
		$this->todos = $todos;

		$this->updateUser();
		$this->updateConnectedUsers();
		$this->updateProductList();
		$this->updateTodoList();

		return "done";
	}

	private function updateConnectedUsers(){
		foreach($this->collaborators as $user) {
			//var_dump($user);
			$cr_user = User::where('todoist_id', '=', $user->id)->first();
			if(!$cr_user) {
				$cr_user = new User();
				$cr_user->save();
				$timesheet = new AvailabilityTimeSheet();
				$timesheet->user_id = $cr_user->id;
				$timesheet->is_available = 1;
				$timesheet->date = null;
				$timesheet->starttime = "8:00";
				$timesheet->endtime = "18:00";
				$timesheet->date_string = "every day";
				$timesheet->is_recurring = 1;
				$timesheet->recurring_count = -1;
				$timesheet->recurring_step = 24;
				$timesheet->save();
			}

			$cr_user->todoist_id = $user->id;
			$cr_user->firstname = $user->full_name;
			$cr_user->lastname = "";
			$cr_user->email = $user->email;
			$cr_user->timezone = $user->timezone;
			if(isset($user->is_deleted)) $cr_user->is_deleted = $user->is_deleted;
			$cr_user->password = Hash::make("pass");

			$cr_user->monday_default_available = 8;
			$cr_user->tuesday_default_available = 8;
			$cr_user->wednesday_default_available = 8;
			$cr_user->thursday_default_available = 8;
			$cr_user->friday_default_available = 8;
			$cr_user->saturday_default_available = 0;
			$cr_user->sunday_default_available = 0;

			$cr_user->dates_unavailable = "";

			$cr_user->save();
		}
	}

	private function updateUser(){

		$user = $this->user;
		//var_dump($user);die;
		$cr_user = User::where('todoist_id', '=', $user->id)->first();
		if(!$cr_user) {
			$cr_user = new User();
			$cr_user->save();
			$timesheet = new AvailabilityTimeSheet();
			$timesheet->user_id = $cr_user->id;
			$timesheet->is_available = 1;
			$timesheet->date = null;
			$timesheet->starttime = "8:00";
			$timesheet->endtime = "18:00";
			$timesheet->date_string = "every day";
			$timesheet->is_recurring = 1;
			$timesheet->recurring_count = -1;
			$timesheet->recurring_step = 24;
			$timesheet->save();
		}

		$cr_user->todoist_id = $user->id;
		$cr_user->todoist_token = $user->token;
		$cr_user->firstname = $user->full_name;
		$cr_user->lastname = "";
		$cr_user->email = $user->email;
		$cr_user->password = Hash::make("pass");

		$cr_user->monday_default_available = 8;
		$cr_user->tuesday_default_available = 8;
		$cr_user->wednesday_default_available = 8;
		$cr_user->thursday_default_available = 8;
		$cr_user->friday_default_available = 8;
		$cr_user->saturday_default_available = 0;
		$cr_user->sunday_default_available = 0;

		$cr_user->dates_unavailable = "";

		$cr_user->save();
	}

	private function updateProductList(){
		foreach($this->projects as $project) {
			$cr_project = Project::where('todoist_id', '=', $project->id)->first();
			if(!$cr_project) $cr_project = new Project();
			if(isset($project->inbox_project)) $cr_project->inbox_project = $project->inbox_project;

			$cr_project->todoist_id = $project->id;
			$cr_project->name = $project->name;
			$cr_project->priority = 1;
			$cr_project->user_id = $project->user_id;
			$cr_project->collapsed = $project->collapsed;

			$cr_project->item_order = $project->item_order;
			$cr_project->indent = $project->indent;
			$cr_project->shared = $project->shared;
			$cr_project->is_archived = $project->is_archived;
			$cr_project->archived_date = $project->archived_date;
			$cr_project->archived_timestamp = $project->archived_timestamp;
			$cr_project->save();
		}
	}
	private function updateTodoList(){
		foreach($this->todos as $todo) {
			$cr_todo = Todo::where('todoist_id', '=', $todo->id)->first();
			if(!$cr_todo) $cr_todo = new todo();

			$cr_todo->todoist_id = $todo->id;
			$cr_todo->user_id = $todo->user_id ;
			$cr_todo->content = $todo->content ;
			$cr_todo->due_date = $todo->due_date ;
			$cr_todo->day_order = $todo->day_order ;
			$cr_todo->assigned_by_uid = $todo->assigned_by_uid ;
			$cr_todo->sync_id = $todo->sync_id ;
			$cr_todo->in_history = $todo->in_history ;
			$cr_todo->date_added = $todo->date_added ;
			$cr_todo->checked = $todo->checked ;
			$cr_todo->date_lang = $todo->date_lang ;
			$cr_todo->indent = $todo->indent ;
			$cr_todo->is_deleted = $todo->is_deleted ;
			$cr_todo->priority = $todo->priority ;
			$cr_todo->responsible_uid = $todo->responsible_uid ;
			$cr_todo->project_id = $todo->project_id ;
			$cr_todo->collapsed = $todo->collapsed ;
			$cr_todo->date_string = $todo->date_string ;
			$cr_todo->is_archived = $todo->is_archived ;
			$cr_todo->item_order = $todo->item_order ;
			$cr_todo->due_date_utc = $todo->due_date_utc ;


			$cr_todo->date_checked = "" ;
			$cr_todo->estimated_time = rand(0,80)/10;

			$cr_todo->save();
		}
	}
}
