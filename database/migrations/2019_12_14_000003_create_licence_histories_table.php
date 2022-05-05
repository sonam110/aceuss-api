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
        Schema::create('licence_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('top_most_parent_id');
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->string('license_key', 50);
            $table->text('module_attached');
            $table->date('active_from')->nullable()->comment('license_key activation date');
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
        Schema::dropIfExists('licence_histories');
    }
}
