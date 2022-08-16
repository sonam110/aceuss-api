<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpFollowUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_follow_ups', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('ip_id')->nullable();
            $table->foreign('ip_id')->references('id')->on('patient_implementation_plans')->onDelete('cascade');

            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('ip_follow_ups')->onDelete('cascade');


            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('action_by')->nullable();
            $table->foreign('action_by')->references('id')->on('users')->onDelete('cascade');


            $table->text('emp_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->boolean('is_completed')->default(0);
            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();
            $table->text('remarks')->nullable();
            $table->text('reason_for_editing')->nullable();
            $table->date('approved_date')->nullable();
            $table->longText('documents')->nullable();
            $table->timestamp('action_date')->nullable();
            $table->text('comment')->nullable();
            $table->text('witness')->nullable();
            $table->text('more_witness')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0:Pending,1:Approved,2:Completed,3:Reject,4:Hold');
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
        Schema::dropIfExists('ip_follow_ups');
    }
}
