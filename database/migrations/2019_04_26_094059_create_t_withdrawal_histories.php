<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTWithdrawalHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_withdrawal_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('dw_number');
            $table->bigInteger('branch_number');
            $table->string('mst_suppliers_cd',5);
            $table->date('dw_day');
            $table->char('dw_classification',1);
            $table->decimal('total_dw_amount',12,2);
            $table->decimal('actual_dw',12,2);
            $table->decimal('fee',12,2);
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
        Schema::dropIfExists('t_withdrawal_histories');
    }
}
