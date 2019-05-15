<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfBuyConsumablesDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wf_buy_consumables_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wf_buy_consumables_id')->nullable();
            $table->string('item_name',100)->nullable();
            $table->decimal('unit_price',8,0)->nullable();
            $table->integer('qty')->nullable();
            $table->decimal('sub_total',8,0)->nullable();
            $table->string('retailer_name',50)->nullable();
            $table->string('reasons',100)->nullable();
            $table->decimal('askul_price',8,0)->nullable();
            $table->timestamp('delete_at')->nullable();
            $table->timestamp('create_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('modified_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wf_buy_consumables_detail');
    }
}
