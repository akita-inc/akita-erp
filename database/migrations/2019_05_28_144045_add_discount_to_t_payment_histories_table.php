<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiscountToTPaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_payment_histories', function (Blueprint $table) {
            //
            $table->decimal('discount',12,2)->after('fee');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_payment_histories', function (Blueprint $table) {
            //
            $table->dropColumn('discount');
        });
    }
}
