<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmptyInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empty_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status')->nullable();
            $table->string('regist_staff',5)->nullable();
            $table->integer('regist_office_id')->nullable();
            $table->string('email_address',300)->nullable();
            $table->integer('vehicle_kb')->nullable();
            $table->string('registration_numbers',50)->nullable();
            $table->string('vehicle_size',50)->nullable();
            $table->string('vehicle_body_shape',50)->nullable();
            $table->integer('max_load_capacity')->nullable();
            $table->string('equipment',200)->nullable();
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->char('start_pref_cd',2)->nullable();
            $table->string('start_address',200)->nullable();
            $table->decimal('asking_price', 10, 0)->nullable();
            $table->integer('asking_baggage')->nullable();
            $table->char('arrive_pref_cd',2)->nullable();
            $table->string('arrive_address',50)->nullable();
            $table->date('arrive_date')->nullable();
            $table->dateTime('ask_date')->nullable();
            $table->integer('ask_office')->nullable();
            $table->string('ask_staff',5)->nullable();
            $table->string('ask_staff_email_address',300)->nullable();
            $table->dateTime('apr_date')->nullable();
            $table->string('apr_staff',5)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('empty_info');
    }
}
