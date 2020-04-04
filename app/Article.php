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

    public function likes(): BelongsToMany
    {
        // 第一引数には関連するモデルの名前、第二引数には中間テーブル名
        // likesテーブルにはcreated_at、update_atがあるのでwithTimestampsを付ける
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
    }

    // あるユーザーがいいね済みかどうかを判定するメソッド
    public function isLikedBy(?User $user): bool
    {
        return $user
            // nullableで引数がnullであることも許容する
            // 三項演算子で$userがnullかどうかで処理を分ける
            //   nullであればfalseを返す
            // countメソッドによって、いいねをしたユーザーの中に引数として渡された$userがいれば
            // 1かそれより大きい数値が返る
            //   いなければ0が返る
            // ひとつの記事に複数回いいねができないため、2以上の数値が返ることはない
            ? (bool)$this->likes->where('id', $user->id)->count()
            : false;
    }

    public function getCountLikesAttribute(): int
    {
        // 特定記事にいいねをした全ユーザーモデルがコレクションで返る
        // countメソッドでコレクションの要素数を数える
        return $this->likes->count();
    }

    // tagテーブルへの多対多のリレーションを定義
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }
}
