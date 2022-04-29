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
            $table->foreignId('top_most_parent_id')->comment('User Table id')->nullable();
            $table->foreignId('admin_file_id')->comment('Admin File id')->nullable();
            $table->foreignId('user_id')->comment('User id')->nullable();
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
