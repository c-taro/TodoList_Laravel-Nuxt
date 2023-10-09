<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Subscription;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetBoardsSuccess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board1 = Board::create([
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $board2 = Board::create([
            'title' => 'やることリスト',
            'subscription_id' => $subscription->id,
        ]);
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $getTasksResponse = $this->get(route('user.getBoards'));
        $getTasksResponse
            ->assertOk()
            ->assertJson([
                [
                    'id' => $board1->id,
                    'title' => '買い物リスト',
                    'subscription_id' => $subscription->id,
                ],
                [
                    'id' => $board2->id,
                    'title' => 'やることリスト',
                    'subscription_id' => $subscription->id,
                ]
            ]);
    }

    public function testCreateBoardSuccess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $createBoardResponse = $this->post(route('user.createBoard'), [
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $createBoardResponse
            ->assertCreated()
            ->assertJson([
                'title' => '買い物リスト',
                'subscription_id' => $subscription->id,
            ]);
    }

    public function testCreateBoardAuthorizationException()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $this->assertGuest();

        $createBoardResponse = $this->post(route('user.createBoard'), [
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $createBoardResponse
            ->assertStatus(500)
            ->assertJson([
                'title' => 'Internal Server Error',
                'detail' => 'Attempt to read property "subscription" on null',
            ])
            ->assertJsonMissing([
                'title' => '買い物リスト',
                'subscription_id' => $subscription->id,
            ]);
    }
}