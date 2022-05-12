<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('top_most_parent_id')->comment('User Table id')->nullable();
            $table->text('title');
            $table->string('file_path');
            $table->boolean('is_public')->default(1);
            $table->integer('user_type_id')->nullable();
            $table->text('company_ids')->nullable()->comment('if admin wants to share this file to selected company');
            $table->unsignedBigInteger('created_by');
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
        Schema::dropIfExists('admin_files');
    }
}
