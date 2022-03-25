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
            $table->tinyInteger('is_repeat')->default(0)->comment('0:No,1:Yes');
            $table->integer('every')->nullable();
            $table->tinyInteger('repetition_type')->nullable()->comment('1:day,2:week,3:month,4:Year');
            $table->text('week_days')->nullable();
            $table->integer('month_day')->nullable()->comment('example day 1 ,day2-----last day');
            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();
            $table->string('address_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('information_url')->nullable();
            $table->string('file')->nullable();
            $table->text('reason_for_editing')->nullable();
            $table->foreignId('created_by');
            $table->foreignId('edited_by')->nullable();
            $table->date('edit_date')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->string('question')->nullable();
            $table->text('selected_option')->nullable();
            $table->text('comment')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=Pending ,1:Done,2:Not Done,3:notapplicable');
            $table->foreignId('action_by')->nullable();
            $table->tinyInteger('remind_before_start')->default(0);
            $table->tinyInteger('remind_after_end')->default(0);
            $table->tinyInteger('is_emergency')->default(0);
            $table->tinyInteger('in_time')->default(0);
            $table->integer('minutes')->nullable();
            $table->tinyInteger('is_text_notify')->default(0);
            $table->tinyInteger('is_push_notify')->default(0);
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
        Schema::dropIfExists('activities');
    }
}
