<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailabilityTimesheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			Schema::create('availability_timesheets', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('user_id');

				$table->integer('is_available');
				$table->string('date_string');

				$table->string('date')->nullable();
				$table->string('starttime');
				$table->string('endtime');

				$table->integer('is_recurring');
				$table->integer('recurring_count');
				$table->integer('recurring_step');

				$table->timestamps();
			});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
			Schema::drop('availability_timesheets');
    }
}
