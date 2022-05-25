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
            $table->string('unique_id')->unique()->nullable();
            $table->string('custom_unique_id')->unique()->nullable();

            $table->unsignedBigInteger('user_type_id')->nullable();
            $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade');

            $table->unsignedBigInteger('category_id')->nullable();
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('dept_id')->nullable();
            // $table->foreign('dept_id')->references('id')->on('departments')->onDelete('cascade');
            
            $table->integer('role_id')->nullable();
            $table->text('company_type_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->string('govt_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('contact_number')->nullable();
            $table->string('gender')->nullable();
            $table->string('personal_number')->nullable();
            $table->string('organization_number')->nullable();
            $table->string('patient_type_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_area')->nullable();
            $table->string('zipcode')->nullable();
            $table->text('full_address')->nullable();
            $table->string('license_key')->nullable();
            $table->date('license_end_date')->nullable();
            $table->boolean('license_status')->default('1')->comment('1:Active,0:Inactive');
            $table->boolean('is_substitute')->default('0')->comment('1:Yes,0:No');
            $table->boolean('is_regular')->default('0')->comment('1:Yes,0:No');
            $table->boolean('is_seasonal')->default('0')->comment('1:Yes,0:No');
            $table->date('joining_date')->nullable();
            $table->integer('establishment_year')->nullable();
            $table->string('user_color')->nullable();
            $table->text('disease_description')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->string('password_token')->nullable();
            $table->boolean('is_file_required')->default('0')->comment('1:Yes,0:No');
            $table->boolean('is_secret')->default('0')->comment('1:Yes,0:No');
            $table->boolean('status')->default('1')->comment('1:Active,0:Inactive,2:deleted');
            $table->boolean('is_fake')->default('0')->comment('1:Yes,0:No');
            $table->boolean('is_password_change')->default('0')->comment('1:Yes,0:No');
            $table->text('documents')->nullable();
            $table->tinyInteger('step_one')->default(0)->comment('0:Pending,1:Partial Completed,2:Completed');
            $table->tinyInteger('step_two')->default(0)->comment('0:Pending,1:Partial Completed,2:Completed');
            $table->tinyInteger('step_three')->default(0)->comment('0:Pending,1:Partial Completed,2:Completed');
            $table->tinyInteger('step_four')->default(0)->comment('0:Pending,1:Partial Completed,2:Completed');
            $table->tinyInteger('step_five')->default(0)->comment('0:Pending,1:Partial Completed,2:Completed');
            $table->string('entry_mode')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
