<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabelConnections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('label_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('label_id');
        });

        Schema::create('label_todo', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('todo_id');
            $table->integer('label_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('label_user');
        Schema::drop('label_todo');
    }
}
