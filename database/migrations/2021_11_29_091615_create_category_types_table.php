<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_types', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('top_most_parent_id')->comment('User Table id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('created_by')->comment('User Table id');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('name');
            $table->boolean('status')->default('1')->comment('1:Active,0:Inactive');
            $table->string('entry_mode')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('category_types');
    }
}
