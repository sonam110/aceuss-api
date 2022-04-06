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
            $table->text('what_happened')->nullable();
            $table->text('how_it_happened')->nullable();
            $table->text('goal')->nullable();
            $table->text('sub_goal')->nullable();
            $table->date('plan_start_date')->nullable();
            $table->time('plan_start_time')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->text('remark')->nullable();
            $table->text('activity_message')->nullable();
            $table->boolean('save_as_template')->default(0);
            $table->text('reason_for_editing')->nullable();
            $table->foreignId('created_by');
            $table->foreignId('edited_by')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->boolean('status')->default(0);
            $table->tinyInteger('step_one_status')->default(1)->comment('1:Not Completed,2:Partially Completed,3:Completed');
            $table->tinyInteger('step_two_status')->default(1)->comment('1:Not Completed,2:Partially Completed,3:Completed');
            $table->tinyInteger('step_three_status')->default(1)->comment('1:Not Completed,2:Partially Completed,3:Completed');
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
