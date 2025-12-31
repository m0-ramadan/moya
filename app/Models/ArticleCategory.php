<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'parent_id',
        'order',
        'is_active',
        'show_in_menu',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean',
        'meta_keywords' => 'array'
    ];

    public function parent()
    {
        return $this->belongsTo(ArticleCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ArticleCategory::class, 'parent_id');
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id');
    }

    public function getArticlesCountAttribute()
    {
        return $this->articles()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParentCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeWithChildren($query)
    {
        return $query->with(['children' => function ($q) {
            $q->active()->orderBy('order');
        }]);
    }
}
