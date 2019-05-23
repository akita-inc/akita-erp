<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfPaidVacationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wf_paid_vacation', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('regist_date')->nullable();
            $table->string('applicant_id',5)->nullable();
            $table->integer('applicant_office_id')->nullable();
            $table->integer('approval_kb')->nullable();
            $table->integer('half_day_kb')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('times')->nullable();
            $table->string('reasons',200)->nullable();
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
        Schema::dropIfExists('wf_paid_vacation');
    }
}
