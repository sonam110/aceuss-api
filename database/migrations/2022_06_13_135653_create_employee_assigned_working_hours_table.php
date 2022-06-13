<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeAssignedWorkingHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_assigned_working_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('emp_id');
            $table->foreign('emp_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->string('assigned_working_hour_per_week')->nullable();
            $table->string('working_percent')->default(100)->nullable();
            $table->string('actual_working_hour_per_week')->nullable(0);
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
        Schema::dropIfExists('employee_assigned_working_hours');
    }
}
