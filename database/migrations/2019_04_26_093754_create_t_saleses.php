<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTSaleses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_saleses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('document_no');
            $table->string('mst_customers_cd',5);
            $table->integer('branch_office_cd');
            $table->integer('mst_business_office_id');
            $table->string('last_updated',50)->nullable();
            $table->string('last_updated_user',50)->nullable();
            $table->date('daily_report_date');
            $table->integer('transport_cd');
            $table->integer('staff_cd');
            $table->integer('vehicles_cd')->nullable();
            $table->string('goods',50)->nullable();
            $table->string('departure_point_name',50)->nullable();
            $table->string('landing_name',50)->nullable();
            $table->string('delivery_destination',50)->nullable();
            $table->tinyInteger('invoicing_flag')->nullable();
            $table->decimal('insurance_fee',10,2)->nullable();
            $table->decimal('billing_fast_charge',10,2)->nullable();
            $table->decimal('loading_fee',10,2)->nullable();
            $table->decimal('wholesale_fee',10,2)->nullable();
            $table->decimal('waiting_fee',10,2)->nullable();
            $table->decimal('incidental_fee',10,2)->nullable();
            $table->decimal('surcharge_fee',10,2)->nullable();
            $table->double('quantity')->nullable();
            $table->decimal('unit_price',10,2)->nullable();
            $table->decimal('discount_amount',10,2)->nullable();
            $table->integer('summary_indicator')->nullable();
            $table->char('tax_classification_flg',1)->nullable();
            $table->decimal('total_fee',10,2)->nullable();
            $table->decimal('consumption_tax',10,2)->nullable();
            $table->decimal('tax_included_amount',10,2)->nullable();
            $table->decimal('payment',10,2)->nullable();
            $table->string('account_title_code',3)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('add_mst_staff_id')->nullable();
            $table->timestamp('modified_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('upd_mst_staff_id')->nullable();
            $table->index('document_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_saleses');
    }
}
