<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModifyLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modify_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('table_name', 255);
            $table->bigInteger('table_id');
            $table->string('column_name', 255);
            $table->string('before_data', 10000)->nullable();
            $table->string('after_data', 10000)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('mst_staff_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modify_logs');
    }
}
