<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_type_id');
            $table->foreignId('company_type_id');
            $table->foreignId('category_id')->nullable();
            $table->foreignId('top_most_parent_id')->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('dept_id')->nullable();
            $table->string('govt_id')->nullable();
            $table->double('weekly_hours_alloted_by_govt')->default(0);
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('contact_number');
            $table->string('gender')->nullable();
            $table->string('personal_number')->nullable();
            $table->string('organization_number')->nullable();
            $table->string('zipcode')->nullable();
            $table->text('full_address')->nullable();
            $table->string('license_key')->nullable();
            $table->date('license_end_date')->nullable();
            $table->boolean('license_status')->default('1')->comment('1:Active,0:Inactive');
            $table->boolean('is_substitute')->default('0')->comment('1:Yes,0:No');
            $table->boolean('is_regular')->default('0')->comment('1:Yes,0:No');
            $table->boolean('is_seasonal')->default('0')->comment('1:Yes,0:No');
            $table->date('joining_date')->nullable();
            $table->date('establishment_date')->nullable();
            $table->string('user_color')->nullable();
            $table->boolean('is_file_required')->default('0')->comment('1:Yes,0:No');
            $table->boolean('status')->default('1')->comment('1:Active,0:Inactive');
             $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
