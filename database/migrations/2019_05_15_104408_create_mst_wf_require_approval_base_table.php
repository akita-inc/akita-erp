<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstWfRequireApprovalBaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_wf_require_approval_base', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wf_type')->nullable();
            $table->tinyInteger('approval_steps')->nullable();
            $table->integer('approval_levels')->nullable();
            $table->integer('approval_kb')->nullable();
            $table->timestamp('delete_at')->nullable();
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
        Schema::dropIfExists('mst_wf_require_approval_base');
    }
}
