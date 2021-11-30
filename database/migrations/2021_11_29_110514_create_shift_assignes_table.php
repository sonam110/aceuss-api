<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftAssignesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_assignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('top_most_parent_id');
            $table->foreignId('user_id');
            $table->integer('shift_id');
            $table->date('shift_start_date');
            $table->date('shift_end_date');
            $table->boolean('status')->default(1);
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('shift_assignes');
    }
}
