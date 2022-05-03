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
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('deviation_id')->nullable();
            $table->foreignId('activity_id')->nullable();
            $table->foreignId('top_most_parent_id')->nullable();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('patient_id')->nullable();
            $table->foreignId('emp_id')->nullable();
            $table->foreignId('category_id');
            $table->foreignId('subcategory_id');
            // $table->string('title');
            $table->date('date')->default(date('Y/m/d'));
            $table->time('time')->nullable();
            $table->text('description');
            $table->enum('status', ['0','1','2'])->default('0')->comment('0:Pending  , 1:Approved ,2:Rejected');
            $table->text('reason_for_editing')->nullable();
            $table->foreignId('edited_by')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->boolean('is_deviation')->default(0);
            $table->boolean('is_social')->default(0);
            $table->string('entry_mode')->nullable();
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
