<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deviations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('top_most_parent_id');
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('emp_id')->nullable();
            $table->foreign('emp_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('category_masters')->onDelete('cascade');

            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->foreign('subcategory_id')->references('id')->on('category_masters')->onDelete('cascade');

            $table->timestamp('date_time');
            $table->text('description');
            $table->text('immediate_action');
            $table->text('probable_cause_of_the_incident')->nullable();
            $table->text('suggestion_to_prevent_event_again')->nullable();
            $table->integer('critical_range')->comment('1 to 5');
            $table->text('related_factor')->nullable();
            
            $table->text('follow_up')->nullable();
            $table->text('further_investigation')->nullable();
            $table->text('copy_sent_to')->nullable();

            $table->boolean('is_secret')->default(0)->nullable();
            $table->boolean('is_signed')->default(0)->nullable();
            $table->boolean('is_completed')->default(0)->nullable();
            $table->bigInteger('completed_by')->nullable();
            $table->date('completed_date')->nullable();

            $table->text('reason_for_editing')->nullable();
            $table->bigInteger('edited_by')->nullable();
            $table->date('edited_date')->nullable();
            $table->string('entry_mode')->nullable();
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
        Schema::dropIfExists('deviations');
    }
}
