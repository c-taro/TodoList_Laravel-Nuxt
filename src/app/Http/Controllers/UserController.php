<?php

namespace App\Http\Controllers;

use App\Http\Requests\BoardRequest;
use App\Models\Board;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{
    public function getBoards(){
        try {
            // throw new HttpResponseException(response()->json(['', 500]));
            return User::find(auth()->id())->boards;
        } catch (\Exception $e) {
            // バックスラッシュなしだと「App\Http\Controllers\Exception」が指定される　すべてのException(グローバルなException=「\Exception」)をcatchしたい場合は「\Exception」と書く 参考：https://detail.chiebukuro.yahoo.co.jp/qa/question_detail/q13275340168 https://tamiblog.xyz/2021/06/16/post-2072/
            report($e);
            // reportメソッドを使うと、どのファイルの何行目で処理が止まっているか（エラーが起きているか）まで出力してくれます。 【Laravel】例外処理(try...catch)でのデバッグ https://qiita.com/wanwanwan/items/aa257f26c0050b346af4
        }
    }

    public function createBoard(BoardRequest $request){
        try {
            // throw new HttpResponseException(response()->json(['', 500]));
            return Board::create([
                'title' => $request->title,
                'subscription_id' => User::find(auth()->id())->subscription->id,
            ]);
        } catch (AuthorizationException $e) {
            report($e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            report($e);
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
