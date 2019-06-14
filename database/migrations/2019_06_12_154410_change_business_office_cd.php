<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBusinessOfficeCd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_business_offices', function (Blueprint $table) {
            $table->string('mst_business_office_cd',3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_business_offices', function (Blueprint $table) {
            $table->integer('mst_business_office_cd')->change();
        });
    }
}
