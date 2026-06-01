@php
    $tawkEnabled = setting('integrations.tawk.enabled') === '1';
    $propertyId = setting('integrations.tawk.property_id');
    $widgetId = setting('integrations.tawk.widget_id');
@endphp
@if($tawkEnabled && $propertyId && $widgetId)
<script>
    var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
    (function(){
        var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/{{ $propertyId }}/{{ $widgetId }}';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script>
@endif
