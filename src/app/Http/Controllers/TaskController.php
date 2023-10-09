<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Board;
use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskController extends Controller
{
    public function show(Board $board, Task $task)
    {
        try {
            $this->authorize('view', [$task, $board]);
            return $task;
        } catch (AuthorizationException $e) {
            report($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function showTrashed(Board $board, Task $task)
    {
        try {
            $this->authorize('view', [$task, $board]);
            if($task->deleted_at === null){
                throw new HttpException(500, 'the_task_is_not_deleted');//【Laravel】任意のExceptionをthrowする。 https://qiita.com/msht0511/items/675cfc1643b4002849a3
            }
            return $task;//例えばid=10000を取得しようとすると、多分$taskにエラーが入ってフロントに返される　"message"、"exception"、"file"、"line"、"trace"とかが入ってる　フロントに情報を与えたくないから、やっぱ例外処理を修正する必要がありそう
            //route model binding https://laracasts.com/discuss/channels/eloquent/is-it-possible-to-throw-special-exception-when-route-model-binding-not-found-and-handle-it-instead-of-page-404
        } catch (AuthorizationException $e) {
            report($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new HttpException($e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(TaskRequest $request, Board $board, Task $task)
    {
        try {
            //$request=ユーザーの入力情報　例：{"title":"taitoru2","content":"kontento2","person_in_charge":"cha-ji2"}
            //$task=DBのレコード　例：{"id":11,"title":"taitoru2","content":"kontento2","person_in_charge":"cha-ji2","created_at":"2022-08-12T10:17:48.000000Z","updated_at":"2022-08-12T10:32:01.000000Z"}
            $this->authorize('update', [$task, $board]);
            $task->update($request->all());
            return $task;
        } catch (AuthorizationException $e) {
            report($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function destroy(Board $board, Task $task)
    {
        try {
            $this->authorize('delete', [$task, $board]);
            $task->delete();
            return $task;
        } catch (AuthorizationException $e) {
            report($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function restore(Board $board, Task $task)
    {
        // throw new HttpResponseException(response()->json(['', 500]));
        try {
            $this->authorize('restore', [$task, $board]);
            $task->deleted_at = null;
            $task->save();
            return $task;
        } catch (AuthorizationException $e) {
            report($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
