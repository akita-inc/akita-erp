<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->date('adhibition_start_dt');
            $table->date('adhibition_end_dt')->default('2999-12-31');
            $table->string('mst_customers_cd',5);
            $table->string('customer_nm',200)->nullable();
            $table->string('customer_nm_kana',200)->nullable();
            $table->string('customer_nm_formal',200)->nullable();
            $table->string('customer_nm_kana_formal',200)->nullable();
            $table->string('person_in_charge_last_nm',25)->nullable();
            $table->string('person_in_charge_first_nm',25)->nullable();
            $table->string('person_in_charge_last_nm_kana',50)->nullable();
            $table->string('person_in_charge_first_nm_kana',50)->nullable();
            $table->string('zip_cd',7)->nullable();
            $table->char('prefectures_cd',2)->nullable();
            $table->string('address1',20)->nullable();
            $table->string('address2',20)->nullable();
            $table->string('address3',50)->nullable();
            $table->string('phone_number',20)->nullable();
            $table->string('fax_number',20)->nullable();
            $table->text('hp_url')->nullable();
            $table->integer('customer_category_id')->nullable();
            $table->integer('prime_business_office_id')->nullable();
            $table->string('explanations_bill',100)->nullable();
            $table->integer('bundle_dt')->nullable();
            $table->integer('deposit_month_id')->nullable();
            $table->integer('deposit_day')->nullable();
            $table->integer('deposit_method_id')->nullable();
            $table->string('deposit_method_notes',200)->nullable();
            $table->date('business_start_dt')->nullable();
            $table->integer('consumption_tax_calc_unit_id')->nullable()->default(0);
            $table->integer('rounding_method_id')->nullable();
            $table->decimal('discount_rate',3,0)->nullable();
            $table->decimal('discount_amount',9,3)->nullable();
            $table->integer('except_g_drive_bill_fg')->nullable()->default(1);
            $table->string('deposit_bank_cd',4)->nullable();
            $table->boolean('enable_fg')->nullable()->default(1);
            $table->integer('mst_account_titles_id')->nullable();
            $table->integer('mst_account_titles_id_2')->nullable();
            $table->integer('mst_account_titles_id_3')->nullable();
            $table->string('notes',50)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('modified_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('mst_customers');
    }
}
