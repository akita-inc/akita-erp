<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditAddColumnDeletedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // mst_calendars
        Schema::table("mst_calendars", function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });
        // mst_roles
        Schema::table("mst_roles", function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });
        // mst_role_auths
        Schema::table("mst_role_auths", function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });
        // mst_staff_auths
        Schema::table("mst_staff_auths", function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });
        // mst_screens
        Schema::table("mst_screens", function (Blueprint $table) {
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
        //
    }
}
