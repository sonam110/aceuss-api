<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicenceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('licence_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('top_most_parent_id')->comment('User Table id')->nullable();
            
            $table->unsignedBigInteger('created_by')->comment('User Table id')->nullable();           

            $table->string('licence_key', 50);
            $table->text('module_attached');
            $table->text('package_details');
            $table->date('active_from')->nullable()->comment('licence_key activation date');
            $table->date('expire_at')->comment('expiry date');
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
        Schema::connection('mysql2')->dropIfExists('licence_histories');
    }
}
