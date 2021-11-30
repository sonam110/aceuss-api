<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpAssigneToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_assigne_to_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ip_id');
            $table->foreignId('user_id');
            $table->boolean('status')->default(0);
            $table->foreign('ip_id')->references('id')->on('patient_implementation_plans')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('ip_assigne_to_employees');
    }
}
