<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditDeleteColumnStartEndDt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // mst_staffs
        Schema::table("mst_staffs", function (Blueprint $table) {
            $table->dropColumn('adhibition_start_dt');
            $table->dropColumn('adhibition_end_dt');
        });
        // mst_vehicles
        Schema::table("mst_vehicles", function (Blueprint $table) {
            $table->dropColumn('adhibition_start_dt');
            $table->dropColumn('adhibition_end_dt');
        });
        // mst_customers
        Schema::table("mst_customers", function (Blueprint $table) {
            $table->dropColumn('adhibition_start_dt');
            $table->dropColumn('adhibition_end_dt');
        });
        // mst_suppliers
        Schema::table("mst_suppliers", function (Blueprint $table) {
            $table->dropColumn('adhibition_start_dt');
            $table->dropColumn('adhibition_end_dt');
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
    }
}
