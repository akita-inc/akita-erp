<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWfApprovalHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wf_approval_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wf_type_id')->nullable();
            $table->integer('wf_id')->nullable();
            $table->tinyInteger('approval_steps')->nullable();
            $table->integer('approval_levels')->nullable();
            $table->integer('predata')->nullable();
            $table->integer('postdata')->nullable();
            $table->integer('approver_id')->nullable();
            $table->string('send_back_reason',200)->nullable();
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
        Schema::dropIfExists('wf_approval_history');
    }
}
