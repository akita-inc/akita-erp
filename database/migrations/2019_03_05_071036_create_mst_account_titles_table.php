<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstAccountTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_account_titles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_title_code',3);
            $table->string('account_title_name',30)->nullable();
            $table->string('account_title_formal_nm',50)->nullable();
            $table->string('show_division',10)->nullable();
            $table->char('credit_debit_division_id',1)->nullable();
            $table->char('debit_tax_division',2)->nullable();
            $table->char('credit_tax_division',2)->nullable();
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
        Schema::dropIfExists('mst_account_titles');
    }
}
