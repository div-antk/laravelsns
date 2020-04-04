<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:50',
            'body' => 'required|max:500',
            // json形式かどうかのバリデーション。半角スペースが無いことをチェックする正規表現
            'tags' => 'json|regex:/^(?!.*\s).+$/u',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'タイトル',
            'body' => '本文',
            'tags' => 'タグ',
        ];
    }

    // フォームリクエストのバリデーションが成功した後に自動的に呼ばれるメソッド
    public function passedValidation()
    {
        // json_decode関数を使ってJSON形式の文字列であるタグ情報を連想配列に変換
        // コレクションメソッドを使うためにコレクション形式に変換
        $this->tags = collect(json_decode($this->tags))
        // コレクションの要素が6個以上あったとしても最初の5個だけが残る
        ->slice(0, 5)
        // 引数に関数を渡す
        ->map(function ($requestTag) {
            return $requestTag->text;
        });
    }
}
