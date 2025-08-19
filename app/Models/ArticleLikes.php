<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleLikes extends Model
{
    protected $fillable = ['user_id', 'article_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Article::class);
    }
}

