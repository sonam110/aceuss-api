<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmergencyContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();


            $table->unsignedBigInteger('top_most_parent_id')->comment('User Table id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');
            

            $table->unsignedBigInteger('user_id')->comment('User Table id')->nullable();
            $table->foreign('user_id')->references('id')->on('user_types')->onDelete('cascade');
            
            $table->string('contact_number')->nullable();
            $table->boolean('is_default')->default('0')->comment('1:Yes,0:No');
            $table->string('entry_mode')->nullable();
            $table->foreignId('created_by');
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
        Schema::dropIfExists('emergency_contacts');
    }
}
