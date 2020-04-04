@csrf
<div class="md-form">
  <label>タイトル</label>
  <input type="text" name="title" class="form-control" required value="{{ $article->title ?? old('title') }}">
</div>

{{-- タグ機能 --}}
<div class="form-group">
  {{-- vueのInitialTagsプロパティにタグ情報の入った$tagNamesの値を渡す --}}
  <article-tags-input
  :initial-tags='@json($tagNames ?? [])'
  >
  </article-tags-input>
</div>

<div class="form-group">
  <label></label>
  <textarea name="body" required class="form-control" row="16" placeholder="本文">{{ $article->body ?? old('title') }}</textarea>
</div>