<?php

namespace Database\Seeders;

use App\Domain\Blog\Models\BlogCategory;
use App\Domain\Blog\Models\BlogPost;
use App\Domain\Blog\Models\Tag;
use App\Domain\Cms\Sanitizer\HtmlPurifier;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $tech = BlogCategory::firstOrNew(['slug' => 'technology']);
        $tech->name = ['en' => 'Technology', 'ar' => 'تقنية'];
        $tech->sort = 1;
        $tech->save();

        $news = BlogCategory::firstOrNew(['slug' => 'news']);
        $news->name = ['en' => 'News'];
        $news->sort = 2;
        $news->save();

        // Tags
        $tagLaravel = Tag::firstOrNew(['slug' => 'laravel']);
        $tagLaravel->name = ['en' => 'Laravel'];
        $tagLaravel->save();

        $tagTips = Tag::firstOrNew(['slug' => 'tips']);
        $tagTips->name = ['en' => 'Tips'];
        $tagTips->save();

        // Posts
        $post1 = BlogPost::firstOrNew(['slug' => 'welcome-to-our-blog']);
        $post1->blog_category_id = $tech->id;
        $post1->title = ['en' => 'Welcome to Our Blog', 'ar' => 'مرحباً بكم في مدوّنتنا'];
        $post1->excerpt = ['en' => 'We\'re excited to launch our new blog.', 'ar' => 'يسعدنا إطلاق مدوّنتنا الجديدة.'];
        $post1->body = [
            'en' => HtmlPurifier::purify('<p>Welcome to our blog! We\'ll be sharing news, tips, and tutorials here regularly.</p>'),
            'ar' => HtmlPurifier::purify('<p>مرحباً بكم في مدوّنتنا! سنشارك هنا الأخبار والنصائح والدروس بانتظام.</p>'),
        ];
        $post1->status = BlogPost::STATUS_PUBLISHED;
        $post1->published_at = now()->subDays(5);
        $post1->save();
        $post1->tags()->sync([$tagLaravel->id]);

        $post2 = BlogPost::firstOrNew(['slug' => 'getting-started-with-laravel']);
        $post2->blog_category_id = $tech->id;
        $post2->title = ['en' => 'Getting Started with Laravel'];
        $post2->excerpt = ['en' => 'A beginner\'s guide to building web applications with Laravel.'];
        $post2->body = ['en' => HtmlPurifier::purify('<p>Laravel is a web application framework with expressive, elegant syntax. In this guide we\'ll cover the basics.</p><p>Follow along to set up your first Laravel project from scratch.</p>')];
        $post2->status = BlogPost::STATUS_PUBLISHED;
        $post2->published_at = now()->subDays(2);
        $post2->save();
        $post2->tags()->sync([$tagLaravel->id, $tagTips->id]);

        $post3 = BlogPost::firstOrNew(['slug' => 'product-update-may-2026']);
        $post3->blog_category_id = $news->id;
        $post3->title = ['en' => 'Product Update — May 2026'];
        $post3->excerpt = ['en' => 'Here\'s what\'s new this month.'];
        $post3->body = ['en' => HtmlPurifier::purify('<p>This month we shipped several improvements including a new payment gateway, KYC flow, and this very blog.</p>')];
        $post3->status = BlogPost::STATUS_SCHEDULED;
        $post3->published_at = now()->addDays(2);
        $post3->save();
    }
}
