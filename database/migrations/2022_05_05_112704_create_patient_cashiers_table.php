<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientCashiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_cashiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('top_most_parent_id');
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('receipt_no');
            $table->date('date');
            $table->enum('type', [1, 2])->comment('1:IN, 2:OUT');
            $table->float('amount', 9, 2)->nullable();
            $table->string('file')->nullable();
            $table->text('comment')->nullable();
            $table->integer('created_by');
            $table->string('entry_mode', 25);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_cashiers');
    }
}
