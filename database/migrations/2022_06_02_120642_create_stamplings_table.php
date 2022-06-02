<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStamplingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stamplings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->time('in_time');
            $table->time('out_time');
            $table->string('extra_hours');
            $table->string('reason_for_extra_hours')->default(0);
            $table->boolean('is_extra_hours_approved')->default(0);
            $table->boolean('is_scheduled_hours_ov_hours')->default(0);
            $table->boolean('scheduled_hours_rate')->default(0);
            $table->boolean('is_extra_hours_ov_hours')->default(0);
            $table->boolean('extra_hours_rate')->default(0);
            $table->float('scheduled_hours_sum')->default(0);
            $table->float('extra_hours_sum')->default(0);
            $table->float('total_sum')->default(0);
            $table->boolean('status')->default(0);
            $table->string('entry_mode')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stamplings');
    }
}
