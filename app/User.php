<?php

namespace App;

use App\Mail\BareMail;
use App\Notifications\PasswordResetNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // PasswordResetNotificationクラスのインスタンスを作成し、notifyメソッドに渡す
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token, new BareMail()));
    }

    // リレーション（多対多）
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany('App\User', 'follows', 'followee_id', 'follower_id')->withTimestamps();
    }


    // これからフォローするユーザー、あるいはフォロー中のユーザーのモデルにアクセス可能にするためのリレーションメソッド
    public function followings(): BelongsToMany
    {
        // リレーション元のusersテーブルのidは、中間テーブルのfollower_idと紐付く
        // リレーション先のusersテーブルのidは、中間テーブルのfollowee_idと紐付く
        return $this->belongsToMany('App\User', 'follows', 'follower_id', 'followee_id')->withTimestamps();
    }

    // あるユーザーをフォロー中かどうかを判定するメソッド
    public function isFollowedBy(?User $user): bool
    {
        return $user
            ? (bool)$this->followers->where('id', $user->id)->count()
            : false;
    }

    // フォロー数を算出するアクセサ
    public function getCountFollowersAttribute(): int
    {
        return $this->followers->count();
    }

    // フォロワー数を算出するアクセサ
    public function getCountFollowingsAttribute(): int
    {
        return $this->followings->count();
    }
}
