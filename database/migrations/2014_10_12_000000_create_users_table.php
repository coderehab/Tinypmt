<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
	/**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::create('users', function (Blueprint $table) {
			$table->increments('id');
			$table->string('todoist_id');
			$table->string('todoist_token');
			$table->string('firstname');
			$table->string('lastname');
			$table->string('email')->unique();
			$table->string('google_calendar_id');
			$table->string('password', 60);
			$table->string('timezone', 60);
			$table->integer('is_deleted');
			$table->text('labels');
			$table->rememberToken();
			$table->timestamps();



			//Extended (not available with todoist)
			$table->integer('is_team');
		});
	}

	/**
     * Reverse the migrations.
     *
     * @return void
     */
	public function down()
	{
		Schema::drop('users');
	}
}
