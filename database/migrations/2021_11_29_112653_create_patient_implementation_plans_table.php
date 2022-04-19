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
            $table->foreignId('top_most_parent_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->foreignId('subcategory_id')->nullable();
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
            $table->foreignId('created_by');
            $table->foreignId('edited_by')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('patient_implementation_plans');
    }
}
