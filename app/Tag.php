<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}