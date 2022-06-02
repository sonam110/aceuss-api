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

            $table->unsignedBigInteger('schedule_id');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->time('in_time');
            $table->time('out_time');
            $table->string('extra_hours');
            $table->string('reason_for_extra_hours')->default(0);
            $table->boolean('is_extra_hours_approved')->default(0);
            $table->boolean('status')->default(0);
            $table->string('entry_mode')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('stamplings');
    }
}
