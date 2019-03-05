<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstStaffQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_staff_qualifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mst_staff_id');
            $table->string('qualification_kind_id',5)->nullable();
            $table->date('acquisition_dt')->nullable();
            $table->date('period_validity_start_dt')->nullable();
            $table->date('period_validity_end_dt')->nullable();
            $table->string('notes',100)->nullable();
            $table->integer('amounts')->nullable();
            $table->date('payday')->nullable();
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
        Schema::dropIfExists('mst_staff_qualifications');
    }
}
