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
            $table->unsignedBigInteger('ip_id')->nullable();
            $table->foreign('ip_id')->references('id')->on('patient_implementation_plans')->onDelete('cascade');

            $table->unsignedBigInteger('follow_up_id')->nullable();
            $table->foreign('follow_up_id')->references('id')->on('ip_follow_ups')->onDelete('cascade');

            $table->string('name');
            $table->string('email');
            $table->string('contact_number');
            $table->string('full_address');
            $table->boolean('is_family_member')->default(0);
            $table->boolean('is_caretaker')->default(0);
            $table->boolean('is_contact_person')->default(0);
            $table->foreign('ip_id')->references('id')->on('patient_implementation_plans')->onDelete('cascade');
            $table->string('entry_mode')->nullable();
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
