<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    // Laravelでの開発でいつもやってること>エラー通知 https://zenn.dev/nrikiji/articles/d5b991402ea89c
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Validation\ValidationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // Laravelでの開発でいつもやってること>APIのエラーレスポンスのjson化 https://zenn.dev/nrikiji/articles/d5b991402ea89c#api%E3%81%AE%E3%82%A8%E3%83%A9%E3%83%BC%E3%83%AC%E3%82%B9%E3%83%9D%E3%83%B3%E3%82%B9%E3%81%AEjson%E5%8C%96
    // Laravel: APIの例外はJSONを返そう https://zenn.dev/blancpanda/articles/laravel-api-exception-renderer#discuss
    // Laravel 8.x エラー処理 https://readouble.com/laravel/8.x/ja/errors.html
    public function render($request, Throwable $e)
    {
        $title = '';
        $status = null;
        $detail = '';
        // Log::info(get_class($e));//エラーインスタンスのクラスを確認
        if (!$request->is('api/*')) {// リクエストされたURLが"/api/*"でなかった場合はエラーを返さない
            report(new \Exception($e->getMessage()));
            $title = Response::$statusTexts[Response::HTTP_BAD_REQUEST];
            $status = Response::HTTP_BAD_REQUEST;
            $detail = 'the_given_data_is_invalid';
        } else if ($e instanceof ModelNotFoundException) {// Route Model Binding でデータが見つからない(DB上にレコードが存在しない)場合
            report(new \Exception($e->getMessage()));
            $title = Response::$statusTexts[Response::HTTP_NOT_FOUND];
            $status = Response::HTTP_NOT_FOUND;
            $detail = 'the_data_you_requested_could_not_be_found';
        } else if ($e instanceof NotFoundHttpException) {// リクエストされたURLに対応するルートが存在しない場合
            report(new \Exception($e->getMessage()));
            $title = Response::$statusTexts[Response::HTTP_NOT_FOUND];
            $status = Response::HTTP_NOT_FOUND;
            $detail = 'the_requested_url_was_not_found_on_this_server';
        }else if ($e instanceof AuthorizationException) {// ユーザーに操作を行う権限がない場合
            $title = Response::$statusTexts[Response::HTTP_FORBIDDEN];
            $status = Response::HTTP_FORBIDDEN;
            $detail = 'this_action_is_unauthorized';
        } else if ($this->isHttpException($e)) {// HttpExceptionがthrowされた場合
        // Laravelでのエラー処理の流れを把握したいのでソースコードリーディング https://qiita.com/wim/items/80406ecc23658896241d
            $title = Response::$statusTexts[$e->getStatusCode()];
            $status = $e->getStatusCode();
            $detail = $e->getMessage() ?: $title;//コントローラー側でエラーメッセージをカスタマイズできるようにしておく
        } else {
            report(new \Exception($e->getMessage()));
            $title = Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $detail = 'an_unexpected_error_occurred_while_processing_your_request';
        }
        return response()->json([
            'title' => $title,
            'status' => $status,
            'detail' => $detail
        ],$status,['Content-Type' => 'application/problem+json']);
    }
}
