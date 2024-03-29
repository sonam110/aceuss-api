<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyWorkShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_work_shifts', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->enum('shift_type',['normal','emergency', 'sleeping_emergency'])->default('normal')->nullable();
            $table->string('shift_name');
            $table->time('shift_start_time');
            $table->time('shift_end_time');
            $table->time('rest_start_time')->nullable();
            $table->time('rest_end_time')->nullable();
            $table->string('shift_color')->nullable();
            $table->boolean('status')->default(0);
            $table->string('entry_mode')->nullable();
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
        Schema::dropIfExists('company_work_shifts');
    }
}
