<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBatchConvert extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // mst_staffs
        Schema::table('mst_staffs', function (Blueprint $table) {
            $table->string('address1',200)->change();
            $table->string('address2',200)->change();
            $table->string('address3',200)->change();
            $table->string('notes',255)->change();
        });

        // mst_vehicles
        Schema::table('mst_vehicles', function (Blueprint $table) {
            $table->string('registration_numbers',50)->nullable()->change();
            $table->integer('mst_business_office_id')->nullable()->change();
        });

        // mst_customers
        Schema::table('mst_customers', function (Blueprint $table) {
            $table->string('address1',200)->change();
            $table->string('address2',200)->change();
            $table->string('address3',200)->change();
        });

        // mst_suppliers
        Schema::table('mst_suppliers', function (Blueprint $table) {
            $table->string('address1',200)->change();
            $table->string('address2',200)->change();
            $table->string('address3',200)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // mst_staffs
        Schema::table('mst_staffs', function (Blueprint $table) {
            $table->string('address1',20)->change();
            $table->string('address2',20)->change();
            $table->string('address3',50)->change();
            $table->string('notes',50)->change();
        });

        // mst_vehicles
        Schema::table('mst_vehicles', function (Blueprint $table) {
            $table->string('registration_numbers',50)->change();
            $table->integer('mst_business_office_id')->change();
        });

        // mst_customers
        Schema::table('mst_customers', function (Blueprint $table) {
            $table->string('address1',20)->change();
            $table->string('address2',20)->change();
            $table->string('address3',50)->change();
        });

        // mst_suppliers
        Schema::table('mst_suppliers', function (Blueprint $table) {
            $table->string('address1',20)->change();
            $table->string('address2',20)->change();
            $table->string('address3',50)->change();
        });
    }
}
