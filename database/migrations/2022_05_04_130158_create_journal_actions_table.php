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
            
            $table->unsignedBigInteger('journal_id')->nullable();
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('cascade');
            
            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('signed_by')->nullable();
            $table->foreign('signed_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->text('comment_action')->nullable();
            $table->text('comment_result')->nullable();
            $table->text('reason_for_editing')->nullable();
            $table->datetime('edit_date')->nullable();
            $table->boolean('is_signed')->default(0);
            $table->string('signed_method')->default(0)->nullable();
            $table->text('signed_response')->default(0)->nullable();
            $table->timestamp('signed_date')->nullable();
            $table->string('sessionId')->nullable();
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
        Schema::dropIfExists('journal_actions');
    }
}
