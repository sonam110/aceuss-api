<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->nullable();
            $table->foreignId('top_most_parent_id')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('patient_id')->nullable();
            $table->foreignId('emp_id')->nullable();
            $table->foreignId('category_id');
            $table->foreignId('subcategory_id');
            $table->string('date')->default(date('Y-m-d'));
            $table->string('time')->default(date("h:i"));
            $table->text('description');
            $table->text('reason_for_editing')->nullable();
            $table->foreignId('edited_by')->nullable();
            $table->date('edit_date')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->string('entry_mode')->nullable();
            $table->boolean('is_signed')->default(0);
            $table->boolean('is_secret')->default(0);
            $table->boolean('is_active')->default(0);
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
        Schema::dropIfExists('journals');
    }
}
