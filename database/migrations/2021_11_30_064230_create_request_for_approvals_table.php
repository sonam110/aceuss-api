<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestForApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_for_approvals', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('top_most_parent_id')->nullable();
            $table->foreign('top_most_parent_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('requested_by')->nullable();
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('requested_to')->nullable();
            $table->foreign('requested_to')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('request_type_id')->nullable();
            $table->foreign('request_type_id')->references('id')->on('request_types')->onDelete('cascade');

            $table->string('request_type');
            $table->string('group_token')->comment('if request_type_id is multiple then action performed according to this');
            $table->foreignId('request_type_id');
            $table->string('reason_for_requesting');
            $table->text('reason_for_rejection')->nullable();
            $table->text('other_info')->nullable();
            $table->date('approved_date')->nullable();
            $table->tinyInteger('approval_type')->nullable()->comment('1:Manual, 2:Digital Signature, 3:Mobile Bank Id');
            $table->enum('status', ['0','1','2','3'])->default('0')->comment('0:inactive, 1:active, 2:approved ,3:rejected');
            $table->string('entry_mode')->nullable();
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
        Schema::dropIfExists('request_for_approvals');
    }
}
