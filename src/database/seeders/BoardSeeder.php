<?php

namespace Database\Seeders;

use App\Models\Board;
use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Board::create(['title' => '開発タスク', 'subscription_id' => 1]);
        Board::create(['title' => '事務タスク', 'subscription_id' => 1]);
        Board::create(['title' => 'その他タスク', 'subscription_id' => 1]);
        Board::create(['title' => 'test1', 'subscription_id' => 2]);
        Board::create(['title' => 'test2', 'subscription_id' => 2]);
    }
}
