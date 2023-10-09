<?php

namespace App\Http\Controllers;

use App\Http\Requests\BoardRequest;
use App\Http\Requests\TaskRequest;
use App\Models\Board;
use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BoardController extends Controller
{
    public function show(Board $board)
    {
        try {
            $this->authorize('view', [$board]);
            // 配列の最初の要素は、呼び出すポリシーを決定するために使用され、残りの配列要素は、パラメーターとしてポリシーメソッドに渡され、認可の決定を行う際の追加のコンテキストに使用できます。
            return $board;
        } catch (AuthorizationException $e) {
            report($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(BoardRequest $request, Board $board)
    {
        try {
            $this->authorize('update', [$board]);
            //$request=ユーザーの入力情報　例：{"title":"taitoru2","content":"kontento2","person_in_charge":"cha-ji2"}
            //$task=DBのレコード　例：{"id":11,"title":"taitoru2","content":"kontento2","person_in_charge":"cha-ji2","created_at":"2022-08-12T10:17:48.000000Z","updated_at":"2022-08-12T10:32:01.000000Z"}
            $board->update($request->all());
            return $board;
        } catch (AuthorizationException $e) {
            report($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function destroy(Board $board)
    {
        // throw new HttpResponseException(response()->json(['', 500]));
        try {
            $this->authorize('delete', $board);
            $board->delete();
            return $board;
        } catch (AuthorizationException $e) {
            report($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function getTasks(Board $board){
        try {
            // throw new HttpResponseException(response()->json(['', 500]));
            $this->authorize('getTasks', $board);
            return $board->tasks;
        } catch (AuthorizationException $e) {
            report($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function createTask(TaskRequest $request, Board $board)
    {
        try {
            $this->authorize('createTask', $board);
            return Task::create($request->all());
        } catch (AuthorizationException $e) {
            report($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function getTrashedTasks(Board $board)
    {
        try {
            $this->authorize('getTasks', $board);
            // throw new HttpResponseException(response()->json(['', 500]));
            return $board->tasksOnlyTrashed;
        } catch (AuthorizationException $e) {
            report($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            report($e, false);
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
