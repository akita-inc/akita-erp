<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTableTJiconaxSalesDatas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_jiconax_sales_datas', function (Blueprint $table) {
            //
            $table->decimal('unit_price',12,2)->change();
            $table->decimal('transport_billing_amount',12,2)->change();
            $table->decimal('insurance_fee',12,2)->change();
            $table->decimal('billing_fast_charge',12,2)->change();
            $table->decimal('discount_amount',12,2)->change();
            $table->decimal('billing_amount',12,2)->change();
            $table->decimal('consumption_tax',12,2)->change();
            $table->decimal('total_fee',12,2)->change();
            $table->decimal('payment',12,2)->change();
            $table->decimal('subcontract_amount',12,2)->change();
            $table->decimal('fast_charge',12,2)->change();
            $table->decimal('loading_fee',12,2)->change();
            $table->decimal('wholesale_fee',12,2)->change();
            $table->decimal('waiting_fee',12,2)->change();
            $table->decimal('incidental_fee',12,2)->change();
            $table->decimal('surcharge_fee',12,2)->change();
            $table->timestamp('deleted_at')->nullable();
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
