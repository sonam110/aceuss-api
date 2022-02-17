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
            $table->foreignId('user_id');
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('category_id');
            $table->foreignId('subcategory_id');
            $table->text('what_happened');
            $table->text('how_it_happened');
            $table->string('when_it_started');
            $table->text('what_to_do');
            $table->text('goal');
            $table->text('sub_goal')->nullable();
            $table->date('plan_start_date');
            $table->timestamp('plan_start_time');
            $table->text('remark')->nullable();
            $table->text('activity_message')->nullable();
            $table->boolean('save_as_template')->default(0);
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
