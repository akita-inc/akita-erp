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
            'created_at'=>new \DateTime(),
            'modified_at'=>new \DateTime(),
            'staff_cd'=>'91919',
            'last_nm'=>'鈴木',
            'first_nm'=>'太郎',
            'password' => bcrypt('123456'),
        ]);
    }
}
