<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');

            $table->enum('assign_status',['vacant','assigned'])->nullable();
            $table->string('group_id')->nullable();
            $table->date('date');
            $table->text('reason')->nullable(0);
            $table->boolean('is_approved')->default(0)->nullable();
            $table->date('approved_date')->nullable();
            $table->string('approved_time')->nullable();
            $table->string('notified_to')->nullable();
            $table->string('notified_group')->nullable();
            $table->boolean('status')->default(0)->comment('0 for not approved, 1 for approved');
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
        Schema::dropIfExists('leaves');
    }
}
