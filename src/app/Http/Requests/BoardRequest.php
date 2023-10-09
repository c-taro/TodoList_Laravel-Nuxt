<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class BoardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
        // 【laravel】FormRequestによるバリデーション https://qiita.com/gone0021/items/249e99338ff414fc5737
        // PHP で文字列が指定された文字列で始まるかどうかを確認する方法 https://www.delftstack.com/ja/howto/php/how-to-check-if-a-string-starts-with-a-specified-string-in-php/#php-で-strpos-関数を使用して文字列が指定された文字列で始まるかどうかを確認する
    }

    // laravel formRequest バリデーションチェック前に実行されるメソッド https://qiita.com/miriwo/items/5a1ec155d3c6f80737c3
    public function prepareForValidation()
    {
        $input = $this->all();

        // 特殊文字をエスケープ処理する
        if(array_key_exists('title', $input)){
            $input['title'] = htmlspecialchars($input['title'], ENT_QUOTES);
        }
        // リクエストボディーがname要素だけの場合、HTMLタグを取り除いたキーと情報で置き換える
        $this->replace($input);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:32'],
        ];
    }

    // laravel API バリデーションエラー時にエラー内容をJSONで返す https://qiita.com/miriwo/items/637961702389d6673489
    protected function failedValidation(Validator $validator)
    {
        $response['title'] = Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY];
        $response['status'] = Response::HTTP_UNPROCESSABLE_ENTITY;
        $response['detail'] = Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY];
        $response['Content-Type'] = 'application/problem+json';
        $response['errors'] = $validator->errors()->toArray();
    
        throw new HttpResponseException(response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY));
        // response()->json(...)の第二引数にステータスコードを渡す 【PHP】Laravel で JSON 形式の Web API を実装する時に考えること https://www.utakata.work/entry/laravel/json-web-api
        // LaravelでHTTP Status Codeの定数を使う【Symfony】 https://rapicro.com/how-to-use-http-status-codes-constants-in-laravel/
        // WebAPIでバリデーションチェックエラーの際、HTTPステータスは何を返すのが適切か https://qiita.com/nesheep5/items/6da796f6ac628c430c36
    }
}