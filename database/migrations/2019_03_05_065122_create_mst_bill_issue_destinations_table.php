<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstBillIssueDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_bill_issue_destinations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mst_customer_id');
            $table->string('bill_zip_cd',7);
            $table->char('bill_address1',2)->nullable();
            $table->string('bill_address2',20)->nullable();
            $table->string('bill_address3',20)->nullable();
            $table->string('bill_address4',50)->nullable();
            $table->string('bill_phone_number',20)->nullable();
            $table->string('bill_fax_number',20)->nullable();
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
        Schema::dropIfExists('mst_bill_issue_destinations');
    }
}
