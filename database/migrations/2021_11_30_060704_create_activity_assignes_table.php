<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityAssignesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_assignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id');
            $table->foreignId('user_id');
            $table->date('assignment_date')->nullable();
            $table->string('assignment_day')->nullable();
            $table->foreignId('assigned_by');
            $table->tinyInteger('status')->default(0)->comment('0:Pending,1:done ,2:notdone,3:notapplicable');
            $table->text('reason')->nullable();
            $table->string('entry_mode')->nullable();
            $table->tinyInteger('is_notify')->default(0);
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
        Schema::dropIfExists('activity_assignes');
    }
}
