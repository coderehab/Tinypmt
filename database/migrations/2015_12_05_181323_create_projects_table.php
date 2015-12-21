<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('todoist_id');
            $table->integer('user_id');
            $table->string('name');
            $table->integer('priority');

            $table->integer('collapsed');
            $table->integer('inbox_project');

            $table->integer('item_order');
            $table->integer('indent');

            $table->integer('shared');
            $table->integer('is_archived');
            $table->date('archived_date')->nullable();
            $table->timestamp('archived_timestamp');

            $table->timestamps();

            //Extended (not available with todoist)
            $table->float('tracked_time');
            $table->float('estimated_time');
            $table->float('start_date');
            $table->float('due_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('projects');
    }
}
