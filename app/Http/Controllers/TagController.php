<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    // ルーティングに定義したURLの{name}の部分に入った文字列が渡る
    public function show(string $name)
    {
        // whereを使って$nameと一致するタグ名を持つタグモデルをコレクションで取得
        $tag = Tag::where('name', $name)->first();

        // viewメソッドを使ってタグ別一覧画面のBladeを表示
            // タグモデルの入った$tagを渡す
        return view('tags.show', ['tag' => $tag]);
    }
}
