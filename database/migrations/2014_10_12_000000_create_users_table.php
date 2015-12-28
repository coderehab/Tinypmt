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
            $table->integer('is_personel');
            $table->rememberToken();
            $table->timestamps();

            $table->text('labels');

            //Extended (not available with todoist)
            $table->float('monday_default_available');
            $table->float('tuesday_default_available');
            $table->float('wednesday_default_available');
            $table->float('thursday_default_available');
            $table->float('friday_default_available');
            $table->float('saturday_default_available');
            $table->float('sunday_default_available');

            $table->text('dates_unavailable');
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
