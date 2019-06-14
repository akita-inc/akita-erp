<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTPaymentHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_payment_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('dw_number');
            $table->bigInteger('branch_number');
            $table->string('mst_customers_cd',5);
            $table->date('dw_day');
            $table->integer('dw_classification');
            $table->decimal('total_dw_amount',10,2);
            $table->decimal('actual_dw',10,2);
            $table->decimal('fee',10,2);
            $table->bigInteger('invoice_number');
            $table->string('note',200)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('add_mst_staff_id');
            $table->timestamp('modified_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('upd_mst_staff_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_payment_histories');
    }
}
