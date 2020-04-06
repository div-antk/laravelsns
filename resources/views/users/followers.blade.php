@extends('app')

@section('title', $user->name . 'のフォロワー')

@section('content')
  @include('nav')
  <div class="container">
    @include('users.user')
    {{-- 投稿記事一覧、いいね一覧をどちらも選択されていない状態にする --}}
    @include('users.tabs', ['hasArticles' => false, 'hasLikes' => false])
    @foreach ($followers as $person)
      @include('users.person')
    @endforeach
  </div>
@endsection