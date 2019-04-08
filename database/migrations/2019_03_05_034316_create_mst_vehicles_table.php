<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->date('adhibition_start_dt');
            $table->date('adhibition_end_dt')->default('2999-12-31');
            $table->integer('vehicles_cd')->nullable();
            $table->integer('door_number')->nullable();
            $table->integer('vehicles_kb')->nullable();
            $table->string('registration_numbers',50);
            $table->string('mst_business_office_id',5);
            $table->integer('vehicle_size_kb')->nullable();
            $table->integer('vehicle_purpose_id')->nullable();
            $table->integer('land_transport_office_cd')->nullable();
            $table->text('vehicle_inspection_sticker_pdf')->nullable();
            $table->date('registration_dt')->nullable();
            $table->string('first_year_registration_dt',6)->nullable();
            $table->integer('vehicle_classification_id')->nullable();
            $table->integer('private_commercial_id')->nullable();
            $table->integer('car_body_shape_id')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->integer('seating_capacity')->nullable();
            $table->integer('max_loading_capacity')->nullable();
            $table->integer('vehicle_body_weights')->nullable();
            $table->integer('vehicle_total_weights')->nullable();
            $table->string('frame_numbers',50)->nullable();
            $table->integer('vehicle_lengths')->nullable();
            $table->integer('vehicle_widths')->nullable();
            $table->integer('vehicle_heights')->nullable()->nullable();
            $table->integer('axle_loads_ff')->nullable();
            $table->integer('axle_loads_fr')->nullable();
            $table->integer('axle_loads_rf')->nullable();
            $table->integer('axle_loads_rr')->nullable();
            $table->string('vehicle_types',50)->nullable();
            $table->string('engine_typese',50)->nullable();
            $table->integer('total_displacements')->nullable();
            $table->integer('rated_outputs')->nullable();
            $table->integer('kinds_of_fuel_id')->nullable();
            $table->string('type_designation_numbers',50)->nullable();
            $table->string('id_segment_numbers',50)->nullable();
            $table->string('owner_nm',50)->nullable();
            $table->string('owner_address',200)->nullable();
            $table->string('user_nm',50)->nullable();
            $table->string('user_address',200)->nullable();
            $table->string('user_base_locations',200)->nullable();
            $table->date('expiry_dt')->nullable();
            $table->string('car_inspections_notes',500)->nullable();
            $table->integer('digital_tachograph_numbers')->nullable();
            $table->string('etc_numbers',19)->nullable();
            $table->string('drive_recorder_numbers',10)->nullable();
            $table->integer('bed_fg')->nullable();
            $table->integer('refrigerator_fg')->nullable();
            $table->integer('drive_system_id')->nullable();
            $table->integer('transmissions_id')->nullable();
            $table->string('transmissions_notes',50)->nullable();
            $table->integer('suspensions_cd')->nullable();
            $table->integer('tank_capacity_1')->nullable()->default(0);
            $table->integer('tank_capacity_2')->nullable()->default(0);
            $table->integer('loading_inside_dimension_capacity_length')->nullable();
            $table->integer('loading_inside_dimension_capacity_width')->nullable();
            $table->integer('loading_inside_dimension_capacity_height')->nullable();
            $table->boolean('snowmelt_fg')->nullable();
            $table->boolean('double_door_fg')->nullable();
            $table->boolean('floor_iron_plate_fg')->nullable();
            $table->boolean('floor_sagawa_embedded_fg')->nullable();
            $table->boolean('floor_roller_fg')->nullable();
            $table->boolean('floor_joloda_conveyor_fg')->nullable();
            $table->integer('power_gate_cd')->nullable();
            $table->date('vehicle_delivery_dt')->nullable();
            $table->text('specification_notes')->nullable();
            $table->string('mst_staff_cd', 5)->nullable();
            $table->decimal('personal_insurance_prices',10,3)->nullable();
            $table->decimal('property_damage_insurance_prices',10,3)->nullable();
            $table->decimal('vehicle_insurance_prices',10,3)->nullable();
            $table->text('picture_fronts')->nullable();
            $table->text('picture_rights')->nullable();
            $table->text('picture_lefts')->nullable();
            $table->text('picture_rears')->nullable();
            $table->decimal('acquisition_amounts', 10, 3)->nullable();
            $table->integer('acquisition_amortization')->nullable();
            $table->integer('durable_years')->nullable();
            $table->string('tire_sizes',10)->nullable();
            $table->string('battery_sizes',10)->nullable();
            $table->date('dispose_dt')->nullable();
            $table->string('notes',100)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('modified_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable();
            $table->tinyInteger('wireless_installation_fg')->nullable();
            $table->tinyInteger('enable_fg')->nullable()->default(1);
            $table->tinyInteger('fork_flg')->nullable();
            $table->index('door_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_vehicles');
    }
}
