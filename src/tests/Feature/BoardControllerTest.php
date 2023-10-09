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

class BoardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShowSuccess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::create([
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $showResponse = $this->get(route('board.show', ['board' => $board->id]));
        $showResponse
            ->assertOk()
            ->assertJson([
                'title' => '買い物リスト',
                'subscription_id' => $subscription->id,
            ]);
    }

    public function testShowAuthorizationException()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::create([
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $this->assertGuest();

        $showResponse = $this->get(route('board.show', ['board' => $board->id]));
        $showResponse
            ->assertForbidden()
            ->assertJson([
                'title' => 'Forbidden',
                'detail' => 'this_action_is_unauthorized',
            ])
            ->assertJsonMissing([
                'title' => '買い物リスト',
                'subscription_id' => $subscription->id,
            ]);
    }

    public function testUpdateSuccess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::create([
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $updateResponse = $this->put(route('board.update', ['board' => $board->id]), [
            'title' => 'やることリスト',
            'subscription_id' => $subscription->id,
        ]);
        $updateResponse
            ->assertOk()
            ->assertJson([
                'title' => 'やることリスト',
                'subscription_id' => $subscription->id,
            ]);
    }

    public function testUpdateWithSpecialChars()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::create([
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $updateResponse = $this->put(route('board.update', ['board' => $board->id]), [
            'title' => 'P<div>aaa</div>',
            'subscription_id' => $subscription->id,
        ]);
        $updateResponse
            ->assertOk()
            ->assertJson([
                'title' => 'P&lt;div&gt;aaa&lt;/div&gt;',
                'subscription_id' => $subscription->id,
            ]);
    }

    public function testUpdateAuthorizationException()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::create([
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $this->assertGuest();

        $updateResponse = $this->put(route('board.update', ['board' => $board->id]), [
            'title' => 'やることリスト',
            'subscription_id' => $subscription->id,
        ]);
        $updateResponse
            ->assertForbidden()
            ->assertJson([
                'title' => 'Forbidden',
                'detail' => 'this_action_is_unauthorized',
            ])
            ->assertJsonMissing([
                'title' => 'やることリスト',
                'subscription_id' => $subscription->id,
            ]);
    }

    public function testDestroySucess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::create([
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $destroyResponse = $this->delete(route('board.destroy', ['board' => $board->id]));
        $destroyResponse
            ->assertOk()
            ->assertJson([
                'title' => '買い物リスト',
                'subscription_id' => $subscription->id,
            ]);
        $this->assertNull(Board::find($board->id), 'The board is not deleted');
        $deletedBoard = Board::withTrashed()->find($board->id);
        $this->assertNotNull($deletedBoard->deleted_at, 'The board is not deleted');
    }

    public function testDestroyAuthorizationException()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::create([
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);

        $this->assertGuest();
        $destroyResponse = $this->delete(route('board.destroy', ['board' => $board->id]));
        $destroyResponse
            ->assertForbidden()
            ->assertJson([
                'title' => 'Forbidden',
                'detail' => 'this_action_is_unauthorized',
            ])
            ->assertJsonMissing([
                'title' => '買い物リスト',
                'subscription_id' => $subscription->id,
            ]);
        $notdeletedBoard = Board::find($board->id);
        $this->assertNull($notdeletedBoard->deleted_at, 'The board is deleted');
    }

    public function testGetTasksSuccess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::create([
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $task1 = Task::create([
            'title' => '新製品の宣伝',
            'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
            'person_in_charge' => '山田 由美',
            'board_id' => $board->id,
        ]);
        $task2 = Task::create([
            'title' => 'Shopping List',
            'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
            'person_in_charge' => 'John Smith',
            'board_id' => $board->id,
        ]);
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $getTasksResponse = $this->get(route('board.getTasks', ['board' => $board->id]));
        $getTasksResponse
            ->assertOk()
            ->assertJson([
                [
                    'id' => $task1->id,
                    'title' => '新製品の宣伝',
                    'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
                    'person_in_charge' => '山田 由美',
                    'board_id' => $board->id,
                ],
                [
                    'id' => $task2->id,
                    'title' => 'Shopping List',
                    'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
                    'person_in_charge' => 'John Smith',
                    'board_id' => $board->id,
                ]
            ]);
    }

    public function testGetTasksAuthorizationException()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::create([
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $task1 = Task::create([
            'title' => '新製品の宣伝',
            'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
            'person_in_charge' => '山田 由美',
            'board_id' => $board->id,
        ]);
        $task2 = Task::create([
            'title' => 'Shopping List',
            'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
            'person_in_charge' => 'John Smith',
            'board_id' => $board->id,
        ]);
        $this->assertGuest();

        $getTasksResponse = $this->get(route('board.getTasks', ['board' => $board->id]));
        $getTasksResponse
            ->assertForbidden()
            ->assertJson([
                'title' => 'Forbidden',
                'detail' => 'this_action_is_unauthorized',
            ])
            ->assertJsonMissing([
                [
                    'id' => $task1->id,
                    'title' => '新製品の宣伝',
                    'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
                    'person_in_charge' => '山田 由美',
                    'board_id' => $board->id,
                ],
                [
                    'id' => $task2->id,
                    'title' => 'Shopping List',
                    'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
                    'person_in_charge' => 'John Smith',
                    'board_id' => $board->id,
                ]
            ]);
    }

    public function testCreateTaskSuccess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::create([
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $createTaskResponse = $this->post(route('board.createTask', ['board' => $board->id]), [
            'title' => 'Shopping List',
            'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
            'person_in_charge' => 'John Smith',
            'board_id' => $board->id,
        ]);
        $createTaskResponse
            ->assertCreated()
            ->assertJson([
                'title' => 'Shopping List',
                'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
                'person_in_charge' => 'John Smith',
                'board_id' => $board->id,
            ]);
    }

    public function testCreateTaskAuthorizationException()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::create([
            'title' => '買い物リスト',
            'subscription_id' => $subscription->id,
        ]);
        $this->assertGuest();

        $createTaskResponse = $this->post(route('board.createTask', ['board' => $board->id]), [
            'title' => 'Shopping List',
            'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
            'person_in_charge' => 'John Smith',
            'board_id' => $board->id,
        ]);
        $createTaskResponse
            ->assertForbidden()
            ->assertJson([
                'title' => 'Forbidden',
                'detail' => 'this_action_is_unauthorized',
            ])
            ->assertJsonMissing([
                'title' => 'Shopping List',
                'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
                'person_in_charge' => 'John Smith',
                'board_id' => $board->id,
            ]);
    }

    public function testGetTrashedTasksSuccess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task1 = Task::create([
            'title' => '新製品の宣伝',
            'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
            'person_in_charge' => '山田 由美',
            'board_id' => $board->id,
        ]);
        $task2 = Task::create([
            'title' => 'Shopping List',
            'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
            'person_in_charge' => 'John Smith',
            'board_id' => $board->id,
        ]);
        $task1->delete();
        $task2->delete();
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $getTasksResponse = $this->get(route('board.getTrashedTasks', ['board' => $board->id]));
        $getTasksResponse
            ->assertOk()
            ->assertJson([
                [
                    'id' => $task1->id,
                    'title' => '新製品の宣伝',
                    'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
                    'person_in_charge' => '山田 由美',
                    'board_id' => $board->id,
                ],
                [
                    'id' => $task2->id,
                    'title' => 'Shopping List',
                    'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
                    'person_in_charge' => 'John Smith',
                    'board_id' => $board->id,
                ]
            ]);
    }

    public function testGetTrashedTasksAuthorizationException()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task1 = Task::create([
            'title' => '新製品の宣伝',
            'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
            'person_in_charge' => '山田 由美',
            'board_id' => $board->id,
        ]);
        $task2 = Task::create([
            'title' => 'Shopping List',
            'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
            'person_in_charge' => 'John Smith',
            'board_id' => $board->id,
        ]);
        $task1->delete();
        $task2->delete();
        $this->assertGuest();

        $getTrashedTasksResponse = $this->get(route('board.getTrashedTasks', ['board' => $board->id]));
        $getTrashedTasksResponse
            ->assertForbidden()
            ->assertJson([
                'title' => 'Forbidden',
                'detail' => 'this_action_is_unauthorized',
            ])
            ->assertJsonMissing([
                [
                    'id' => $task1->id,
                    'title' => '新製品の宣伝',
                    'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
                    'person_in_charge' => '山田 由美',
                    'board_id' => $board->id,
                ],
                [
                    'id' => $task2->id,
                    'title' => 'Shopping List',
                    'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
                    'person_in_charge' => 'John Smith',
                    'board_id' => $board->id,
                ]
            ]);
    }
}