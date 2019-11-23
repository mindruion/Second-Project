<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->truncate(); // truncate user table each time of seeders run
        User::create([ // create a new user
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'name' => 'Administrator']);

    }
}
