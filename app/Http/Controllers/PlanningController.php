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

            $previousTime += $todo->estimated_time;
            $todo->due_date = $this->calcDate($startDate, $previousTime);
            $todo->save();


        }

    }

    private function calcDate($startDate, $curTime) {

        $days_to_add = ($curTime - $curTime%8) / 8;
        $hours_to_add = $curTime%8;

        $totalToAdd = strtotime(sprintf("+%d hours", $hours_to_add)) + strtotime(sprintf("+%d days", $days_to_add));
        $totalToAdd -= strtotime(date('Y-m-d'));

        //ignore weekend
        if (date("w", $totalToAdd) == 6) $totalToAdd+=strtotime("+1 days")-strtotime(date('Y-m-d'));
        if (date("w", $totalToAdd) == 0) $totalToAdd+=strtotime("+1 days")-strtotime(date('Y-m-d'));

        $date = date('Y-m-d', $totalToAdd);

        return $date;
    }
}
