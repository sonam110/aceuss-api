<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('deviation_id')->nullable();
            $table->foreignId('activity_id')->nullable();
            $table->foreignId('top_most_parent_id')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('patient_id')->nullable();
            $table->foreignId('emp_id')->nullable();
            $table->foreignId('category_id');
            $table->foreignId('subcategory_id');
            $table->string('type')->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->text('description');
            $table->text('reason_for_editing')->nullable();
            $table->foreignId('edited_by')->nullable();
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
        Schema::dropIfExists('journal_logs');
    }
}
