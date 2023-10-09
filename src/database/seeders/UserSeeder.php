<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'name',
            'email' => 'p@p.pp',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('pppppppp'),
        ]);
        User::create([
            'name' => 'name',
            'email' => 'o@o.oo',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('oooooooo'),
        ]);
    }
}