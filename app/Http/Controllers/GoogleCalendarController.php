<?php

namespace App\Http\Controllers;
use App\Http\Controllers\PlanningController;
use Illuminate\Support\Facades\View;

use Input;
use Request;
use Redirect;

use App\Project;
use App\Todo;

use App\Services\GoogleCalendar;


class GoogleCalendarController extends PlanningController
{
	public function get_index() {
		$calendar = new GoogleCalendar;
		$calendarId = "edgarravenhorst@gmail.com";
		$results = $calendar->get_time_available($calendarId);

		foreach ($results as $timesheet){
			//var_dump($timesheet);
			echo  date("d-m-Y", strtotime($timesheet->start->dateTime)) . " - " . date("d-m-Y", strtotime($timesheet->end->dateTime)) . "<br />";
		}

	}
}
