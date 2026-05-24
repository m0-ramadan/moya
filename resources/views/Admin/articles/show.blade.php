@extends('Admin.layout.master')

@section('title', 'عرض المقال: ' . $article->title)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #696cff;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --dark-bg: #1e1e2d;
            --dark-card: #2b3b4c;
            --darker-card: #1e2a3a;
        }

        body {
            font-family: "Cairo", sans-serif !important;
            background: var(--dark-bg);
            color: #fff;
        }

        /* بطاقة المقال الرئيسية */
        .article-card {
            background: var(--dark-card);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .article-header {
            background: var(--primary-gradient);
            padding: 30px;
            position: relative;
        }

        .article-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M20 20 L80 20 L80 80 L20 80 Z" fill="none" stroke="white" stroke-width="2"/><path d="M30 30 L70 30 L70 70 L30 70 Z" fill="none" stroke="white" stroke-width="2"/></svg>') repeat;
            opacity: 0.1;
        }

        .article-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
            color: white;
            position: relative;
        }

        .article-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
            position: relative;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        .meta-item i {
            font-size: 16px;
        }

        .badge-status {
            padding: 8px 16px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-published {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-draft {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
            border: 1px solid rgba(108, 117, 125, 0.3);
        }

        .status-featured {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-sponsored {
            background: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
            border: 1px solid rgba(23, 162, 184, 0.3);
        }

        /* بطاقات الإحصائيات */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(105, 108, 255, 0.1);
            border-color: var(--primary-color);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(105, 108, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 20px;
            color: var(--primary-color);
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 5px;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
        }

        /* صورة المقال */
        .featured-image-container {
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
            position: relative;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }

        .featured-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
        }

        .image-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: #fff;
            padding: 20px;
            font-size: 14px;
        }

        /* محتوى المقال */
        .article-content {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.9);
        }

        .article-content h1,
        .article-content h2,
        .article-content h3,
        .article-content h4 {
            color: #fff;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .article-content p {
            margin-bottom: 20px;
        }

        .article-content img {
            max-width: 100%;
            border-radius: 10px;
            margin: 20px 0;
        }

        .article-content blockquote {
            border-right: 4px solid var(--primary-color);
            background: rgba(105, 108, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            font-style: italic;
        }

        .article-content ul,
        .article-content ol {
            margin-bottom: 20px;
            padding-right: 20px;
        }

        .article-content li {
            margin-bottom: 5px;
        }

        .article-content pre {
            background: var(--darker-card);
            border-radius: 10px;
            padding: 15px;
            overflow-x: auto;
            margin: 20px 0;
        }

        .article-content code {
            background: rgba(105, 108, 255, 0.2);
            padding: 3px 6px;
            border-radius: 5px;
            color: var(--primary-color);
        }

        /* قسم الوسوم */
        .tags-section {
            margin-bottom: 30px;
        }

        .tags-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #fff;
        }

        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tag {
            background: rgba(105, 108, 255, 0.1);
            border: 1px solid var(--primary-color);
            color: #fff;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .tag:hover {
            background: var(--primary-gradient);
            transform: translateY(-2px);
        }

        /* صور إضافية */
        .additional-images {
            margin-bottom: 30px;
        }

        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .image-item {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
        }

        .image-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .image-item:hover img {
            transform: scale(1.05);
        }

        .image-item-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: #fff;
            padding: 10px;
            font-size: 12px;
            text-align: center;
        }

        /* معلومات إضافية */
        .info-section {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(105, 108, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
        }

        /* التعليقات */
        .comments-section {
            margin-top: 40px;
        }

        .comments-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #fff;
        }

        .comment {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .comment-author {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .comment-name {
            font-weight: 600;
            color: #fff;
        }

        .comment-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }

        .comment-content {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .comment-actions {
            display: flex;
            gap: 15px;
        }

        .comment-action {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .comment-action:hover {
            color: var(--primary-color);
        }

        .comment-reply {
            margin-right: 50px;
            margin-top: 15px;
            padding-right: 20px;
            border-right: 2px solid var(--primary-color);
        }

        /* مقالات ذات صلة */
        .related-articles {
            margin-top: 40px;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .related-card {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease;
            text-decoration: none;
        }

        .related-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
        }

        .related-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .related-content {
            padding: 15px;
        }

        .related-title {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .related-meta {
            display: flex;
            justify-content: space-between;
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
        }

        /* SEO Info */
        .seo-card {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 20px;
        }

        .seo-item {
            margin-bottom: 15px;
        }

        .seo-label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 5px;
        }

        .seo-value {
            background: rgba(255, 255, 255, 0.05);
            padding: 10px;
            border-radius: 8px;
            color: #fff;
            word-break: break-word;
        }

        /* أزرار الإجراءات */
        .action-buttons {
            position: sticky;
            bottom: 30px;
            left: 30px;
            z-index: 1000;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .action-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--dark-card);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .action-btn:hover {
            transform: translateY(-5px);
        }

        .action-btn.edit {
            background: var(--primary-gradient);
        }

        .action-btn.delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .action-btn.back {
            background: rgba(255, 255, 255, 0.1);
        }

        /* responsive */
        @media (max-width: 768px) {
            .article-title {
                font-size: 22px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .related-grid {
                grid-template-columns: 1fr;
            }

            .comment-reply {
                margin-right: 20px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- مسار التنقل -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                     <a href="{{ route('admin.home') }}">الرئيسية</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.articles.index') }}">المقالات</a>
                </li>
                <li class="breadcrumb-item active">{{ $article->title }}</li>
            </ol>
        </nav>

        <!-- المقال الرئيسي -->
        <div class="article-card">
            <!-- رأس المقال -->
            <div class="article-header">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex gap-2">
                        @if ($article->is_featured)
                            <span class="badge-status status-featured">
                                <i class="fas fa-star"></i> مميز
                            </span>
                        @endif

                        @if ($article->is_sponsored)
                            <span class="badge-status status-sponsored">
                                <i class="fas fa-ad"></i> برعاية
                            </span>
                        @endif

                        <span
                            class="badge-status {{ $article->status === 'published' ? 'status-published' : 'status-draft' }}">
                            <i class="fas {{ $article->status === 'published' ? 'fa-check-circle' : 'fa-pen' }}"></i>
                            {{ $article->status === 'published' ? 'منشور' : 'مسودة' }}
                        </span>
                    </div>

                    <div class="text-white">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $article->created_at->format('Y/m/d') }}
                    </div>
                </div>

                <h1 class="article-title">{{ $article->title }}</h1>

                <div class="article-meta">
                    <div class="meta-item">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ $article->author->name ?? 'غير معروف' }}</span>
                    </div>

                    <div class="meta-item">
                        <i class="fas fa-folder"></i>
                        <span>{{ $article->category->name ?? 'غير مصنف' }}</span>
                    </div>

                    @if ($article->subcategory)
                        <div class="meta-item">
                            <i class="fas fa-folder-open"></i>
                            <span>{{ $article->subcategory->name }}</span>
                        </div>
                    @endif

                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span>{{ $article->formatted_reading_time }}</span>
                    </div>

                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>آخر تحديث: {{ $article->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- جسم المقال -->
            <div class="p-4">
                <!-- الإحصائيات -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="stat-value">{{ number_format($article->views_count) }}</div>
                        <div class="stat-label">عدد المشاهدات</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-value">{{ number_format($article->likes_count) }}</div>
                        <div class="stat-label">الإعجابات</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <div class="stat-value">{{ number_format($article->shares_count) }}</div>
                        <div class="stat-label">المشاركات</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-comment"></i>
                        </div>
                        <div class="stat-value">{{ number_format($article->comments_count) }}</div>
                        <div class="stat-label">التعليقات</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-bookmark"></i>
                        </div>
                        <div class="stat-value">{{ number_format($article->bookmarks_count ?? 0) }}</div>
                        <div class="stat-label">المفضلة</div>
                    </div>
                </div>

                <!-- الصورة الرئيسية -->
                @if ($article->featured_image)
                    <div class="featured-image-container">
                        <img src="{{ Storage::url($article->featured_image) }}"
                            alt="{{ $article->image_alt ?? $article->title }}" class="featured-image">
                        @if ($article->image_alt)
                            <div class="image-caption">
                                <i class="fas fa-camera me-2"></i>
                                {{ $article->image_alt }}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- الملخص -->
                @if ($article->excerpt)
                    <div class="alert"
                        style="background: rgba(105, 108, 255, 0.1); border: 1px solid var(--primary-color);">
                        <i class="fas fa-quote-right me-2" style="color: var(--primary-color);"></i>
                        {{ $article->excerpt }}
                    </div>
                @endif

                <!-- محتوى المقال -->
                <div class="article-content">
                    {!! $article->content !!}
                </div>

                <!-- الوسوم -->
                @if (!empty($article->tags) && is_array($article->tags))
                    <div class="tags-section">
                        <h6 class="tags-title">
                            <i class="fas fa-tags me-2"></i>
                            الوسوم
                        </h6>
                        <div class="tags-container">
                            @foreach ($article->tags as $tag)
                                <a href="{{ route('admin.articles.index', ['tag' => $tag]) }}" class="tag">
                                    <i class="fas fa-hashtag"></i>
                                    {{ $tag }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- الصور الإضافية -->
                @if ($article->images->count() > 0)
                    <div class="additional-images">
                        <h6 class="tags-title">
                            <i class="fas fa-images me-2"></i>
                            الصور الإضافية
                        </h6>
                        <div class="images-grid">
                            @foreach ($article->images as $image)
                                <div class="image-item" onclick="openImage('{{ Storage::url($image->image_path) }}')">
                                    <img src="{{ Storage::url($image->image_path) }}"
                                        alt="{{ $image->caption ?? 'صورة إضافية' }}">
                                    @if ($image->caption)
                                        <div class="image-item-caption">
                                            {{ $image->caption }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- معلومات إضافية -->
                <div class="info-section">
                    <h6 class="tags-title mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات إضافية
                    </h6>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-hashtag"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">ID المقال</div>
                                <div class="info-value">#{{ $article->id }}</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-link"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">الرابط (Slug)</div>
                                <div class="info-value">{{ $article->slug }}</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">تاريخ الإنشاء</div>
                                <div class="info-value">{{ $article->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">تاريخ النشر</div>
                                <div class="info-value">
                                    {{ $article->published_at ? $article->published_at->format('Y-m-d H:i') : 'غير منشور' }}
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-toggle-on"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">الحالة</div>
                                <div class="info-value">
                                    @if ($article->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-secondary">غير نشط</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">التعليقات</div>
                                <div class="info-value">
                                    @if ($article->allow_comments)
                                        <span class="badge bg-success">مسموح</span>
                                    @else
                                        <span class="badge bg-danger">ممنوع</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Information -->
                    <div class="seo-card mt-4">
                        <h6 class="tags-title mb-3">
                            <i class="fas fa-chart-line me-2"></i>
                            معلومات SEO
                        </h6>

                        @if ($article->meta_title)
                            <div class="seo-item">
                                <div class="seo-label">Meta Title</div>
                                <div class="seo-value">{{ $article->meta_title }}</div>
                            </div>
                        @endif

                        @if ($article->meta_description)
                            <div class="seo-item">
                                <div class="seo-label">Meta Description</div>
                                <div class="seo-value">{{ $article->meta_description }}</div>
                            </div>
                        @endif

                        @if (!empty($article->meta_keywords))
                            <div class="seo-item">
                                <div class="seo-label">Meta Keywords</div>
                                <div class="seo-value">
                                    @if (is_array($article->meta_keywords))
                                        {{ implode(' - ', $article->meta_keywords) }}
                                    @else
                                        {{ $article->meta_keywords }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- التعليقات -->
                @if ($article->comments_count > 0)
                    <div class="comments-section">
                        <h6 class="comments-title">
                            <i class="fas fa-comments me-2"></i>
                            التعليقات ({{ $article->comments_count }})
                        </h6>

                        @foreach ($article->comments as $comment)
                            <div class="comment">
                                <div class="comment-header">
                                    <div class="comment-author">
                                        <div class="comment-avatar d-flex align-items-center justify-content-center bg-secondary text-white">
                                            @if($comment->user && $comment->user->avatar)
                                                <img src="{{ asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->user->name }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                            @else
                                                <i class="fas fa-user" style="font-size: 14px;"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="comment-name">
                                                {{ $comment->user->name ?? ($comment->guest_name ?? 'زائر') }}
                                            </div>
                                            <div class="comment-date">
                                                {{ $comment->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="comment-actions">
                                        <span class="comment-action">
                                            <i class="fas fa-thumbs-up me-1"></i>
                                            {{ $comment->likes_count ?? 0 }}
                                        </span>
                                        <span class="comment-action">
                                            <i class="fas fa-reply"></i>
                                        </span>
                                        @if ($comment->status === 'pending')
                                            <span class="badge bg-warning">في انتظار المراجعة</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="comment-content">
                                    {{ $comment->content }}
                                </div>

                                @if ($comment->replies->count() > 0)
                                    @foreach ($comment->replies as $reply)
                                        <div class="comment-reply">
                                            <div class="comment-header">
                                                <div class="comment-author">
                                                     <div class="comment-avatar d-flex align-items-center justify-content-center bg-secondary text-white"
                                                         style="width: 30px; height: 30px;">
                                                         @if($reply->user && $reply->user->avatar)
                                                             <img src="{{ asset('storage/' . $reply->user->avatar) }}" alt="{{ $reply->user->name }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                                         @else
                                                             <i class="fas fa-user" style="font-size: 10px;"></i>
                                                         @endif
                                                     </div>
                                                    <div>
                                                        <div class="comment-name" style="font-size: 14px;">
                                                            {{ $reply->user->name ?? ($reply->guest_name ?? 'رد') }}
                                                        </div>
                                                        <div class="comment-date">
                                                            {{ $reply->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="comment-content" style="font-size: 14px;">
                                                {{ $reply->content }}
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- مقالات ذات صلة -->
                @if (!empty($article->related_articles) && is_array($article->related_articles))
                    <div class="related-articles">
                        <h6 class="comments-title">
                            <i class="fas fa-link me-2"></i>
                            مقالات ذات صلة
                        </h6>

                        <div class="related-grid">
                            @foreach ($article->related_articles as $relatedId)
                                @php
                                    $related = \App\Models\Article::find($relatedId);
                                @endphp
                                @if ($related)
                                    <a href="{{ route('admin.articles.show', $related) }}" class="related-card">
                                        @if ($related->featured_image)
                                            <img src="{{ Storage::url($related->featured_image) }}"
                                                alt="{{ $related->title }}" class="related-image">
                                        @else
                                            <div class="related-image"
                                                style="background: var(--primary-gradient); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-newspaper"
                                                    style="font-size: 40px; color: rgba(255,255,255,0.5);"></i>
                                            </div>
                                        @endif

                                        <div class="related-content">
                                            <h6 class="related-title">{{ Str::limit($related->title, 50) }}</h6>
                                            <div class="related-meta">
                                                <span><i class="fas fa-eye me-1"></i>
                                                    {{ number_format($related->views_count) }}</span>
                                                <span><i class="fas fa-calendar me-1"></i>
                                                    {{ $related->created_at->format('Y/m/d') }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- أزرار الإجراءات السريعة -->
    <div class="action-buttons">
        <a href="{{ route('admin.articles.edit', $article) }}" class="action-btn edit" title="تعديل المقال">
            <i class="fas fa-edit"></i>
        </a>

        <a href="{{ route('admin.articles.create') }}" class="action-btn" style="background: var(--success-color);"
            title="إضافة مقال جديد">
            <i class="fas fa-plus"></i>
        </a>

        <button class="action-btn" onclick="window.print()" title="طباعة">
            <i class="fas fa-print"></i>
        </button>

        <button class="action-btn delete" onclick="confirmDelete({{ $article->id }})" title="حذف المقال">
            <i class="fas fa-trash"></i>
        </button>

        <a href="{{ route('admin.articles.index') }}" class="action-btn back" title="العودة للقائمة">
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <!-- Modal for images -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background: var(--dark-card);">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img src="" id="modalImage" style="max-width: 100%; max-height: 80vh;" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // فتح الصورة في modal
        function openImage(imageUrl) {
            $('#modalImage').attr('src', imageUrl);
            $('#imageModal').modal('show');
        }

        // تأكيد الحذف
        function confirmDelete(articleId) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف المقال نهائياً ولا يمكن التراجع عن هذا الإجراء",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.articles.destroy', $article) }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: response.success || 'تم حذف المقال بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '{{ route('admin.articles.index') }}';
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: 'حدث خطأ أثناء الحذف',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        }

        // نسخ الرابط
        function copySlug() {
            const slug = '{{ $article->slug }}';
            navigator.clipboard.writeText(slug).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'تم النسخ',
                    text: 'تم نسخ الرابط',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        }

        // تحديث حالة التفعيل
        function toggleStatus() {
            $.ajax({
                url: '{{ route('admin.articles.toggle-status', $article) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PATCH'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التحديث',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        // تحديث حالة التمييز
        function toggleFeatured() {
            $.ajax({
                url: '{{ route('admin.articles.toggle-featured', $article) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PATCH'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التحديث',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        // رسائل التنبيه من الجلسة
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'نجاح',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: "{{ session('error') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>
@endsection
