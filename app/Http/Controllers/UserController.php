<?php

// ユーザーページの表示やフォローのアクションメソッドを持たせる

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(string $name)
    {
        // ルーティングに定義したURL /users/{name} の部分が渡る
        $user = User::where('name', $name)->first();

        // ユーザーの投稿した記事モデルをコレクションで所得して投稿日の降順でソート
        $articles = $user->articles->sortByDesc('created_at');

        // ユーザーページのBladeを表示し、ユーザーモデルの入った変数 $user を渡す
        return view('users.show', [
            'user' => $user,
            'articles' => $articles,
        ]);
    }

    // フォロー
    // 引数 $user にはURLのnameの部分が渡ってくる
    public function follow(Request $request, string $name)
    {
        // 条件に一致するユーザーモデルをコレクションとして最初の1件を取得
            // $user にはフォローされる側のユーザーのユーザーモデルが代入される
        $user = User::where('name', $name)->first();

        if ($user->id === $request->user()->id)
        {
            // 自分自身をフォローしようとするとエラーのHTTPステータスコードをレスポンスする
            return abort('404', 'Cannot follow yourself.');
        }

        // followingメソッドは多対多のリレーション（BelongsToManyクラスのインスタンス）が返ることを想定
        // detachしてからattachしているのは複数回重ねてフォローできないようにするため
        $request->user()->followings()->detach($user);
        $request->user()->followings()->attach($user);

        // コントローラで配列や連想配列を返すと、JSON形式に変換されてレスポンスされる
        // どのユーザーへのフォローが成功したかがわかるようにユーザーの名前を返す
        return ['name' => $name];
    }

    // フォロー解除
    public function unfollow(Request $request, string $name)
    {
        $user = User::where('name', $name)->first();

        if ($user->id === $request->user()->id)
        {
            return abort('404', 'Cannot follow yourself.');
        }

        $request->user()->followings()->detach($user);

        return ['name' => $name];
    }
}
