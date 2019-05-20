<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMstStaffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_staffs', function (Blueprint $table) {
            //
            $table->integer('approval_levels')->nullable()->after('sysadmin_flg');
            $table->integer('section_id')->nullable()->after('approval_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('mst_staffs', function (Blueprint $table) {
            //
            $table->dropColumn('approval_levels');
            $table->dropColumn('section_id');
        });
    }
}
