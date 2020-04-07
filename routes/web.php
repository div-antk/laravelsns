<?php

// ルーティングの定義

Auth::routes();

// 他サービスでのログイン用のルーティング
  // {provider}の部分は、利用する他のサービスの名前を入れることを想定している。Googleであれば{google}
Route::prefix('login')->name('login.')->group(function () {
  Route::get('/{provider}', 'Auth\LoginController@redirectToProvider')->name('{provider}');
  Route::get('/{provider}/callback', 'Auth\LoginController@handleProviderCallBack')->name('{provider}.callback');
});

Route::get('/', 'ArticleController@index')->name('articles.index');
Route::resource('/articles', 'ArticleController')->except(['index', 'show'])->middleware('auth');
Route::resource('/articles', 'ArticleController')->only(['show']);

// groupメソッドを使うことで、それまでに定義した
  // prefix('articles') name('articles.')を
  // groupメソッドにクロージャ（無名関数）として渡した各ルーティングにまとめて適用させる
// prefixメソッドは引数として渡した文字列をURLの先頭につける
// nameメソッドはルーティングに名前をつける
Route::prefix('articles')->name('articles.')->group(function () {
  Route::put('/{article}/like', 'ArticleController@like')->name('like')->middleware('auth');
  Route::delete('/{article}/like', 'ArticleController@unlike')->name('unlike')->middleware('auth');
}); 
Route::get('/tags/{name}', 'TagController@show')->name('tags.show');
Route::prefix('users')->name('users.')->group(function () {
  Route::get('/{name}', 'UserController@show')->name('show');
  
  // ユーザーがいいねした記事一覧のルーティング
    // 未ログインユーザーでも参照可能にするためmiddlewareの外側
  Route::get('/{name}/likes', 'UserController@likes')->name('likes');

  // フォロー、フォロワー一覧のルーティング
    // 未ログインユーザーでも参照可能にするためmiddlewareの外側
  Route::get('/{name}/followings', 'UserController@followings')->name('followings');
  Route::get('/{name}/followers', 'UserController@followers')->name('followers');

  Route::middleware('auth')->group(function () {
    Route::put('/{name}/follow', 'UserController@follow')->name('follow');
    Route::delete('/{name}/follow', 'UserController@unfollow')->name('unfollow');
  });
});