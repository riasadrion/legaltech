@php
    $gaEnabled = setting('integrations.ga.enabled') === '1';
    $gaId = setting('integrations.ga.measurement_id');
@endphp
@if($gaEnabled && $gaId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $gaId }}');
</script>
@endif
