<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
    ];

    // タグをハッシュタグ風に表示するアクセサ
    public function getHashtagAttribute(): string
    {
        return '#' .$this->name;
    }

    // タグモデルと記事モデルのリレーション（多対多）を定義
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany('App\Article')->withTimestamps();
    }
}