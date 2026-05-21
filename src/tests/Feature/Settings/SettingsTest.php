<?php

use App\Domain\Settings\Repositories\DatabaseSettingsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

test('settings can be written and read back', function () {
    $repo = app(DatabaseSettingsRepository::class);

    $repo->set('site.name', 'My SaaS', 'string', 'general', true);

    expect($repo->string('site.name'))->toBe('My SaaS');
});

test('settings bool helper casts correctly', function () {
    $repo = app(DatabaseSettingsRepository::class);

    $repo->set('feature.enabled', true, 'boolean');

    expect($repo->bool('feature.enabled'))->toBeTrue();
});

test('settings integer helper casts correctly', function () {
    $repo = app(DatabaseSettingsRepository::class);

    $repo->set('upload.max_mb', 10, 'integer');

    expect($repo->integer('upload.max_mb'))->toBe(10);
});

test('autoloaded settings are cached', function () {
    $repo = app(DatabaseSettingsRepository::class);
    $repo->set('site.name', 'Cached SaaS', 'string', 'general', true);

    Cache::flush();

    expect($repo->string('site.name'))->toBe('Cached SaaS');
    expect(Cache::has('settings:autoload'))->toBeTrue();
});

test('settings cache is flushed on write', function () {
    $repo = app(DatabaseSettingsRepository::class);
    $repo->set('site.name', 'First', 'string', 'general', true);

    // prime cache
    $repo->string('site.name');
    expect(Cache::has('settings:autoload'))->toBeTrue();

    $repo->set('site.name', 'Second', 'string', 'general', true);
    expect(Cache::has('settings:autoload'))->toBeFalse();
    expect($repo->string('site.name'))->toBe('Second');
});

test('missing setting returns default', function () {
    $repo = app(DatabaseSettingsRepository::class);

    expect($repo->string('nonexistent.key', 'fallback'))->toBe('fallback');
    expect($repo->bool('nonexistent.flag', false))->toBeFalse();
    expect($repo->integer('nonexistent.int', 42))->toBe(42);
});
