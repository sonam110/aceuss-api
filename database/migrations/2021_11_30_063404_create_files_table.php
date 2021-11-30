<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('top_most_parent_id');
            $table->foreignId('folder_id');
            $table->foreignId('source_id');
            $table->string('source_name');
            $table->string('file_url');
            $table->string('file_type');
            $table->string('file_extension');
            $table->boolean('is_compulsory')->default(0);
            $table->boolean('approval_required')->default(0);
            $table->foreignId('created_by');
            $table->foreignId('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->string('visible_to_users')->nullable();
            $table->softDeletes();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('files');
    }
}
