<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Like India, United States');
            $table->string('country_code',10)->comment('Like IN, US');
            $table->string('dial_code',10)->comment('Like +91, +12');
            $table->string('currency', 50)->comment('Like Indian rupee, United States dollar');
            $table->string('currency_code', 10)->comment('Like INR, USD');
            $table->string('currency_symbol', 50)->comment('Like ₹, $');
            $table->boolean('is_govt_certifcate_valid')->default(false);
            $table->string('entry_mode')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        \DB::unprepared(file_get_contents(public_path('seeder/countries.sql')));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
