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
            $table->foreignId('journal_action_id')->nullable();
            $table->foreignId('top_most_parent_id')->nullable();
            $table->text('comment_action');
            $table->text('comment_result');
            $table->text('reason_for_editing')->nullable();
            $table->foreignId('edited_by')->nullable();
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