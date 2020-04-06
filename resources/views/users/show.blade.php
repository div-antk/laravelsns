{{-- ユーザーページ --}}

@extends('app')

@section('title', $user->name)

@section('content')
  @include('nav')
  <div class="container">
    
    {{-- ユーザーページ --}}
    @include('users.user')

    {{-- 記事一覧といいねした記事一覧のタブ --}}
      {{-- 三項演算子で使うための変数を渡す --}}
    @include('users.tabs', ['hasArticles' => true, 'hasLikes' => false])

    @foreach ($articles as $article)
      @include('articles.card')
    @endforeach
  </div>
@endsection