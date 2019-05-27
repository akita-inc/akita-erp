<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDaysToWfPaidVacation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wf_paid_vacation', function (Blueprint $table) {
            $table->unsignedTinyInteger('days')->nullable()->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wf_paid_vacation', function(Blueprint $table) {
            $table->dropColumn('days');
        });
    }
}
