<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpFollowUpCreationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_follow_up_creations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ip_id');
            $table->foreignId('follow_up_id')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('contact_number');
            $table->string('full_address');
            $table->boolean('is_family_member')->default(0);
            $table->boolean('is_caretaker')->default(0);
            $table->boolean('is_contact_person')->default(0);
            $table->foreign('ip_id')->references('id')->on('patient_implementation_plans')->onDelete('cascade');
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
        Schema::dropIfExists('ip_follow_up_creations');
    }
}
