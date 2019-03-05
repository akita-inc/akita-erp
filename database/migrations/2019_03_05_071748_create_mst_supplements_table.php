<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstSupplementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_supplements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_title_code',3);
            $table->string('supplement_code',5);
            $table->string('supplement_name',30)->nullable();
            $table->string('supplement_formal_nm',50)->nullable();
            $table->char('debit_tax_division',2)->nullable();
            $table->char('credit_tax_division',2)->nullable();
            $table->string('tax_rounding',10)->nullable();
            $table->string('zip_cd',7)->nullable();
            $table->char('prefectures_cd', 2)->nullable();
            $table->string('address1',20)->nullable();
            $table->string('address2',20)->nullable();
            $table->string('address3',50)->nullable();
            $table->string('phone_number',20)->nullable();
            $table->string('fax_number',20)->nullable();
            $table->string('payee',30)->nullable();
            $table->string('payday',2)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('modified_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();
            $table->integer('disp_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_supplements');
    }
}
