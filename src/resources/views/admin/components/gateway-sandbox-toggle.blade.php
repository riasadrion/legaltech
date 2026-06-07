@props(['gateway'])

<div class="flex items-center gap-2 pt-1">
    <input type="hidden" name="credentials[sandbox]" value="0">
    <input type="checkbox" name="credentials[sandbox]" value="1"
           id="sandbox_toggle"
           {{ ($gateway?->credential('sandbox')) ? 'checked' : '' }}
           class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />
    <label for="sandbox_toggle" class="text-sm text-gray-700">Sandbox / test mode</label>
</div>
