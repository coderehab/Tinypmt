<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

use App\Services\GoogleCalendar;

use Request;
use Redirect;

use App\Project;
use App\Todo;
use App\User;

use stdClass;

use GuzzleHttp\Client;

class PlanningController extends Controller
{

	private $schedules = [];
	private $userGoogleTimesheets = [];
	private $todo_list = [];

	public function updatePlanning(){
		$this->createSchedule();
		return Redirect::back();
	}

	public function createSchedule(){
		$this->prepareSchedule();
	}

	private function prepareSchedule() {
		$users = User::all();

		foreach ($users as $user) {
			if(!$user->is_team) continue;
			$this->schedules[$user->id] = [];

			$calendar = new GoogleCalendar;
			$calendarId = "edgarravenhorst@gmail.com";
			$this->userGoogleTimesheets[$user->id] = $calendar->get_time_available($user->google_calendar_id);

			$this->addDayToSchedule($user->id);
		}

		$this->todo_list = $this->prepareTodos();
		$this->setUserTasks();

	}

	private function addDayToSchedule($user_id) {
		$user = User::find($user_id);
		$this->initTimesheets($user);
	}

	private function initTimesheets($user) {
		$user_schedule = $this->schedules[$user->id];
		$timeavailable = 0;

/*		foreach ($user->availability->get() as $timesheet){
			$timeavailable += strtotime($timesheet->endtime) - strtotime($timesheet->starttime);
		}*/

		if(count($this->schedules[$user->id]) > 0){
			$daystoadd = count($this->schedules[$user->id]);
			$date = date("d-m-Y") + strtotime("+$daystoadd days");
		}else{
			$date = strtotime(date("d-m-Y"));
		}

		//var_dump($date);

		foreach ($this->userGoogleTimesheets[$user->id] as $timesheet){
			//echo date("d-m-Y", $date) . "-----" . date("d-m-Y", strtotime($timesheet->start->dateTime)) . " - " . date("d-m-Y", strtotime($timesheet->end->dateTime)) . "<br />";

			$googleStartdate = date("d-m-Y", strtotime($timesheet->start->dateTime));
			$googleEnddate = date("d-m-Y", strtotime($timesheet->end->dateTime));

			if(date("d-m-Y", $date) == $googleStartdate && date("d-m-Y", $date) == $googleEnddate) {
				$timeavailable += strtotime($timesheet->end->dateTime) - strtotime($timesheet->start->dateTime);
			}
		}

		$this->addTimeToSchedule(date("d-m-Y", $date), $user->id, $timeavailable);
	}

	private function addTimeToSchedule($date, $user_id, $timeavailable) {
		$this->schedules[$user_id][] = ["date" => $date, "timeAvailable"=>$timeavailable, "scheduled" => array()];
	}


	private function setUserTasks() {
		//var_dump($this->schedules);
		$item_order = 0;
		$item_update_count = 0;
		$todoist_commands = [];
		$counter = 0;
		set_time_limit(60);
		foreach($this->todo_list as $todo) {
			//var_dump($counter++);
			$task_is_planned = 0;
			$todo->due_date = '';
			$todo->user_id = 0;
			$estimated_time = $todo->estimated_time * 3600;


			if($estimated_time <= 0 ) continue;


			$task_is_planned = $this->addTodoToSchedule($todo);

			$loop = 0;
			while(!$task_is_planned && $loop < 10){
				foreach ($this->schedules as $user_id => $user_agenda){
					$this->addDayToSchedule($user_id);
				}
				$task_is_planned = $this->addTodoToSchedule($todo);
				$loop +=1;
			}

			if($task_is_planned){
				$item_order+=1;
				$todo->update();
			}


			if(isset($todo->user->todoist_id)){
				$item_update_count ++;
				$commands = new stdClass();
				$commands->uuid = uniqid();
				$commands->type = "item_update";
				$commands->args = new stdClass();
				$commands->args->id = $todo->todoist_id;
				$commands->args->date_string = date("Y-m-d", strtotime($todo->due_date));
				$commands->args->due_date_utc = date("Y-m-d\TH:i:s\Z", strtotime($todo->due_date));
				$commands->args->date_lang = "nl";
				$commands->args->responsible_uid = $todo->user->todoist_id;
				$todoist_commands[] = $commands;
			}

			if($item_update_count == 99){
				$client = new Client();
				$response = $client->request('POST', 'https://todoist.com/API/v6/sync', [
					"form_params" => [
						//"token"=>"dbadf3381fc34496c555e87111cd0b4d7d9eecee",
						"token"=>"31ecf41c4338d45dd4c6ad65f706207366691925",
						"commands" => json_encode($todoist_commands)
					]
				]);
				$data = json_decode($response->getBody()->getContents());
				$todoist_commands = [];
				$item_update_count == 0;
			}
		}

		$client = new Client();
		$response = $client->request('POST', 'https://todoist.com/API/v6/sync', [
			"form_params" => [
				//"token"=>"dbadf3381fc34496c555e87111cd0b4d7d9eecee",
				"token"=>"31ecf41c4338d45dd4c6ad65f706207366691925",
				"commands" => json_encode($todoist_commands)
			]
		]);
		$data = json_decode($response->getBody()->getContents());
		//die;
	}

	private function addTodoToSchedule($todo){
		$task_is_planned = 0;
		$estimated_time = $todo->estimated_time * 3600;
		foreach ($this->schedules as $user_id => $user_agenda){
			$user = User::find($user_id);
			if(!$user->is_team) continue;
			if($todo->checked == 1) continue;

			$user_has_project = false;
			foreach ($user->projects as $project) {
				if($project->todoist_id == $todo->project->todoist_id) {
					$user_has_project = true;
					break;
				}
			}
			if(!$user_has_project) continue;

			if(count(array_intersect($todo->labels()->lists("name")->all(), $user->labels()->lists("name")->all())) == 0) continue;

			foreach($user_agenda as $key => $schedule){
				$timeAvailable = $user_agenda[$key]["timeAvailable"];
				foreach($user_agenda[$key]["scheduled"] as $scheduled_todo) {
					$timeAvailable -= $scheduled_todo->estimated_time * 3600;
				}

				if($timeAvailable >= $estimated_time && !$task_is_planned) {
					$task_is_planned = 1;
					$todo->user_id = $user_id;
					$todo->due_date = date("Y-m-d H:i:s", strtotime($schedule['date']));
					$user_agenda[$key]["scheduled"][] = $todo;
				}else{
					continue;
				}
			}

			$this->schedules[$user_id] = $user_agenda;
		}

		return $task_is_planned;
	}

	private function prepareTodos() {
		$todo_list = [];
		$projects = Project::orderBy('priority', 'desc')->get();

		foreach ($projects as $project){
			$todos = Todo::where("project_id", $project->todoist_id)->where('priority', 4)->where("estimated_time", ">", 0)->where("checked", 0)->get();
			foreach ($todos as $todo){
				$todo_list[] = $todo;
			};
		};
		foreach ($projects as $project){
			$todos = Todo::where("project_id", $project->todoist_id)->where('priority', 3)->where("estimated_time", ">", 0)->where("checked", 0)->get();
			foreach ($todos as $todo){
				$todo_list[] = $todo;
			};
		};
		foreach ($projects as $project){
			$todos = Todo::where("project_id", $project->todoist_id)->where('priority', 2)->where("estimated_time", ">", 0)->where("checked", 0)->get();
			foreach ($todos as $todo){
				$todo_list[] = $todo;
			};
		};
		foreach ($projects as $project){
			$todos = Todo::where("project_id", $project->todoist_id)->where('priority', 1)->where("estimated_time", ">", 0)->where("checked", 0)->get();
			foreach ($todos as $todo){
				$todo_list[] = $todo;
			};
		};

		return $todo_list;
	}

	private function user_is_available($user_id){

	}

	private function newTimeInterval($starttime, $endtime){

		return [];
	}

	private function isPersonAvailable($user_id){

		return true;
	}

	private function calculateProjectEstimates($project) {
		$todos = Todo::where("project_id", '=', $project->todoist_id)->orderBy('priority', 'desc')->get();

		$estimated_time = 0;
		foreach($todos as $todo) {
			$estimated_time += $todo->estimated_time;
		}

		$project->estimated_time = $estimated_time;
		$estimated_time = $estimated_time/8;

		$now = date("Y-m-d");
		$project->due_date = date('Y-m-d', strtotime(sprintf("+%d days", $estimated_time)));

		$project->save();
	}
}
