<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('price')->default(0);
            $table->boolean('is_on_offer')->default('0')->comment('1:Yes,0:No');
            $table->enum('discount_type', ['1','2'])->default(1)->comment('1:Percentage ,2:Direct Value');
            $table->integer('discount_value')->default(0);
            $table->double('discounted_price')->default(0);
            $table->integer('validity_in_days');
            $table->integer('number_of_patients');
            $table->integer('number_of_employees');
            $table->tinyInteger('status')->default('1')->comment('1:Active,0:Inactive,2:Deleted');
            $table->string('entry_mode')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('packages');
    }
}
