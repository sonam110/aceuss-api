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
            $table->foreignId('ip_id');
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('top_most_parent_id');
            $table->string('title');
            $table->text('description');
            $table->enum('follow_up_type', ['1','2'])->default('1')->comment('1:one_time , 2:ongoing');
            $table->enum('repetition_type', ['1','2','3'])->nullable()->comment('1:daily,2:weekly,3:monthly ');
            $table->string('repetition_days')->nullable();
            $table->date('start_date');
            $table->timestamp('start_time');
            $table->boolean('is_completed')->default(0);
            $table->date('end_date')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->text('remarks');
            $table->text('reason_for_editing')->nullable();
            $table->foreignId('created_by');
            $table->foreignId('edited_by')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->boolean('status')->default(0);
            $table->softDeletes();
            $table->foreign('ip_id')->references('id')->on('patient_implementation_plans')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('ip_follow_ups')->onDelete('cascade');
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('ip_follow_ups');
    }
}
