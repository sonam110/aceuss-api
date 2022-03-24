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
            $table->foreignId('top_most_parent_id')->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('activity_class_id')->nullable();
            $table->foreignId('ip_id')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('patient_id')->nullable();
            $table->foreignId('emp_id')->nullable();
            $table->foreignId('shift_id')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->foreignId('subcategory_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->tinyInteger('is_repeat')->default('0')->comment('0:No,1:Yes');
            $table->integer('every')->nullable();
            $table->tinyInteger('repetition_type')->nullable()->comment('1:day,2:week,3:month,4:Year');
            $table->text('week_days')->nullable();
            $table->integer('month_day')->nullable()->comment('example day 1 ,day2-----last day');
            $table->date('end_date')->nullable();
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
            $table->string('question')->nullable();
            $table->text('selected_option')->nullable();
            $table->text('comment')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=inactive 1:Active,2:Completed,3:Approved ,4:Rejected');
            $table->boolean('remind_before_start')->default(0);
            $table->boolean('remind_after_end')->default(0);
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
        Schema::dropIfExists('activities');
    }
}
