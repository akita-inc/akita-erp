<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSendBackReasonToWfApprovalStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wf_approval_status', function (Blueprint $table) {
            $table->string('send_back_reason',200)->nullable()->after('approval_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wf_approval_status', function (Blueprint $table) {
            //
            $table->dropColumn('send_back_reason');

        });
    }
}
