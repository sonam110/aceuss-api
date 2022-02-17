<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityAssignesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_assignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id');
            $table->foreignId('user_id');
            $table->date('assignment_date');
            $table->string('assignment_day');
            $table->foreignId('assigned_by');
            $table->boolean('status')->default(1);
            $table->string('entry_mode')->nullable();
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
        Schema::dropIfExists('activity_assignes');
    }
}
