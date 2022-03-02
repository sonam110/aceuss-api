<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceLoginHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_login_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('User Table id');
            $table->string('device_id')->nullable();
            $table->string('device_model')->nullable();
            $table->string('device_token')->nullable();
            $table->lontext('user_token')->nullable();
            $table->enum('login_via', ['0', '1','2'])->default('0')->comment('0:Web,1:android,2:ios');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
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
        Schema::dropIfExists('device_login_history');
    }
}
