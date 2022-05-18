<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalActionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_action_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('journal_action_id')->nullable();
            $table->foreign('journal_action_id')->references('id')->on('journal_actions')->onDelete('cascade');
            
            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->text('comment_action')->nullable();
            $table->text('comment_result')->nullable();
            $table->text('reason_for_editing')->nullable();
            $table->datetime('comment_created_at');
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
        Schema::dropIfExists('journal_action_logs');
    }
}
