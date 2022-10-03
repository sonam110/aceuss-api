<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClmToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('group_name')->after('guard_name');
            $table->string('se_name')->after('group_name')->unique();
            $table->text('description')->after('se_name')->nullable();
            $table->tinyInteger('belongs_to')->default(1)->after('description')->comment('1:Admin,2:Store,3:Other');
            $table->string('entry_mode')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('group_name');
            $table->dropColumn('se_name');
            $table->dropColumn('description');
            $table->dropColumn('belongs_to');
            $table->dropColumn('entry_mode');
        });
    }
}
