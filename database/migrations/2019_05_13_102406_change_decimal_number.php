<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDecimalNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //t_saleses
        Schema::table('t_saleses', function (Blueprint $table) {
            $table->decimal('insurance_fee',12,2)->nullable()->change();
            $table->decimal('billing_fast_charge',12,2)->nullable()->change();
            $table->decimal('loading_fee',12,2)->nullable()->change();
            $table->decimal('wholesale_fee',12,2)->nullable()->change();
            $table->decimal('waiting_fee',12,2)->nullable()->change();
            $table->decimal('incidental_fee',12,2)->nullable()->change();
            $table->decimal('surcharge_fee',12,2)->nullable()->change();
            $table->decimal('unit_price',12,2)->nullable()->change();
            $table->decimal('discount_amount',12,2)->nullable()->change();
            $table->decimal('total_fee',12,2)->nullable()->change();
            $table->decimal('consumption_tax',12,2)->nullable()->change();
            $table->decimal('tax_included_amount',12,2)->nullable()->change();
            $table->decimal('payment',12,2)->nullable()->change();
        });

        //t_billing_history_headers
        Schema::table('t_billing_history_headers', function (Blueprint $table) {
            $table->decimal('total_fee',12,2)->change();
            $table->decimal('consumption_tax',12,2)->change();
            $table->decimal('tax_included_amount',12,2)->change();
        });

        //t_billing_history_header_details
        Schema::table('t_billing_history_header_details', function (Blueprint $table) {
            $table->decimal('insurance_fee',12,2)->nullable()->change();
            $table->decimal('billing_fast_charge',12,2)->nullable()->change();
            $table->decimal('loading_fee',12,2)->nullable()->change();
            $table->decimal('wholesale_fee',12,2)->nullable()->change();
            $table->decimal('waiting_fee',12,2)->nullable()->change();
            $table->decimal('incidental_fee',12,2)->nullable()->change();
            $table->decimal('surcharge_fee',12,2)->nullable()->change();
            $table->decimal('unit_price',12,2)->nullable()->change();
            $table->decimal('discount_amount',12,2)->nullable()->change();
            $table->decimal('total_fee',12,2)->change();
            $table->decimal('consumption_tax',12,2)->change();
            $table->decimal('tax_included_amount',12,2)->change();
            $table->decimal('payment',12,2)->nullable()->change();
        });

        //t_payment_histories
        Schema::table('t_payment_histories', function (Blueprint $table) {
            $table->decimal('total_dw_amount',12,2)->change();
            $table->decimal('actual_dw',12,2)->change();
            $table->decimal('fee',12,2)->change();
        });

        //t_purchases
        Schema::table('t_purchases', function (Blueprint $table) {
            $table->decimal('insurance_fee',12,2)->nullable()->change();
            $table->decimal('billing_fast_charge',12,2)->nullable()->change();
            $table->decimal('loading_fee',12,2)->nullable()->change();
            $table->decimal('wholesale_fee',12,2)->nullable()->change();
            $table->decimal('waiting_fee',12,2)->nullable()->change();
            $table->decimal('incidental_fee',12,2)->nullable()->change();
            $table->decimal('surcharge_fee',12,2)->nullable()->change();
            $table->decimal('unit_price',12,2)->nullable()->change();
            $table->decimal('discount_amount',12,2)->nullable()->change();
            $table->decimal('total_fee',12,2)->nullable()->change();
            $table->decimal('consumption_tax',12,2)->nullable()->change();
            $table->decimal('tax_included_amount',12,2)->nullable()->change();
            $table->decimal('payment',12,2)->nullable()->change();
        });

        // t_payment_history_headeres
        Schema::table('t_payment_history_headeres', function (Blueprint $table) {
            $table->decimal('total_fee',12,2)->change();
            $table->decimal('consumption_tax',12,2)->change();
            $table->decimal('tax_included_amount',12,2)->change();
        });

        //t_payment_history_header_details
        Schema::table('t_payment_history_header_details', function (Blueprint $table) {
            $table->decimal('insurance_fee',12,2)->nullable()->change();
            $table->decimal('billing_fast_charge',12,2)->nullable()->change();
            $table->decimal('loading_fee',12,2)->nullable()->change();
            $table->decimal('wholesale_fee',12,2)->nullable()->change();
            $table->decimal('waiting_fee',12,2)->nullable()->change();
            $table->decimal('incidental_fee',12,2)->nullable()->change();
            $table->decimal('surcharge_fee',12,2)->nullable()->change();
            $table->decimal('unit_price',12,2)->nullable()->change();
            $table->decimal('discount_amount',12,2)->nullable()->change();
            $table->decimal('total_fee',12,2)->nullable()->change();
            $table->decimal('consumption_tax',12,2)->nullable()->change();
            $table->decimal('tax_included_amount',12,2)->nullable()->change();
            $table->decimal('payment',12,2)->nullable()->change();
        });

        //t_withdrawal_histories
        Schema::table('t_withdrawal_histories', function (Blueprint $table) {
            $table->decimal('total_dw_amount',12,2)->change();
            $table->decimal('actual_dw',12,2)->change();
            $table->decimal('fee',12,2)->change();
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
