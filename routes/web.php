<?php

Auth::routes();
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
  ROute::delete('/{article}/like', 'ArticleController@unlike')->name('unlike')->middleware('auth');
}); 