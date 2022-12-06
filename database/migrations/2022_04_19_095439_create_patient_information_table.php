<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_information', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('patient_id')->comment('User Table id')->nullable();
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('special_information')->nullable();
            $table->string('institute_name')->nullable();
            $table->string('institute_contact_number')->nullable();
            $table->string('institute_contact_person')->nullable();
            $table->text('institute_full_address')->nullable();
            $table->text('institute_week_days')->nullable();
            $table->string('classes_from', 50)->nullable();
            $table->string('classes_to', 50)->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_contact_person')->nullable();
            $table->string('company_contact_number')->nullable();
            $table->text('company_full_address')->nullable();
            $table->text('company_week_days')->nullable();
            $table->string('from_timing', 50)->nullable();
            $table->string('to_timing', 50)->nullable();
            $table->string('aids')->nullable();
            $table->string('another_activity')->nullable();
            $table->string('another_activity_name')->nullable();
            $table->string('another_activity_contact_person')->nullable();
            $table->string('activitys_contact_number')->nullable();
            $table->text('activitys_full_address')->nullable();
            $table->string('another_activity_start_time')->nullable();
            $table->string('another_activity_end_time')->nullable();
            $table->text('week_days')->nullable();
            $table->string('issuer_name')->nullable();
            $table->string('number_of_hours')->nullable();
            $table->string('period')->nullable();
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
        Schema::dropIfExists('patient_information');
    }
}
