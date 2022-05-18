<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileAccessLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('top_most_parent_id')->comment('User Table id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('admin_file_id')->comment('Admin File id')->nullable();
            $table->foreign('admin_file_id')->references('id')->on('admin_files')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->comment('User id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('file_access_logs');
    }
}
