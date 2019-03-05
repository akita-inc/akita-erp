<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_staffs', function (Blueprint $table) {
            $table->increments('id');
            $table->date('adhibition_start_dt');
            $table->date('adhibition_end_dt')->default('2999-12-31');
            $table->string('staff_cd',5);
            $table->string('password',50);
            $table->string('last_nm',25)->nullable();
            $table->string('first_nm',25)->nullable();
            $table->string('last_nm_kana',50)->nullable();
            $table->string('first_nm_kana',50)->nullable();
            $table->boolean('sex_id')->nullable();
            $table->date('birthday')->nullable();
            $table->date('enter_date')->nullable();
            $table->date('retire_date')->nullable();
            $table->string('zip_cd',7)->nullable();
            $table->char('prefectures_cd', 2)->nullable();
            $table->string('address1',20)->nullable();
            $table->string('address2',20)->nullable();
            $table->string('address3',50)->nullable();
            $table->string('landline_phone_number',20)->nullable();
            $table->string('cellular_phone_number',20)->nullable();
            $table->string('corp_cellular_phone_number',20)->nullable();
            $table->boolean('employment_pattern_id')->nullable();
            $table->string('belong_company_id',20)->nullable();
            $table->string('occupation_id',5)->nullable();
            $table->integer('mst_business_office_id')->nullable();
            $table->string('depertment_id',5)->nullable();
            $table->string('position_id',5)->nullable();
            $table->boolean('admin_fg')->nullable()->default(0);
            $table->date('driver_election_dt')->nullable();
            $table->string('medical_checkup_interval_id',5)->nullable();
            $table->boolean('enable_fg')->nullable()->default(1);
            $table->string('notes',50)->nullable();
            $table->string('drivers_license_number',12)->nullable();
            $table->boolean('drivers_license_color_id')->nullable();
            $table->date('drivers_license_issued_dt')->nullable();
            $table->date('drivers_license_period_validity')->nullable();
            $table->text('drivers_license_picture')->nullable();
            $table->integer('drivers_license_divisions_1')->nullable();
            $table->integer('drivers_license_divisions_2')->nullable();
            $table->integer('drivers_license_divisions_3')->nullable();
            $table->integer('drivers_license_divisions_4')->nullable();
            $table->integer('drivers_license_divisions_5')->nullable();
            $table->integer('drivers_license_divisions_6')->nullable();
            $table->integer('drivers_license_divisions_7')->nullable();
            $table->integer('drivers_license_divisions_8')->nullable();
            $table->integer('drivers_license_divisions_9')->nullable();
            $table->integer('drivers_license_divisions_10')->nullable();
            $table->integer('drivers_license_divisions_11')->nullable();
            $table->integer('drivers_license_divisions_12')->nullable();
            $table->integer('drivers_license_divisions_13')->nullable();
            $table->integer('drivers_license_divisions_14')->nullable();
            $table->string('educational_background',50)->nullable();
            $table->date('educational_background_dt')->nullable();
            $table->date('retire_dt')->nullable();
            $table->string('retire_reason')->nullable();
            $table->string('employment_insurance_numbers',20)->nullable();
            $table->string('health_insurance_numbers',20)->nullable();
            $table->string('employees_pension_insurance_numbers',10)->nullable();
            $table->integer('mst_role_id')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('modified_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();
            $table->index('staff_cd');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_staffs');
    }
}
