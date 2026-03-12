<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'summary',
        'featured_image',
        'reading_time',
        'views_count',
        'likes_count',
        'shares_count',
        'comments_count',
        'status',
        'is_featured',
        'is_sponsored',
        'allow_comments',
        'published_at',
        'author_id',
        'category_id',
        'subcategory_id',
        'meta_keywords',
        'meta_description',
        'tags',
        'related_articles'
    ];

protected $casts = [
    'is_featured' => 'boolean',
    'is_sponsored' => 'boolean',
    'allow_comments' => 'boolean',
    'published_at' => 'datetime',
    'meta_keywords' => 'array',
    'tags' => 'array',
    'related_articles' => 'array'
];
public function getTagLinksAttribute()
{
    if (empty($this->tags) || !is_array($this->tags)) {
        return [];
    }

    return collect($this->tags)->map(function ($tag) {
        return [
            'name' => $tag,
            'url'  => route('articles.tag', ['tag' => $tag]),
            'slug' => \Illuminate\Support\Str::slug($tag),
        ];
    })->toArray();
}
public function scopeByTag($query, $tag)
{
    return $query->whereJsonContains('tags', $tag);
}
    // إضافة Accessor لـ reading_time
    protected $appends = ['formatted_reading_time'];

    // Accessor لحساب وقت القراءة تلقائياً إذا لم يكن محدداً
    public function getFormattedReadingTimeAttribute()
    {
        if ($this->reading_time && !is_numeric($this->reading_time)) {
            return $this->reading_time;
        }

        // إذا كان reading_time رقمي (عدد الدقائق)
        if ($this->reading_time && is_numeric($this->reading_time)) {
            return $this->reading_time . ' دقيقة';
        }

        // حساب وقت القراءة من عدد الكلمات
        $wordCount = str_word_count(strip_tags($this->content));
        $readingTime = ceil($wordCount / 200); // 200 كلمة في الدقيقة

        return $readingTime . ' دقيقة';
    }

    // Accessor للوصول إلى reading_time القديم
    public function getReadingTimeAttribute($value)
    {
        // إذا كانت القيمة محفوظة كرقم (مثل "5") أو كنص (مثل "5 دقائق")
        return $value;
    }

    // Mutator لحفظ reading_time
    public function setReadingTimeAttribute($value)
    {
        // تنظيف القيمة - إزالة "دقيقة" أو "minutes"
        if (is_string($value)) {
            $value = preg_replace('/[^0-9]/', '', $value);
        }

        $this->attributes['reading_time'] = $value ?: null;
    }

    // طريقة لتحديث slug تلقائياً من العنوان
    public static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title') && empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    // العلاقات
    public function author()
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(ArticleCategory::class, 'subcategory_id');
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class)->whereNull('parent_id');
    }

    public function allComments()
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function likes()
    {
        return $this->hasMany(ArticleLike::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(ArticleBookmark::class);
    }

    public function shares()
    {
        return $this->hasMany(ArticleShare::class);
    }

    public function views()
    {
        return $this->hasMany(ArticleView::class);
    }

    // public function sections()
    // {
    //     return $this->hasMany(ArticleSection::class);
    // }

    public function images()
    {
        return $this->hasMany(ArticleImage::class);
    }

    public function increaseViewCount()
    {
        $this->increment('views_count');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // طريقة جديدة لضمان وجود reading_time
    public function ensureReadingTime()
    {
        if (empty($this->reading_time) || !is_numeric($this->reading_time)) {
            $wordCount = str_word_count(strip_tags($this->content));
            $this->reading_time = ceil($wordCount / 200);
            $this->save();
        }
    }
}
