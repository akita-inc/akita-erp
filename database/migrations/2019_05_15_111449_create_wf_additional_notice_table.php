<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfAdditionalNoticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wf_additional_notice', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wf_type_id')->nullable();
            $table->integer('wf_id')->nullable();
            $table->string('staff_cd',5)->nullable();
            $table->string('email_address',300)->nullable();
            $table->timestamp('create_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wf_additional_notice');
    }
}
