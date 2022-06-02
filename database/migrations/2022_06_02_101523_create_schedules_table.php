<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id')->references('id')->on('company_work_shifts')->onDelete('cascade');

            $table->string('shift_name');
            $table->time('shift_start_time');
            $table->time('shift_end_time');
            $table->string('shift_color')->nullable();

            $table->string('shift_date');
            $table->boolean('is_red_day')->default(0);
            $table->boolean('is_ov_hours')->default(0);
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
        Schema::dropIfExists('schedules');
    }
}
