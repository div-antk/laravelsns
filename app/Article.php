<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// 記事モデルとユーザーモデルの関係は多対多
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Article extends Model
{
    protected $fillable = [
        'title',
        'body',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    public function likes(): BelomgsToMany
    {
        // 第一引数には関連するモデルの名前、第二引数には中間テーブル名
        // likesテーブルにはcreated_at、update_atがあるのでwithTimestampsを付ける
        return $this->belingsToMany('App\User', 'likes')->withTimestamps();
    }

    public function isLikedBy(?User $user): bool
    {
        return $user
            // nullableで引数がnullであることも許容する
            // 三項演算子で$userがnullかどうかで処理を分ける
            // nullであればfalseを返す
            ? (bool)$this->likes->where('id', $user->id)->count()
    }
}
