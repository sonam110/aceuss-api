<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssigneModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assigne_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->integer('module_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('assigne_modules');
    }
}
