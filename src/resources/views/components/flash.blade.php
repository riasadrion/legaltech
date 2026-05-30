@php
    $messages = [];
    foreach (['success', 'error', 'warning', 'info'] as $type) {
        if (session($type)) {
            $messages[] = ['type' => $type, 'message' => session($type)];
        }
    }
@endphp

@if (!empty($messages))
<script>
(function () {
    'use strict';
    var queue = @json($messages);
    function flush() {
        if (!window.toast) {
            setTimeout(flush, 50);
            return;
        }
        queue.forEach(function (m) { window.toast(m.message, m.type); });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', flush);
    } else {
        flush();
    }
}());
</script>
@endif
