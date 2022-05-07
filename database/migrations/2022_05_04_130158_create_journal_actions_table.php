<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->nullable();
            $table->foreignId('top_most_parent_id')->nullable();
            $table->text('comment_action');
            $table->text('comment_result');
            $table->text('reason_for_editing')->nullable();
            $table->foreignId('edited_by')->nullable();
            $table->datetime('edit_date')->nullable();
            $table->boolean('is_signed')->default(0);
            $table->foreignId('signed_by')->nullable();
            $table->date('signed_date')->nullable();
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
        Schema::dropIfExists('journal_actions');
    }
}
