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

            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');


            $table->string('group_id', 50)->nullable();
            
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('parent_id')->comment('User Table id')->nullable();
            $table->foreign('parent_id')->references('id')->on('tasks')->onDelete('cascade');
            
            $table->unsignedBigInteger('user_type_id')->comment('User Table id')->nullable();
            $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade');
            
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('action_by')->nullable();
            $table->foreign('action_by')->references('id')->on('users')->onDelete('cascade');

            $table->tinyInteger('type_id')->nullable()->comment('1:Activity,2:IP,3:User,4:Deviation,5:FollowUps,6:Journal,7:Patient,8:Employee,9:');
            $table->foreignId('resource_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->integer('how_many_time')->nullable();
            $table->tinyInteger('is_repeat')->default(0)->comment('0:No,1:Yes');
            $table->integer('every')->nullable();
            $table->tinyInteger('repetition_type')->nullable()->comment('1:day,2:week,3:month,4:Year');
            $table->longText('how_many_time_array')->nullable();
            $table->longText('repeat_dates')->nullable();
            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();
            $table->string('file')->nullable();
            $table->tinyInteger('remind_before_start')->default(0);
            $table->integer('before_minutes')->nullable();
            $table->tinyInteger('before_is_text_notify')->default(0);
            $table->tinyInteger('before_is_push_notify')->default(0);
            $table->timestamp('action_date')->nullable();
            $table->text('comment')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=Not Done,1:Done');
            $table->boolean('is_latest_entry')->default(1);
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
        Schema::dropIfExists('tasks');
    }
}
