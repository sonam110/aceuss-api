<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalInfoDuringIpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_info_during_ips', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('ip_id')->nullable();
            $table->foreign('ip_id')->references('id')->on('patient_implementation_plans')->onDelete('cascade');


            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('follow_up_id')->nullable();
            $table->foreign('follow_up_id')->references('id')->on('ip_follow_ups')->onDelete('cascade');

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_area')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('full_address')->nullable();
            $table->string('personal_number')->nullable();
            $table->boolean('is_family_member')->default(0);
            $table->boolean('is_caretaker')->default(0);
            $table->boolean('is_contact_person')->default(0);
            $table->boolean('is_guardian')->default(0);
            $table->boolean('is_other')->default(0);
            $table->boolean('is_presented')->default(0);
            $table->boolean('is_participated')->default(0);
            $table->text('how_helped')->nullable();
            $table->string('is_other_name')->nullable();
            $table->string('entry_mode')->nullable();
            $table->boolean('is_approval_requested')->default(0);
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
        Schema::dropIfExists('personal_info_during_ips');
    }
}
