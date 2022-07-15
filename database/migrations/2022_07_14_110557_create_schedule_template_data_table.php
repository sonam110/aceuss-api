<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleTemplateDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_template_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('schedule_template_id');
            $table->foreign('schedule_template_id')->references('id')->on('schedules')->onDelete('cascade');

            $table->unsignedBigInteger('shift_id')->nullable();
            $table->foreign('shift_id')->references('id')->on('company_work_shifts')->onDelete('cascade');

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->enum('schedule_type',['basic','extra','emergency'])->nullable();
            $table->string('shift_name')->nullable();
            $table->string('shift_type')->nullable();
            $table->string('shift_date');
            $table->datetime('shift_start_time')->nullable();
            $table->datetime('shift_end_time')->nullable();
            $table->string('shift_color')->nullable();
            $table->boolean('is_active')->default(0);
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
        Schema::dropIfExists('schedule_template_data');
    }
}
