<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstStaffDependentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_staff_dependents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mst_staff_id');
            $table->boolean('dependent_kb')->nullable();
            $table->string('last_nm',25)->nullable();
            $table->string('first_nm',25)->nullable();
            $table->string('last_nm_kana',50)->nullable();
            $table->string('first_nm_kana',50)->nullable();
            $table->date('birthday')->nullable();
            $table->boolean('sex_id')->nullable();
            $table->string('social_security_number', 10)->nullable();
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
        Schema::dropIfExists('mst_staff_dependents');
    }
}
