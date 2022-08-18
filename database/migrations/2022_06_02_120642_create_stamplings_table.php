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

            $table->enum('stampling_type',['scheduled','walkin'])->default('scheduled')->nullable();

            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->date('date');
            $table->datetime('in_time');
            $table->datetime('out_time')->nullable();
            $table->datetime('rest_start_time')->nullable();
            $table->datetime('rest_end_time')->nullable();
            $table->string('in_location');
            $table->string('out_location')->nullable();

            $table->string('reason_for_early_in')->nullable();
            $table->string('reason_for_early_out')->nullable();
            $table->string('reason_for_late_in')->nullable();
            $table->string('reason_for_late_out')->nullable();

            $table->float('scheduled_hours_rate',8,2)->nullable()->comment('amount or salary / hr');
            $table->float('extra_hours_rate',8,2)->nullable()->comment('amount or salary / hr');
            $table->float('ob_hours_rate',8,2)->nullable()->comment('amount or salary / hr');
            $table->string('ob_type')->nullable();

            $table->float('total_schedule_hours',8,2)->default(0)->nullable()->comment('total worked - ob worked - extra worked',8,2);
            $table->float('total_extra_hours',8,2)->default(0)->nullable();
            $table->float('total_ob_hours',8,2)->default(0)->nullable();
            
            $table->string('working_percent')->default(0)->nullable();

            $table->enum('is_extra_hours_approved',[0,1,2])->default(1)->nullable()->comment('0 for pending,1 for approved,2 for rejected');
            $table->string('reason_for_rejection')->nullable();
            $table->string('entry_mode')->nullable();
            $table->enum('logout_by',['self','system'])->default('self')->nullable();
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
