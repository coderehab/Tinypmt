<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

use Request;

use App\Project;
use App\Todo;

class PlanningController extends Controller
{
    public function updatePlanning($project) {

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

        $startDate = date('Y-m-d', strtotime("+1 days"));
        $previousTime = 0;
        foreach($todos as $todo) {

            $todo->due_date = $this->calcDate($startDate, $previousTime);
            $todo->save();

            $previousTime += $todo->estimated_time;
        }

    }

    private function calcDate($startDate, $curTime) {

        $days_to_add = ($curTime - $curTime%8) / 8;
        $hours_to_add = $curTime%8;

        $totalToAdd = strtotime(sprintf("+%d hours", $hours_to_add)) + strtotime(sprintf("+%d days", $days_to_add));


        $totalToAdd -= strtotime(date('Y-m-d'));
        if (date("w", $totalToAdd) == 6){
            $totalToAdd+=strtotime("+1 days")-strtotime(date('Y-m-d'));

        }
        if (date("w", $totalToAdd) == 0){
            $totalToAdd+=strtotime("+1 days")-strtotime(date('Y-m-d'));
        }


        $date = date('Y-m-d', $totalToAdd);


        return $date;
    }

    private function randomDate($start_date, $end_date)
    {
        // Convert to timetamps
        $min = strtotime($start_date);
        $max = strtotime($end_date);

        // Generate random number using above bounds
        $val = rand($min, $max);

        // Convert back to desired date format
        return date('Y-m-d', $val);
    }
}
