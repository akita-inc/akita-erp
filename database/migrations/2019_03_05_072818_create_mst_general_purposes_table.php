<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstGeneralPurposesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_general_purposes', function (Blueprint $table) {
            $table->increments('id');
            $table->char('data_kb',5);
            $table->string('data_kb_nm',100);
            $table->integer('date_id');
            $table->string('date_nm',100);
            $table->string('date_nm_kana',200);
            $table->string('date_nm_short',10)->nullable();
            $table->tinyInteger('disp_fg')->nullable();
            $table->integer('disp_number')->nullable();
            $table->string('contents1',100)->nullable();
            $table->string('contents2',100)->nullable();
            $table->string('contents3',100)->nullable();
            $table->decimal('amount',10,2)->nullable();
            $table->decimal('amount_start',10,2)->nullable();
            $table->decimal('amount_end',10,2)->nullable();
            $table->date('dt')->nullable();
            $table->date('dt_start')->nullable();
            $table->date('dt_end')->nullable();
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
        Schema::dropIfExists('mst_general_purposes');
    }
}
