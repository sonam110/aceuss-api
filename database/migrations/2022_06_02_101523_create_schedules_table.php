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

            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('shift_id')->nullable();
            $table->foreign('shift_id')->references('id')->on('company_work_shifts')->onDelete('cascade');

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('schedules')->onDelete('cascade');

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('slot_assigned_to')->nullable();
            $table->foreign('slot_assigned_to')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('employee_assigned_working_hour_id')->nullable();
            $table->foreign('employee_assigned_working_hour_id')->references('id')->on('employee_assigned_working_hours')->onDelete('cascade');

            $table->unsignedBigInteger('schedule_template_id')->nullable()->comment('active schedule template');
            $table->foreign('schedule_template_id')->references('id')->on('schedule_templates')->onDelete('cascade');

            $table->enum('schedule_type',['basic','extra'])->nullable();
            $table->string('group_id')->nullable()->comment('only for schedule');
            $table->string('shift_name')->nullable();
            $table->string('shift_date');
            $table->datetime('shift_start_time')->nullable();
            $table->datetime('shift_end_time')->nullable();
            $table->string('shift_color')->nullable();
            //--------------for-leave-management-------------------//
            $table->boolean('leave_applied')->default(0);
            $table->string('leave_group_id')->nullable()->comment('only for leave');
            $table->enum('leave_type',['leave','vacation','extra'])->nullable();
            $table->text('leave_reason')->nullable();
            $table->boolean('leave_approved')->default(0);
            $table->datetime('leave_approved_date_time')->nullable();
            $table->string('leave_notified_to')->nullable();
            $table->string('notified_group')->nullable();
            //-----------------------------------------------------//
            $table->boolean('is_active')->default(0);
            $table->string('scheduled_work_duration')->default(0)->nullable();
            $table->string('extra_work_duration')->default(0)->nullable();
            $table->boolean('status')->default(0)->nullable();
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
        Schema::dropIfExists('schedules');
    }
}
