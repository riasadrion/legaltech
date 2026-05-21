<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <p class="text-7xl font-bold text-brand-600">503</p>
        <h1 class="mt-4 text-2xl font-semibold text-gray-900">Under Maintenance</h1>
        <p class="mt-2 text-gray-500">
            We are performing scheduled maintenance. We'll be back shortly.
        </p>
    </div>
</body>
</html>
