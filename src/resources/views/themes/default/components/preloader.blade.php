
<div id="fe-preloader">
    <div id="fe-preloader-mark">
        <span class="fe-preloader-ring"></span>
        <span class="fe-preloader-dot"></span>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    'use strict';
    var $loader = $('#fe-preloader');
    if (!$loader.length) return;

    function finish() {
        setTimeout(function () { $loader.addClass('fe-preloader-done'); }, 300);
    }

    if (document.readyState === 'complete') {
        finish();
    } else {
        $(window).on('load', finish);
        setTimeout(finish, 4000);
    }
})();
</script>
@endpush
@endonce
