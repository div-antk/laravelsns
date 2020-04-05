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

        // ユーザーページのBladeを表示し、ユーザーモデルの入った変数 $user を渡す
        return view('users.show', [
            'user' => $user,
        ]);
    }
}
