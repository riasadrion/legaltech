<?php

use App\Http\Controllers\Frontend\PageController;
use Illuminate\Support\Facades\Route;

// CMS pages without locale prefix (e.g. /about, /cookie-policy)
// Must be registered LAST so it doesn't swallow /login, /register, etc.
Route::middleware(['language', 'maintenance'])
    ->get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[^/]+')
    ->name('page.show.bare');
