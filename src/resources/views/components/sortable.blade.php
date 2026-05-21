@props(['id', 'url' => '', 'handle' => '.sortable-handle'])

<div id="{{ $id }}"
     class="sortable-list"
     data-sortable-url="{{ $url }}"
     data-sortable-handle="{{ $handle }}">
    {{ $slot }}
</div>
