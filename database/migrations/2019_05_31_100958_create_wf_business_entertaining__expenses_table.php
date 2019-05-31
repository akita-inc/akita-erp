<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfBusinessEntertainingExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wf_business_entertaining_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('regist_date')->nullable();
            $table->string('applicant_id',5)->nullable();
            $table->integer('applicant_office_id')->nullable();
            $table->integer('wf_business_entertaining_id')->nullable();
            $table->date('date')->nullable();
            $table->decimal('cost',8,0)->nullable();
            $table->string('client_company_name',200)->nullable();
            $table->string('client_members',200)->nullable();
            $table->tinyInteger('client_members_count')->nullable();
            $table->string('own_members',200)->nullable();
            $table->tinyInteger('own_members_count')->nullable();
            $table->string('place',200)->nullable();
            $table->string('report',400)->nullable();
            $table->decimal('deposit_amount',8,0)->nullable()->default(0);
            $table->integer('payoff_kb')->nullable();
            $table->decimal('payoff_amount',8,0)->nullable()->default(0);
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
        Schema::dropIfExists('wf_business_entertaining_expenses');
    }
}
