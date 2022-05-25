<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('type_id')->nullable();

            $table->string('top_most_parent_id', 50)->comment('comes from users table (user company id)');
            
            $table->string('resource_id', 50)->nullable()->comment('comes from any table');
            $table->string('mobile');
            $table->text('message');
            $table->integer('status')->nullable();

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
        Schema::dropIfExists('sms_logs');
    }
}
