{{-- ユーザーページ --}}

@extends('app')

@section('title', $user->name)

@section('content')
  @include('nav')
  <div class="container">
    <div class="card mt-3">
      <div class="card-body">
        <div class="d-flex flex-row">
          <a href="{{ route('users.show', ['name' => $user->name]) }}" class="text-dark">
            <i class="fas fa-user-circle fa-3x"></i>
          </a>

          {{-- 自分自身をフォローできないようにするため
            ログイン中のユーザーidと表示されるユーザーidが一致した場合フォローボタンを非表示にする --}}
          @if( Auth::id() !== $user->id )
            <follow-button
              class="ml-auto"
              {{-- @jsonを使うことで $user->isFollowedBy(Auth::user()) の結果を文字列ではなく値で返す --}}
              :initial-is-followed-by='@json($user->isFollowedBy(Auth::user()))'
              {{-- フォローはログイン中のみできるため、Authのcheckメソッドでtrueかfalseで返す --}}
              :authorized='@json(Auth::check())'
              {{-- route関数で取得したURLを文字列で渡す --}}
              endpoint="{{ route('users.follow', ['name' => $user->name] )}}"
            >
            </follow-button>
          @endif

        </div>
        <h2 class="h5 card-title m-0">
          <a href="{{ route('users.show', ['name' => $user->name]) }}" class="text-dark">
            {{ $user->name }}
          </a>
        </h2>
      </div>
      <div class="card-body">
        <div class="card-text">
          <a href="" class="text-muted">
            {{ $user->count_followings }} フォロー
          </a>
          <a href="" class="text-muted">
            {{ $user->count_followers }}  フォロー
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection