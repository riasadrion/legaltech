<?php

use App\Domain\Blog\Models\BlogPost;
use App\Domain\Cms\Models\Page;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\GuestTicketController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PlansController;
use App\Http\Controllers\UnsubscribeController;
use Illuminate\Support\Facades\Route;

Route::prefix('{locale?}')
    ->where(['locale' => '[a-z]{2}(-[A-Z]{2})?'])
    ->middleware(['language', 'maintenance'])
    ->group(function () {
        Route::get('/', [PageController::class, 'home'])->name('home');

        // Contact
        Route::get('/contact', [ContactController::class, 'show'])->name('contact');
        Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

        // Blog
        Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
        Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
        Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
        Route::get('/blog/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');

        // Plans
        Route::get('/plans', [PlansController::class, 'index'])->name('plans');

        // Newsletter
        Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
        Route::get('/newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
        Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

        // Guest support tickets
        Route::get('/support', [GuestTicketController::class, 'create'])->name('support.guest.create');
        Route::post('/support', [GuestTicketController::class, 'store'])->name('support.guest.store');
        Route::get('/support/verify/{token}', [GuestTicketController::class, 'verify'])->name('support.guest.verify');
        Route::get('/support/ticket/{reference}', [GuestTicketController::class, 'show'])->name('support.guest.show');
        Route::post('/support/ticket/{reference}/reply', [GuestTicketController::class, 'reply'])->name('support.guest.reply');

        // CMS pages with locale prefix (e.g. /ar/about)
        Route::get('/{slug}', [PageController::class, 'show'])->where('slug', '[^/]+')->name('page.show');
    });

// Email unsubscribe (no locale prefix — linked from emails)
Route::get('/unsubscribe', [UnsubscribeController::class, 'show'])->name('notifications.unsubscribe');
Route::post('/unsubscribe', [UnsubscribeController::class, 'handle'])->name('notifications.unsubscribe.handle');

// Sitemap, robots & RSS (no locale prefix)
Route::get('/sitemap.xml', function () {
    $pages = Page::published()->get();
    $posts = BlogPost::published()->latest('published_at')->get();

    return response()->view('theme::sitemap', compact('pages', 'posts'))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/blog/feed.rss', [BlogController::class, 'rss'])->name('blog.rss');

Route::get('/robots.txt', function () {
    return response()->view('theme::robots')
        ->header('Content-Type', 'text/plain');
})->name('robots');
