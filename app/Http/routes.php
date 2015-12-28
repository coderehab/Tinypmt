<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/login', array('as'=>'user_login', 'uses'=>'UserController@get_login'));
Route::post('/login', array('as'=>'user_login', 'uses'=>'UserController@post_login'));
Route::get('/logout', array('as'=>'user_logout', 'uses'=>'UserController@get_logout'));
Route::get('/register', array('as'=>'user_registration', 'uses'=>'UserController@get_register'));
Route::post('/register', array('as'=>'user_registration', 'uses'=>'UserController@post_register'));

Route::group(array('before' => 'auth'), function(){

    Route::get('/edit/user/{id}', array('as'=>'edit_user', 'uses'=>'UserController@edit_user'));
    Route::post('/edit/user/{id}', array('as'=>'update_user', 'uses'=>'UserController@put_user'));
    Route::get('/delete/user/{id}', array('as'=>'remove_user', 'uses'=>'UserController@remove_user'));

    Route::get('/settings', array('as'=>'settings', 'uses'=>'AppController@get_settings'));
    Route::get('/settings/team', array('as'=>'settings_team', 'uses'=>'AppController@get_team_settings'));

    Route::get('/settings/labels', array('as'=>'settings_labels', 'uses'=>'LabelController@get_index'));
    Route::post('/settings/labels', array('as'=>'save_label', 'uses'=>'LabelController@post_label'));
    Route::get('/settings/labels/remove/{id}', array('as'=>'remove_label', 'uses'=>'LabelController@remove_label'));
    Route::get('/settings/user/label/remove/{user_id}/{label_id}', array('as'=>'remove_label_from_user', 'uses'=>'LabelController@remove_label_from_user'));
    Route::post('/settings/user/label/add/{user_id}/', array('as'=>'add_label_to_user', 'uses'=>'LabelController@add_label_to_user'));

    Route::get('/', ['as' => 'homepage', 'uses' => 'AppController@get_index']);
    Route::get('/project/{id}', ['as' => 'project_single', 'uses' => 'ProjectController@get_single']);

    Route::get('/sync', ['as' => 'todoist_sync', 'uses' => 'TodoistController@syncdata']);
    Route::get('/update_planning', ['as' => 'update_planning', 'uses' => 'PlanningController@updatePlanning']);

    Route::get('todoist', ['as' => 'todoist', 'uses' => 'TodoistController@test']);
    Route::get('login/authorized', ['as' => 'todoist', 'uses' => 'TodoistController@authorized']);

    Route::post('todo/estimate/{id}', ['as' => 'save_todo_estimate', 'uses' => 'TaskController@update_estimate']);


    Route::get('sync/calendar', ['as' => 'sync_google_calendar', 'uses' => 'GoogleCalendarController@get_index']);

});
