<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTypeHasPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_type_has_permissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_type_id')->comment('User Table id')->nullable();
            $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade');
            
            $table->unsignedBigInteger('permission_id')->comment('User Table id')->nullable();
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            
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
        Schema::dropIfExists('user_type_has_permissions');
    }
}
