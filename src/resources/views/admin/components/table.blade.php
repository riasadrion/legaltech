@props(['striped' => false])

<div class="overflow-x-auto rounded-xl border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm {{ $striped ? '[&_tbody_tr:nth-child(even)]:bg-gray-50' : '' }}">
        {{ $slot }}
    </table>
</div>
