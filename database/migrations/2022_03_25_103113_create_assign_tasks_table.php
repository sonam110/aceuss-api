<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_tasks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('task_id')->comment('User Table id')->nullable();
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            

            $table->unsignedBigInteger('user_id')->comment('User Table id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->foreignId('task_id');
            $table->foreignId('user_id');
            $table->date('assignment_date')->nullable();
            $table->foreignId('assigned_by');
            $table->tinyInteger('status')->default(0)->comment('0:Not Done,1:done');
            $table->tinyInteger('is_notify')->default(0)->comment('0:Not send,1:send');
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
        Schema::dropIfExists('assign_tasks');
    }
}
