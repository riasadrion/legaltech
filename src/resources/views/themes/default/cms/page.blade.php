@extends('theme::layouts.frontend')

@section('title', $page->getLocalMetaTitle())
@section('meta_description', $page->getLocalMetaDescription())

@section('content')
@foreach($sections as $section)
    @if($section->enabled)
        <?php echo $section->renderFrontend(); ?>
    @endif
@endforeach
@endsection
