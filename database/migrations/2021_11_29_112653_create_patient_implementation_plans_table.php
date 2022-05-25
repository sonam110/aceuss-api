<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientImplementationPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_implementation_plans', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('patient_implementation_plans')->onDelete('cascade');

            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('category_masters')->onDelete('cascade');

            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->foreign('subcategory_id')->references('id')->on('category_masters')->onDelete('cascade');

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('action_by')->nullable();
            $table->foreign('action_by')->references('id')->on('users')->onDelete('cascade');


            $table->string('title');
            $table->boolean('save_as_template')->default(0);
            $table->string('goal')->nullable();
            $table->string('limitations')->nullable();
            $table->text('limitation_details')->nullable();
            $table->text('how_support_should_be_given')->nullable();
            $table->string('week_days')->nullable();
            $table->integer('how_many_time')->nullable();
            $table->longText('when_during_the_day')->nullable();
            $table->text('who_give_support')->nullable();
            $table->string('sub_goal')->nullable();
            $table->text('sub_goal_details')->nullable();
            $table->string('sub_goal_selected')->nullable();
            $table->string('overall_goal')->nullable();
            $table->text('overall_goal_details')->nullable();
            $table->text('body_functions')->nullable();
            $table->text('personal_factors')->nullable();
            $table->text('health_conditions')->nullable();
            $table->text('other_factors')->nullable();
            $table->text('treatment')->nullable();
            $table->text('working_method')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('documents')->nullable();
            $table->text('reason_for_editing')->nullable();
            $table->date('approved_date')->nullable();
            $table->date('action_date')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('status')->default(0);
            $table->tinyInteger('step_one')->default(0)->comment('0:Pending,1:Partial Completed,2:Completed');
            $table->tinyInteger('step_two')->default(0)->comment('0:Pending,1:Partial Completed,2:Completed');
            $table->tinyInteger('step_three')->default(0)->comment('0:Pending,1:Partial Completed,2:Completed');
            $table->tinyInteger('step_four')->default(0)->comment('0:Pending,1:Partial Completed,2:Completed');
            $table->tinyInteger('step_five')->default(0)->comment('0:Pending,1:Partial Completed,2:Completed');
            $table->tinyInteger('step_six')->default(0)->comment('0:Pending,1:Partial Completed,2:Completed');
            $table->tinyInteger('step_seven')->default(0)->comment('0:Pending,1:Partial Completed,2:Completed');
            $table->string('entry_mode')->nullable();
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
        Schema::dropIfExists('patient_implementation_plans');
    }
}
