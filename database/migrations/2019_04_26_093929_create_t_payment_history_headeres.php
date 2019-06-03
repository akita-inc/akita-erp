<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTPaymentHistoryHeaderes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_payment_history_headeres', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('invoice_number');
            $table->string('mst_suppliers_cd',5);
            $table->integer('mst_business_office_id');
            $table->date('publication_date');
            $table->decimal('total_fee',12,2);
            $table->decimal('consumption_tax',12,2);
            $table->decimal('tax_included_amount',12,2);
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
        Schema::dropIfExists('t_payment_history_headeres');
    }
}
