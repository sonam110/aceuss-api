<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileBankIdLoginLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_bank_id_login_logs', function (Blueprint $table) {
            $table->id();
            $table->string('top_most_parent_id', 50)->comment('comes from users table (user company id)');
            $table->string('sessionId');
            $table->string('personnel_number');
            $table->string('name');

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
        Schema::dropIfExists('mobile_bank_id_login_logs');
    }
}
