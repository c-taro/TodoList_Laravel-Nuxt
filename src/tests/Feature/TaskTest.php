<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Subscription;
use App\Models\Task;
use App\Models\User;
use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;
    // 【Laravel】PHPUnitの実行でDBを汚染しないためにできる簡単なこと https://qiita.com/mitashun/items/8b200c0b8c7080471ff6
    // RefreshDatabaseはテーブル内のデータを初期化してくれるけど、IDのオートインクリメントはリセットされない。 超初心者がLaravelテストでつまずいた点を書いてみた https://qiita.com/tadouma/items/6094d29aa6a75ce42b9b

    public function testShowSuccess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task = Task::create([
            'title' => '新製品の宣伝',
            'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
            'person_in_charge' => '山田 由美',
            'board_id' => $board->id,
        ]);
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $showResponse = $this->get(route('task.show', ['board' => $board->id, 'task' => $task->id]));
        $showResponse
            ->assertOk()
            ->assertJson([
                'title' => '新製品の宣伝',
                'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
                'person_in_charge' => '山田 由美',
                'board_id' => $board->id,
            ]);
    }

    public function testShowAuthorizationException()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task = Task::create([
            'title' => '新製品の宣伝',
            'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
            'person_in_charge' => '山田 由美',
            'board_id' => $board->id,
        ]);
        $this->assertGuest();

        $showResponse = $this->get(route('task.show', ['board' => $board->id, 'task' => $task->id]));
        $showResponse
            ->assertForbidden()
            ->assertJson([
                'title' => 'Forbidden',
                'detail' => 'this_action_is_unauthorized',
            ])
            ->assertJsonMissing([
                'title' => '新製品の宣伝',
                'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
                'person_in_charge' => '山田 由美',
                'board_id' => $board->id,
            ]);
    }

    public function testShowTrashedSuccess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task = Task::create([
            'title' => '新製品の宣伝',
            'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
            'person_in_charge' => '山田 由美',
            'board_id' => $board->id,
        ]);
        $task->delete();
        $this->assertNull(Task::find($task->id), 'The task was not deleted by deletion');
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $showTrashedresponse = $this->get(route('task.showTrashed', ['board' => $board->id, 'task' => $task->id]));
        $showTrashedresponse->assertOk();
        $showTrashedresponse->assertJson([
            'title' => '新製品の宣伝',
            'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
            'person_in_charge' => '山田 由美',
            'board_id' => $board->id,
        ]);
    }

    public function testShowTrashedAuthorizationException()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task = Task::create([
            'title' => '新製品の宣伝',
            'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
            'person_in_charge' => '山田 由美',
            'board_id' => $board->id,
        ]);
        $task->delete();
        $this->assertNull(Task::find($task->id), 'The task was not deleted by deletion');
        $this->assertGuest();

        $showTrashedresponse = $this->get(route('task.showTrashed', ['board' => $board->id, 'task' => $task->id]));
        $showTrashedresponse
            ->assertForbidden()
            ->assertJson([
                'title' => 'Forbidden',
                'detail' => 'this_action_is_unauthorized',
            ])
            ->assertJsonMissing([
                'title' => '新製品の宣伝',
                'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
                'person_in_charge' => '山田 由美',
                'board_id' => $board->id,
            ]);
    }

    public function testUpdateSuccess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task = Task::create([
            'title' => '明日の会議の準備',
            'content' => '明日の会議の資料をまとめて、プレゼン資料の最終チェックをする。',
            'person_in_charge' => '山田 太郎',
            'board_id' => $board->id,
        ]);

        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();
        $updateResponse = $this->put(route('task.update', ['board' => $board->id, 'task' => $task->id]), [
            'title' => 'Shopping List',
            'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
            'person_in_charge' => 'John Smith',
            'board_id' => $board->id,
        ]);
        $updateResponse
            ->assertOk()
            ->assertJson([
                'title' => 'Shopping List',
                'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
                'person_in_charge' => 'John Smith',
                'board_id' => $board->id,
            ]);
    }

    public function testUpdateWithSpecialChars()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task = Task::create([
            'title' => 'Project Proposal',
            'content' => 'Propose a new project for the upcoming quarter. Research the market demand and com',
            'person_in_charge' => 'Emily Johnson',
            'board_id' => $board->id,
        ]);

        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();
        $updateResponse = $this->put(route('task.update', ['board' => $board->id, 'task' => $task->id]), [
            'title' => 'Project P<div>aaa</div>roposal',
            'content' => 'Propose a new p<script>alert("Hacked!");</script> rojecrter. R for the',
            'person_in_charge' => 'Emily‘\'“\"＆&<>＜＞  Johnson',
            'board_id' => $board->id,
        ]);
        $updateResponse
            ->assertOk()
            ->assertJson([
                'title' => 'Project P&lt;div&gt;aaa&lt;/div&gt;roposal',
                'content' => 'Propose a new p&lt;script&gt;alert(&quot;Hacked!&quot;);&lt;/script&gt; rojecrter. R for the',
                'person_in_charge' => 'Emily‘&#039;“\\&quot;＆&amp;&lt;&gt;＜＞  Johnson',
                'board_id' => $board->id,
            ]);
    }

    public function testUpdateAuthorizationException()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task = Task::create([
            'title' => '新製品の宣伝',
            'content' => '新製品の特徴をまとめて、SNSで宣伝する。広告予算は50万円まで。',
            'person_in_charge' => '山田 由美',
            'board_id' => $board->id,
        ]);

        $this->assertGuest();
        $updateResponse = $this->put(route('task.update', ['board' => $board->id, 'task' => $task->id]), [
            'title' => 'Shopping List',
            'content' => 'Milk, bread, eggs, and cheese for breakfast. Chicken, rice, and vegetables for dinner. Snacks for',
            'person_in_charge' => 'John Smith',
            'board_id' => $board->id,
        ]);
        $updateResponse
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

    public function testDestroySucess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task = Task::create([
            'title' => 'タスクを作成する',
            'content' => '新しいタスクを作成し、テスト用のデータを追加する。',
            'person_in_charge' => '山田 太郎 / Taro Yamada',
            'board_id' => $board->id,
        ]);
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $destroyResponse = $this->delete(route('task.destroy', ['board' => $board->id, 'task' => $task->id]));
        $destroyResponse
            ->assertOk()
            ->assertJson([
                'title' => 'タスクを作成する',
                'content' => '新しいタスクを作成し、テスト用のデータを追加する。',
                'person_in_charge' => '山田 太郎 / Taro Yamada',
            ]);
        $this->assertNull(Task::find($task->id), 'The task was not deleted');
    }

    public function testDestroyAuthorizationException()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task = Task::create([
            'title' => 'タスクを作成する',
            'content' => '新しいタスクを作成し、テスト用のデータを追加する。',
            'person_in_charge' => '山田 太郎 / Taro Yamada',
            'board_id' => $board->id,
        ]);

        $this->assertGuest();
        $destroyResponse = $this->delete(route('task.destroy', ['board' => $board->id, 'task' => $task->id]));
        $destroyResponse
            ->assertForbidden()
            ->assertJson([
                'title' => 'Forbidden',
                'detail' => 'this_action_is_unauthorized',
            ])
            ->assertJsonMissing([
                'title' => 'タスクを作成する',
                'content' => '新しいタスクを作成し、テスト用のデータを追加する。',
                'person_in_charge' => '山田 太郎 / Taro Yamada',
                'board_id' => $board->id,
            ]);
        $notdeletedTask = Task::find($task->id);
        $this->assertNull($notdeletedTask->deleted_at, 'the task was deleted');
    }

    public function testRestoreSuccess()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task = Task::create([
            'title' => '明日の会議の準備',
            'content' => '明日の会議の資料をまとめて、プレゼン資料の最終チェックをする。',
            'person_in_charge' => '山田 太郎',
            'board_id' => $board->id,
        ]);
        $task->delete();
        $this->assertNull(Task::find($task->id), 'The task was not deleted');
        $loginResponse = $this->post(route('login', [
            'email' => $user->email,
            'password' => 'password',
        ]));
        $this->assertAuthenticated();
        $loginResponse->assertNoContent();

        $this->patch(route('task.restore', ['board' => $board->id, 'task' => $task->id]));
        $restoredTask = Task::find($task->id);
        $this->assertNull($restoredTask->deleted_at, 'the task was not restored');
    }

    public function testRestoreAuthorizationException()
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'owner_id' => $user->id,
        ]);
        $board = Board::factory()->create([
            'subscription_id' => $subscription->id,
        ]);
        $task = Task::create([
            'title' => '明日の会議の準備',
            'content' => '明日の会議の資料をまとめて、プレゼン資料の最終チェックをする。',
            'person_in_charge' => '山田 太郎',
            'board_id' => $board->id,
        ]);
        $task->delete();
        $this->assertNull(Task::find($task->id), 'The task was not deleted');
        $this->assertGuest();

        $this->patch(route('task.restore', ['board' => $board->id, 'task' => $task->id]));
        $this->assertNull(Task::find($task->id), 'The task was restored');
    }
}