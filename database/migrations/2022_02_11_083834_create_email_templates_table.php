<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_template_id', function (Blueprint $table) {
            $table->id();
            $table->string('mail_sms_for');
            $table->longtext('mail_subject')->nullable();
            $table->longtext('mail_body')->nullable();
            $table->longtext('sms_body')->nullable();
            $table->longtext('notify_body')->nullable();
            $table->text('custom_attributes')->nullable();
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
        Schema::dropIfExists('email_templates');
    }
}
