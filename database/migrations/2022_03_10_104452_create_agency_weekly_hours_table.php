<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencyWeeklyHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_weekly_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('name')->nullable();
            $table->decimal('assigned_hours',[10,2])->default(0);
            $table->decimal('assigned_hours_per_day',[10,2])->default(0);
            $table->decimal('assigned_hours_per_week',[10,2])->default(0);
            $table->decimal('assigned_hours_per_month',[10,2])->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('entry_mode')->nullable();
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
        Schema::dropIfExists('agency_weekly_hours');
    }
}
