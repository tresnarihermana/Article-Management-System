<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'featured_image',
        'status',
        'is_pinned',
        'published_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'article_category')->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tag')->withTimestamps();
    }

    // public function comments()
    // {
    //     return $this->hasMany(Comment::class)->whereNull('parent_id');
    // }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    // 🔎 Scope untuk published only
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
