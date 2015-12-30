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
use App\Label;
use App\AvailabilityTimeSheet;
use Input;
use DB;

use GuzzleHttp\Client;

class TodoistController extends Controller
{

	public function test() {

		return redirect::to('https://todoist.com/oauth/authorize?client_id=de27417420bf4d14881b239ed8506e1d&scope=data:read_write&state=dce0445f47794e51ad85b70090524cb9');
	}

	public function receive_event(Request $request){

		var_dump($request->json('event_name'));
		$event = $request->json('event_name');
		$data = [(object) $request->json('event_data')];

		switch ($event) {
			case "item:added" || "item:updated" || "item:completed" || "item:uncompleted" || "item:deleted":
				$this->todos = $data;
				$this->updateTodoList();
				break;
			case "project:added" || "project:updated" || "project:deleted" || "project:archived" || "project:unarchived":
				$this->projects = $data;
				$this->updateProjectList();
				break;
			case "label:added" || "label:deleted" || "label:updated":
				$this->labels = $data;
				$this->updateLabels();
				break;
		}
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
				//"token"=>"dbadf3381fc34496c555e87111cd0b4d7d9eecee",
				"token"=>"31ecf41c4338d45dd4c6ad65f706207366691925",
				"seq_no"=> uniqid() ,
				"resource_types"=> '["all"]'
			]
		]);

		$data = json_decode($response->getBody()->getContents());

		$projects = array_values(array_sort($data->Projects, function($value){
			$value = (array) $value;
			return $value['item_order'];
		}));

		DB::table('project_user')->delete();
		DB::table('label_todo')->delete();

		$todos = $data->Items;
		$labels = $data->Labels;
		$user = $data->User;
		$collaborators = $data->Collaborators;
		$collaboratorStates = $data->CollaboratorStates;

		//var_dump($todos);

		$this->user = $user;
		$this->collaborators = $collaborators;
		$this->projects = $projects;
		$this->collaboratorStates = $collaboratorStates;

		$this->todos = $todos;
		$this->labels = $labels;

		$this->updateLabels();
		$this->updateUser();
		$this->updateConnectedUsers();
		$this->updateProjectList();
		$this->updateCollaboratorStates();
		$this->updateTodoList();

		return Redirect::back();
	}

	private function updateConnectedUsers(){
		foreach($this->collaborators as $user) {
			$cr_user = User::where('todoist_id', '=', $user->id)->first();
			if(isset($user->is_deleted) && !$user->is_deleted || !isset($user->is_deleted)){
				if(!$cr_user) {
					$cr_user = new User();
				}

				$cr_user->todoist_id = $user->id;
				$cr_user->firstname = $user->full_name;
				$cr_user->lastname = "";
				$cr_user->email = $user->email;
				$cr_user->timezone = $user->timezone;
				if(isset($user->is_deleted)) $cr_user->is_deleted = $user->is_deleted;
				$cr_user->password = Hash::make("pass");

				$cr_user->save();
			}else{
				if ($cr_user) $cr_user->delete();
			}
		}
	}

	private function updateCollaboratorStates(){


		foreach($this->collaboratorStates as $connection) {
			if($connection->state == 'active'){
				$project = Project::where('todoist_id', $connection->project_id)->first();
				$user = User::where('todoist_id', $connection->user_id)->first();

				if($project) $project->users()->attach($user->id);
			}
		}

	}

	private function updateUser(){

		$user = $this->user;
		//var_dump($user);die;
		$cr_user = User::where('todoist_id', '=', $user->id)->first();
		if(!$cr_user) {
			$cr_user = new User();
		}

		$cr_user->todoist_id = $user->id;
		$cr_user->todoist_token = $user->token;
		$cr_user->firstname = $user->full_name;
		$cr_user->lastname = "";
		$cr_user->email = $user->email;
		$cr_user->password = Hash::make("pass");

		$cr_user->save();
	}

	private function updateProjectList(){
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

	private function updateLabels(){
		foreach($this->labels as $label) {
			$cr_label = Label::where('todoist_id', $label->id)->first();
			if(!$cr_label) $cr_label = new Label();

			$cr_label->todoist_id = $label->id;
			$cr_label->uid = $label->uid;
			$cr_label->name = $label->name;
			$cr_label->color = $label->color;
			$cr_label->is_deleted = $label->is_deleted;

			$cr_label->save();
		}
	}

	private function updateTodoList(){
		foreach($this->todos as $todo) {
			$cr_todo = Todo::where('todoist_id', $todo->id)->first();

			if(!$cr_todo) {
				$cr_todo = new todo();
			}

			if (isset($todo->id)) $cr_todo->todoist_id = $todo->id;
			if (isset($todo->user_id)) $cr_todo->user_id = $todo->user_id ;
			if (isset($todo->content)) $cr_todo->content = $todo->content ;
			if (isset($todo->due_date)) $cr_todo->due_date = $todo->due_date ;
			if (isset($todo->day_order)) $cr_todo->day_order = $todo->day_order ;
			if (isset($todo->assigned_by_uid)) $cr_todo->assigned_by_uid = $todo->assigned_by_uid ;
			if (isset($todo->sync_id)) $cr_todo->sync_id = $todo->sync_id ;
			if (isset($todo->in_history)) $cr_todo->in_history = $todo->in_history ;
			if (isset($todo->date_added)) $cr_todo->date_added = $todo->date_added ;
			if (isset($todo->checked)) $cr_todo->checked = $todo->checked ;
			if (isset($todo->date_lang)) $cr_todo->date_lang = $todo->date_lang ;
			if (isset($todo->indent)) $cr_todo->indent = $todo->indent ;
			if (isset($todo->is_deleted)) $cr_todo->is_deleted = $todo->is_deleted ;
			if (isset($todo->priority)) $cr_todo->priority = $todo->priority ;;

			if (isset($todo->responsible_uid)) $cr_todo->responsible_uid = $todo->responsible_uid ;
			if (isset($todo->project_id)) $cr_todo->project_id = $todo->project_id ;
			if (isset($todo->collapsed)) $cr_todo->collapsed = $todo->collapsed ;
			//$cr_todo->date_string = $todo->date_string ;
			if (isset($todo->is_archived)) $cr_todo->is_archived = $todo->is_archived ;
			if (isset($todo->item_order)) $cr_todo->item_order = $todo->item_order ;
			//$cr_todo->due_date_utc = $todo->due_date_utc ;
			if (isset($todo->date_checked)) $cr_todo->date_checked = $todo->date_checked ;

			if(isset($cr_todo->is_deleted)) {
				if(!$cr_todo->is_deleted) $cr_todo->save();
				else $cr_todo->delete();
			}
			if (isset($todo->labels)){
				foreach($todo->labels as $label) {
					$label = Label::where('todoist_id', $label)->first();
					$cr_todo->labels()->attach($label->id);
				}
			}
		}
	}
}
