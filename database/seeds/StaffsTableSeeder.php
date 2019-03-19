<?php

use Illuminate\Database\Seeder;

class StaffsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('mst_staffs')->insert([
            'adhibition_start_dt'=>new \DateTime(),
            'adhibition_end_dt'=>new \DateTime(),
            'staff_cd'=>'toan1',
            'last_nm'=>'鈴木',
            'first_nm'=>'太郎',
            'password' => bcrypt('123456'),
        ]);
    }
}
