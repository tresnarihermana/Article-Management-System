<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_tag')->withTimestamps();
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_tag')->withTimestamps();
    }
    protected $fillable = [
        'tag_id',
        'name',
        'slug'
    ];
}
