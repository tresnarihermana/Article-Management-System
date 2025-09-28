<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'cover',
        'featured_image',
        'status',
        'is_pinned',
        'published_at',
        'rejected_message',
        'views',
        'hits',

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
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function likedByUser($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
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

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    protected $appends = ['cover_url'];
    public function getCoverUrlAttribute()
    {
        return asset('storage/' . $this->cover);
    }


    protected function safeTimestamp($time): ?int
    {
        if (!$time) {
            return null;
        }

        if ($time instanceof Carbon) {
            return $time->timestamp;
        }

        if (is_string($time)) {
            try {
                $dt = Carbon::parse($time);
                return $dt->timestamp;
            } catch (\Exception $e) {
                // Jika string tidak valid, return null
                return null;
            }
        }

        // Field bukan string / Carbon → return null
        return null;
    }

    public function toSearchableArray(): array

    {

        return array_merge($this->toArray(), [

            'id' => (string) $this->id,

            'title' => (string) $this->title,

            'body' => (string) $this->body,

            'excerpt' => (string) $this->excerpt,

            'cover' => (string) $this->cover,

            'status' => (string) $this->status,

            'slug' => (string) $this->slug,

            'published_at' => (int) $this->safeTimestamp($this->published_at),

            'created_at' => $this->safeTimestamp($this->created_at),

            'updated_at' => $this->safeTimestamp($this->updated_at),

            'is_pinned' => (bool) $this->is_pinned,

        ]);
    }
    public function searchableAs()
{
    return env('TYPESENSE_COLLECTION', 'DailyLiMENews_Indexes');
}

}
