<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTJiconaxSalesDatas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_jiconax_sales_datas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('daily_report_date');
            $table->integer('branch_office_cd');
            $table->integer('document_no');
            $table->integer('operating_days');
            $table->integer('transport_cd');
            $table->integer('vehicle_cd');
            $table->string('point_of_departure',50)->nullable();
            $table->string('destination',50)->nullable();
            $table->integer('staff_cd');
            $table->integer('mst_customers_cd');
            $table->string('goods',50)->nullable();
            $table->double('quantity');
            $table->decimal('unit_price',10,2);
            $table->decimal('transport_billing_amount',10,2);
            $table->decimal('insurance_fee',10,2);
            $table->decimal('billing_fast_charge',10,2);
            $table->decimal('discount_amount',10,2);
            $table->decimal('billing_amount',10,2);
            $table->decimal('consumption_tax',10,2);
            $table->decimal('total_fee',10,2);
            $table->integer('departure_point');
            $table->string('departure_point_name',50)->nullable();
            $table->integer('landing');
            $table->string('landing_name',50)->nullable();
            $table->string('delivery_destination',50)->nullable();
            $table->decimal('payment',10,2);
            $table->decimal('subcontract_amount',10,2);
            $table->integer('summary_indicator');
            $table->integer('actual_days');
            $table->integer('mileage');
            $table->integer('actual_distance');
            $table->double('refueling_fuel');
            $table->decimal('fast_charge',10,2);
            $table->integer('shipping_quantity');
            $table->integer('loading_rate');
            $table->integer('round_trip_classification');
            $table->integer('tax_classification');
            $table->integer('discount_classification');
            $table->string('opening_time',5)->nullable();
            $table->string('closing_time',5)->nullable();
            $table->string('restraint_time',5)->nullable();
            $table->string('lunch_break_time',5)->nullable();
            $table->string('night_break_time',5)->nullable();
            $table->string('midnight_time',5)->nullable();
            $table->string('working_time',5)->nullable();
            $table->string('lunch_break_time2',5)->nullable();
            $table->string('night_break_time2',5)->nullable();
            $table->string('predetermined_time',5)->nullable();
            $table->string('off_hours',5)->nullable();
            $table->string('custom_object_id',50)->nullable();
            $table->string('last_updated_user',50)->nullable();
            $table->string('last_updated',50)->nullable();
            $table->decimal('loading_fee',10,2);
            $table->decimal('wholesale_fee',10,2);
            $table->decimal('waiting_fee',10,2);
            $table->decimal('incidental_fee',10,2);
            $table->decimal('surcharge_fee',10,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_jiconax_sales_datas');
    }
}
