<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' =>  "Zeeshan Niaz",
            'email' => "info@example.com",
            'password' => bcrypt('11223344'),
            'photo' => "profile.png",
            'type' =>  'admin',
            'created_at' => now(),
        ]);
    }
}
