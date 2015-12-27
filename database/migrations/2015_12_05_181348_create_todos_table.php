<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('todoist_id');
            $table->integer('user_id');
            $table->integer('project_id');
            $table->integer('sync_id')->nullable();

            $table->integer('assigned_by_uid');
            $table->integer('responsible_uid')->nullable();

            $table->text('content');
            $table->text('labels');

            $table->integer('checked');
            $table->integer('collapsed');
            $table->string('day_order');
            $table->integer('indent');
            $table->integer('priority');
            $table->integer('children')->nullable();

            $table->integer('item_order');
            $table->integer('in_history');
            $table->integer('is_deleted');
            $table->integer('is_archived');

            $table->string('date_lang')->nullable();
            $table->string('date_added');
            $table->string('due_date')->nullable();
            $table->string('due_date_utc')->nullable();
            $table->string('date_string')->nullable();

            //Extended (not available with todoist)
            $table->float('estimated_time');
            $table->string('date_checked')->nullable();

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
        Schema::drop('todos');
    }
}
