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
            $table->foreignId('top_most_parent_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->date('start_date')->nullable();
            $table->timestamp('start_time');
            $table->tinyInteger('is_repeat')->default('0')->comment('0:No,1:Yes');
            $table->integer('every')->nullable();
            $table->tinyInteger('repetition_type')->nullable()->comment('1:day,2:week,3:month,4:Year');
            $table->text('week_days')->nullable();
            $table->integer('month_day')->nullable()->comment('example day 1 ,day2-----last day');
            $table->boolean('is_completed')->default(0);
            $table->date('end_date')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->text('remarks')->nullable();
            $table->text('reason_for_editing')->nullable();
            $table->foreignId('created_by');
            $table->foreignId('edited_by')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0:Pending,1:Approved,2:Completed,3:Reject,4:Hold');
            $table->softDeletes();
            $table->string('entry_mode')->nullable();
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
