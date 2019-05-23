<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstBusinessOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_business_offices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mst_business_office_cd');
            $table->string('business_office_nm',50);
            $table->string('business_office_nm_kana',100)->nullable();
            $table->integer('branch_office_cd')->nullable();
            $table->string('zip_cd',7)->nullable();
            $table->char('prefectures_cd', 2)->nullable();
            $table->string('address1',20)->nullable();
            $table->string('address2',20)->nullable();
            $table->string('address3',50)->nullable();
            $table->string('phone_number',20)->nullable();
            $table->string('fax_number',20)->nullable();
            $table->string('ip_phone_number',20)->nullable();
            $table->string('email_address',300)->nullable();
            $table->tinyInteger('green_m_fg')->nullable();
            $table->tinyInteger('gmark_fg')->nullable();
            $table->date('gmark_limit')->nullable();
            $table->integer('erea_id')->nullable();
            $table->tinyInteger('warehouse_fg')->nullable();
            $table->date('opening_dt')->nullable();
            $table->date('closing_dt')->nullable();
            $table->integer('disp_number')->nullable();
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
        Schema::dropIfExists('mst_business_offices');
    }
}
