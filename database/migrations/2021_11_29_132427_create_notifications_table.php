<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->foreignId('user_id');
            $table->foreignId('device_id');
            $table->boolean('device_platform')->comment('1:android,2:ios');
            $table->integer('user_type')->nullable();
            $table->string('module')->nullable();
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->text('message')->nullable();
            $table->string('image_url')->nullable();
            $table->string('screen')->nullable();
            $table->integer('data_id')->nullable();
            $table->boolean('read_status')->default(0);
            $table->timestamp('read_at')->nullable();
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
        Schema::dropIfExists('notifications');
    }
}
