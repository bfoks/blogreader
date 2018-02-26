<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        factory(\App\User::class)->create([
            'email' => 'uiq3.eu@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('12345a'),
        ]);

    }
}
