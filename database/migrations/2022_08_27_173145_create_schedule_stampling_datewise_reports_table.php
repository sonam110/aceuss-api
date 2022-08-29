<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleStamplingDatewiseReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_stampling_datewise_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->date('date');
            $table->string('scheduled_duration')->default(0)->nullable()->comment('total scheduled');
            $table->string('stampling_duration')->default(0)->nullable()->comment('total worked');
            $table->string('ob_duration')->default(0)->nullable();
            $table->string('regular_duration')->default(0)->nullable()->comment('stampling_duration - ob_duration');
            $table->string('extra_duration')->default(0)->nullable()->comment('regular_duration - scheduled_duration');
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
        Schema::dropIfExists('schedule_stampling_datewise_reports');
    }
}
