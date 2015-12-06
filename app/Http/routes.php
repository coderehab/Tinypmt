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



Route::get('/', ['as' => 'homepage', 'uses' => 'AppController@get_index']);
Route::get('/project/{id}', ['as' => 'project_single', 'uses' => 'ProjectController@get_single']);




Route::get('/sync', ['as' => 'todoist_sync', 'uses' => 'TodoistController@syncdata']);
Route::get('todoist', ['as' => 'todoist', 'uses' => 'TodoistController@test']);
Route::get('login/authorized', ['as' => 'todoist', 'uses' => 'TodoistController@authorized']);

