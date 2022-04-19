<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityTimeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id');
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->date('action_date')->nullable();
            $table->time('action_time')->nullable();
            $table->integer('time_diff')->nullable();
            $table->integer('action_by')->nullable();
            $table->tinyInteger('status')->default(0)->comment('1:Done,2:Not Done,3:notapplicable');
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
        Schema::dropIfExists('activity_time_logs');
    }
}
