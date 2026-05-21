<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $htmlDir ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <p class="text-7xl font-bold text-brand-600">{{ $code }}</p>
        <h1 class="mt-4 text-2xl font-semibold text-gray-900">{{ $title }}</h1>
        <p class="mt-2 text-gray-500">{{ $message }}</p>
        <a href="{{ url('/') }}" class="mt-8 inline-block rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-700">
            Go home
        </a>
    </div>
</body>
</html>
