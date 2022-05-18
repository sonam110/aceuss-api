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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('device_id')->nullable();
            $table->string('device_model')->nullable();
            $table->string('device_token')->nullable();
            $table->longtext('user_token')->nullable();
            $table->string('ip_address')->nullable();
            $table->enum('login_via', ['0', '1','2'])->default('0')->comment('0:Web,1:android,2:ios');
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
