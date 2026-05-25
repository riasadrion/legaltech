<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview: {{ $page->getLocalTitle() }}</title>
    <?php echo \App\Support\ViteAssets::includeAssets(['resources/css/app.css']); ?>
</head>
<body class="bg-white">
@foreach($sections as $section)
    @if($section->enabled)
        <?php echo $section->renderFrontend(); ?>
    @endif
@endforeach
</body>
</html>
