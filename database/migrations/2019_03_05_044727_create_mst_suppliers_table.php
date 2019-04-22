<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->date('adhibition_start_dt');
            $table->date('adhibition_end_dt')->default('2999-12-31');
            $table->string('mst_suppliers_cd',5);
            $table->string('supplier_nm',200)->nullable();
            $table->string('supplier_nm_kana',200)->nullable();
            $table->string('supplier_nm_formal',200)->nullable();
            $table->string('supplier_nm_kana_formal',200)->nullable();
            $table->string('dealing_person_in_charge_last_nm',25)->nullable();
            $table->string('dealing_person_in_charge_first_nm',25)->nullable();
            $table->string('dealing_person_in_charge_last_nm_kana',50)->nullable();
            $table->string('dealing_person_in_charge_first_nm_kana',50)->nullable();
            $table->string('accounting_person_in_charge_last_nm',25)->nullable();
            $table->string('accounting_person_in_charge_first_nm',25)->nullable();
            $table->string('accounting_person_in_charge_last_nm_kana',50)->nullable();
            $table->string('accounting_person_in_charge_first_nm_kana',50)->nullable();
            $table->string('zip_cd',7)->nullable();
            $table->char('prefectures_cd',2)->nullable();
            $table->string('address1',20)->nullable();
            $table->string('address2',20)->nullable();
            $table->string('address3',50)->nullable();
            $table->string('phone_number',20)->nullable();
            $table->string('fax_number',20)->nullable();
            $table->string('hp_url',2500)->nullable();
            $table->integer('bundle_dt')->nullable();
            $table->integer('payday')->nullable();
            $table->integer('payment_month_id')->nullable();
            $table->integer('payment_day')->nullable();
            $table->integer('payment_method_id')->nullable();
            $table->string('explanations_bill',100)->nullable();
            $table->date('business_start_dt')->nullable();
            $table->integer('consumption_tax_calc_unit_id')->nullable();
            $table->integer('rounding_method_id')->nullable();
            $table->string('payment_bank_cd',4)->nullable();
            $table->string('payment_bank_name',30)->nullable();
            $table->string('payment_branch_cd',4)->nullable();
            $table->string('payment_branch_name',30)->nullable();
            $table->string('payment_account_type',2)->nullable();
            $table->string('payment_account_number',10)->nullable();
            $table->string('payment_account_holder',30)->nullable();
            $table->string('notes',50)->nullable();
            $table->boolean('enable_fg')->nullable()->default(1);
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
        Schema::dropIfExists('mst_suppliers');
    }
}
