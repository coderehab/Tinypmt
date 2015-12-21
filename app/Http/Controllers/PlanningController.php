<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

use Request;

use App\Project;
use App\Todo;
use App\User;

class PlanningController extends Controller
{

    private $schedules = [];
    private $todo_list = [];

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
            $estimated_time = $todo->estimated_time * 3600;

            $task_is_planned = $this->addTodoToSchedule($todo);

            if(!$task_is_planned){
                foreach ($this->schedules as $user_id => $user_agenda){
                    $this->addDayToSchedule($user_id);
                }
                $task_is_planned = $this->addTodoToSchedule($todo);

            }

            if($task_is_planned){
                $item_order+=1;
            }
        }
    }

    private function addTodoToSchedule($todo){
        $task_is_planned = 0;
        $estimated_time = $todo->estimated_time * 3600;
        foreach ($this->schedules as $user_id => $user_agenda){

            foreach($user_agenda as $key => $schedule){
                $timeAvailable = $user_agenda[$key]["timeAvailable"];
                foreach($user_agenda[$key]["scheduled"] as $scheduled_todo) {
                    $timeAvailable -= $scheduled_todo->estimated_time * 3600;
                }

                if($timeAvailable >= $estimated_time && !$task_is_planned) {
                    $task_is_planned = 1;
                    $todo->user_id = $user_id;
                    $todo->due_date = date("Y-m-d H:i:s", strtotime($schedule['date']));
                    $todo->save();
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
            $todos = Todo::where("project_id", '=', $project->todoist_id)->orderBy('priority', 'desc')->get();
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
