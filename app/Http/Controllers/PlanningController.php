<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

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
    private $todo_list = [];

    public function updatePlanning(){
        $this->prepareSchedule();
        Redirect::back();
    }

    public function createSchedule(){
        $this->prepareSchedule();
    }

    private function prepareSchedule() {
        $users = User::all();

        foreach ($users as $user) {
            $this->schedules[$user->id] = [];
            $this->addDayToSchedule($user->id);
        }

        $this->todo_list = $this->prepareTodos();
        $this->setUserTasks();
        /*

        echo "<pre>";
        print_r($this->schedules);
        echo "</pre>";
        die;*/
    }

    private function addDayToSchedule($user_id) {
        $user = User::find($user_id);
        $this->initTimesheets($user);
    }

    private function initTimesheets($user) {
        $user_schedule = $this->schedules[$user->id];
        $timeavailable = 0;

        foreach ($user->availability->get() as $timesheet){
            $timeavailable += strtotime($timesheet->endtime) - strtotime($timesheet->starttime);
        }

        if(count($this->schedules[$user->id]) > 0){
            $daystoadd = count($this->schedules[$user->id]);
            $date = date("d-m-Y") + strtotime("+$daystoadd days");
        }else{
            $date = strtotime(date("d-m-Y"));
        }

        if(date('N', $date) >= 6) $timeavailable = 0;

        $this->addTimeToSchedule(date("d-m-Y", $date), $user->id, $timeavailable);
    }

    private function addTimeToSchedule($date, $user_id, $timeavailable) {
        $this->schedules[$user_id][] = ["date" => $date, "timeAvailable"=>$timeavailable, "scheduled" => array()];
    }


    private function setUserTasks() {
        //var_dump($this->schedules);
        $item_order = 0;
        foreach($this->todo_list as $todo) {
            $task_is_planned = 0;
            $todo->due_date = '';
            $todo->user_id = 0;
            $estimated_time = $todo->estimated_time * 3600;
            if($estimated_time <= 0)continue;

            $task_is_planned = $this->addTodoToSchedule($todo);

            while(!$task_is_planned){
                foreach ($this->schedules as $user_id => $user_agenda){
                    $this->addDayToSchedule($user_id);
                }
                $task_is_planned = $this->addTodoToSchedule($todo);
            }

            if($task_is_planned){
                $item_order+=1;
            }


            if(isset($todo->user->todoist_id) && count($todo->getDirty()) > 0){
                $client = new Client();
                $commands = new stdClass();
                $commands->uuid = uniqid();
                $commands->type = "item_update";
                $commands->args = new stdClass();
                $commands->args->id = $todo->todoist_id;
                $commands->args->date_string = date("Y-m-d", strtotime($todo->due_date));
                $commands->args->due_date_utc = date("Y-m-d\TH:i:s\Z", strtotime($todo->due_date));
                $commands->args->date_lang = "nl";
                $commands->args->responsible_uid = $todo->user->todoist_id;

                $response = $client->request('POST', 'https://todoist.com/API/v6/sync', [
                    "form_params" => [
                        "token"=>"dbadf3381fc34496c555e87111cd0b4d7d9eecee",
                        "commands" => json_encode([$commands])
                    ]
                ]);
                $data = json_decode($response->getBody()->getContents());
            }

            $todo->save();

        }

    }

    private function addTodoToSchedule($todo){
        $task_is_planned = 0;
        $estimated_time = $todo->estimated_time * 3600;
        foreach ($this->schedules as $user_id => $user_agenda){
            $user = User::find($user_id);
            if($todo->checked == 1) continue;
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
            $todos = Todo::where("project_id", $project->todoist_id)->where('priority', 4)->orderBy('priority', 'desc')->get();
            foreach ($todos as $todo){
                $todo_list[] = $todo;
            };
        };
        foreach ($projects as $project){
            $todos = Todo::where("project_id", $project->todoist_id)->where('priority', 3)->orderBy('priority', 'desc')->get();
            foreach ($todos as $todo){
                $todo_list[] = $todo;
            };
        };
        foreach ($projects as $project){
            $todos = Todo::where("project_id", $project->todoist_id)->where('priority', 2)->orderBy('priority', 'desc')->get();
            foreach ($todos as $todo){
                $todo_list[] = $todo;
            };
        };
        foreach ($projects as $project){
            $todos = Todo::where("project_id", $project->todoist_id)->where('priority', 1)->orderBy('priority', 'desc')->get();
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
