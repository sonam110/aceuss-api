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
            $table->foreignId('top_most_parent_id')->nullable();
            $table->foreignId('user_id');
            $table->string('shift_name');
            $table->time('shift_start_time');
            $table->time('shift_end_time');
            $table->string('shift_color')->nullable();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('company_work_shifts');
    }
}
