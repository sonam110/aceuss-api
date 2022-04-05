<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('top_most_parent_id')->nullable();
            $table->tinyInteger('type_id')->nullable()->comment('1:Activity,2:IP,3:User,4:Deviation,5:FollowUps,6:Journal,7:Patient,8:Employee,9:');
            $table->foreignId('resource_id')->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('branch_id')->nullable();
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
            $table->tinyInteger('remind_before_start')->default(0);
            $table->integer('before_minutes')->nullable();
            $table->tinyInteger('before_is_text_notify')->default(0);
            $table->tinyInteger('before_is_push_notify')->default(0);
            $table->tinyInteger('remind_after_end')->default(0);
            $table->integer('after_minutes')->nullable();
            $table->tinyInteger('after_is_text_notify')->default(0);
            $table->tinyInteger('after_is_push_notify')->default(0);
            $table->tinyInteger('is_emergency')->default(0);
            $table->integer('emergency_minutes')->nullable();
            $table->tinyInteger('emergency_is_text_notify')->default(0);
            $table->tinyInteger('emergency_is_push_notify')->default(0);
            $table->tinyInteger('in_time')->default(0);
            $table->tinyInteger('in_time_is_text_notify')->default(0);
            $table->tinyInteger('in_time_is_push_notify')->default(0);
            $table->foreignId('created_by');
            $table->foreignId('edited_by')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=Not Done,1:Done');
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
        Schema::dropIfExists('tasks');
    }
}
