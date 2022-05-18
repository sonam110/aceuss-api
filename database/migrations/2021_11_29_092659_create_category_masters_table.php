<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_masters', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('category_masters')->onDelete('cascade');
            
            $table->unsignedBigInteger('category_type_id');
            $table->foreign('category_type_id')->references('id')->on('category_types')->onDelete('cascade');
            
            $table->string('name');
            $table->string('category_color')->nullable();
            $table->boolean('is_global')->default('0')->comment('1:Yes,0:No');
            $table->boolean('status')->default('1')->comment('1:Active,0:Inactive');
            $table->string('follow_up_image')->nullable();
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
        Schema::dropIfExists('category_masters');
    }
}
