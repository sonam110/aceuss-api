<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_options', function (Blueprint $table) {
            $table->id();
            $table->string('option')->nullable();
            $table->boolean('is_journal')->default('0')->comment('1:Yes,0:No');
            $table->boolean('is_deviation')->default('0')->comment('1:Yes,0:No');
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
        Schema::dropIfExists('activity_options');
    }
}
