<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfApprovalStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wf_approval_status', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wf_type_id')->nullable();
            $table->integer('wf_id')->nullable();
            $table->tinyInteger('approval_steps')->nullable();
            $table->integer('approval_levels')->nullable();
            $table->string('title',30)->nullable();
            $table->string('approver_id',5)->nullable();
            $table->integer('approval_kb')->nullable();
            $table->integer('approval_fg')->nullable();
            $table->dateTime('approval_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wf_approval_status');
    }
}
