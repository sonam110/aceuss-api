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
