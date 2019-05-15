<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditTableMstVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_vehicles', function (Blueprint $table) {
            $table->string('registration_numbers1',10)->nullable()->after('registration_numbers');
            $table->char('registration_numbers2',3)->nullable()->after('registration_numbers1');
            $table->char('registration_numbers3',1)->nullable()->after('registration_numbers2');
            $table->char('registration_numbers4',4)->nullable()->after('registration_numbers3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_vehicles', function (Blueprint $table) {
            //
            $table->dropColumn('registration_numbers1');
            $table->dropColumn('registration_numbers2');
            $table->dropColumn('registration_numbers3');
            $table->dropColumn('registration_numbers4');
        });
    }
}
