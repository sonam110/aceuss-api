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
            $table->date('date');
            $table->datetime('in_time');
            $table->datetime('out_time')->nullable();
            $table->string('in_location');
            $table->string('out_location')->nullable();
            $table->string('extra_hours');
            $table->string('reason_for_early_in')->nullable();
            $table->string('reason_for_early_out')->nullable();
            $table->string('reason_for_late_in')->nullable();
            $table->string('reason_for_late_out')->nullable();
            $table->boolean('is_extra_hours_approved')->default(0)->nullable();
            $table->boolean('is_scheduled_hours_ov_hours')->default(0)->nullable();
            $table->boolean('scheduled_hours_rate')->default(0)->nullable();
            $table->boolean('is_extra_hours_ov_hours')->default(0)->nullable();
            $table->boolean('extra_hours_rate')->default(0)->nullable();
            $table->float('scheduled_hours_sum')->default(0)->nullable();
            $table->float('extra_hours_sum')->default(0)->nullable();
            $table->float('total_sum')->default(0)->nullable();
            $table->boolean('status')->default(0)->nullable();
            $table->string('entry_mode')->nullable();
            // $table->string('latitude')->nullable();
            // $table->string('longitude')->nullable();
            // $table->string('ip_address')->nullable();
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
