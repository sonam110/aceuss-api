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
            $table->foreignId('patient_id');
            $table->string('institute_name')->nullable();
            $table->string('institute_contact_number')->nullable();
            $table->text('institute_full_address')->nullable();
            $table->string('classes_from')->nullable();
            $table->string('classes_to')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_contact_number')->nullable();
            $table->text('company_full_address')->nullable();
            $table->text('from_timing')->nullable();
            $table->text('to_timing')->nullable();
            $table->string('aids')->nullable();
            $table->text('another_activity')->nullable();
            $table->text('another_activity_name')->nullable();
            $table->text('activitys_contact_number')->nullable();
            $table->text('activitys_full_address')->nullable();
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
