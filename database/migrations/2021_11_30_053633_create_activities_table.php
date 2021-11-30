<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('top_most_parent_id');
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('activity_class_id');
            $table->foreignId('ip_id')->nullable();
            $table->foreignId('patient_id')->nullable();
            $table->foreignId('emp_id')->nullable();
            $table->foreignId('shift_id')->nullable();
            $table->foreignId('category_id');
            $table->foreignId('subcategory_id');
            $table->string('title');
            $table->text('description');
            $table->enum('activity_type', ['1','2'])->default('1')->comment('1:one_time , 2:ongoing');
            $table->enum('repetition_type', ['1','2','3'])->nullable()->comment('1:daily,2:weekly,3:monthly ');
            $table->string('repetition_days')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('external_link')->nullable();
            $table->enum('activity_status', ['1','2','3'])->nullable()->comment('1:done ,2:notdone,3:notapplicable ');
            $table->string('done_by')->nullable();
            $table->string('not_done_by')->nullable();
            $table->enum('not_done_reason', ['1','2'])->nullable()->comment('1:Patient not wanted ,2:Employee did it wrong ');
            $table->text('not_applicable_reason')->nullable();
            $table->string('notity_to_users')->nullable();
            $table->text('reason_for_editing')->nullable();
            $table->foreignId('created_by');
            $table->foreignId('edited_by')->nullable();
            $table->date('edit_date')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->enum('status', ['1','2','3','4'])->nullable()->comment('0=inactive 1:Active,2:Completed,3:Approved ,4:Rejected');
            $table->boolean('remind_before_start')->default(0);
            $table->boolean('remind_after_end')->default(0);
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('activities')->onDelete('cascade');
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
        Schema::dropIfExists('activities');
    }
}
