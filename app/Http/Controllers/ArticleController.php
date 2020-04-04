<?php

namespace App\Http\Controllers;

use App\Article;
use App\Tag;
use App\Http\Requests\ArticleRequest;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Article::class, 'article');
    }
    
    public function index()
    {
        $articles = Article::all()->sortByDesc('created_at');

        return view('articles.index', ['articles' => $articles]);
    }

    public function create()
    {
        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });

        return view('articles.create', [
            'allTagNames' => $allTagNames,
        ]);
    }

    public function store(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all());
        $article->user_id = $request->user()->id;
        $article->save();
        // 渡されたクロージャの第一引数にはコレクションの値
            // 第二引数にはコレクションのキーが入っている
        // 繰り返し処理の1回目では、例えば['USA', 'France']であれば
        //     第一引数はUSA、第二引数には0（2回目の繰り返しでは1）
        $request->tags->each(function ($tagName) use ($article) {
            // すでにtagsテーブルに存在するかそうでないかを判断
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });
        return redirect()->route('articles.index');
    }

    public function edit(Article $article)
    {
        // タグ名に対してtextというキーが付いている必要があるため
        //     mapメソッドで連想配列にする
        $tagNames = $article->tags->map(function ($tag) {
            return ['text' => $tag->name];
        });

        // 自動補完機能のため
        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });

        return view('articles.edit', [
            'article' => $article,
            // ブレードに$tagNamesという変数で渡す
            'tagNames' => $tagNames,
            'allTagNames' => $allTagNames,
        ]);
    }

    public function update(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all())->save();

        $article->tags()->detach();
        $request->tags->each(function ($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });

        return redirect()->route('articles.index');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }

    public function show(Article $article)
    {
        return view('articles.show', ['article' => $article]);
    }

    public function like(Request $request, Article $article)
    {
        // 記事モデルとリクエストを送信したユーザーのモデルを紐付ける
        //     likesテーブルのレコードが新規登録される
        $article->likes()->detach($request->user()->id);
        $article->likes()->attach($request->user()->id);

        // 非同期通信に対するレスポンス
        // コントローラのアクションメソッドで配列や連想配列を返すと
        //     JSON形式に変換されてレスポンスされる
        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }
}
