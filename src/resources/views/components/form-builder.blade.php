@props(['form' => null, 'locales' => ['en']])


<div id="form-builder" class="space-y-4" data-existing-fields='{{ $form ? $form->fields->toJson() : '[]' }}'>

    <div id="form-builder-empty" class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-400 {{ $form && $form->fields->count() > 0 ? 'hidden' : '' }}">
        No fields yet. Click "Add field" to start.
    </div>

    <div id="form-builder-fields" class="space-y-3">

    </div>

    <button type="button" id="form-builder-add-btn"
            class="flex items-center gap-1.5 rounded-lg border border-dashed border-brand-400 px-4 py-2 text-sm font-medium text-brand-600 hover:bg-brand-50">
        <x-heroicon-o-plus class="h-4 w-4" />
        Add field
    </button>
</div>

@push('scripts')
<script>
'use strict';

    'use strict';
    var $container = $('#form-builder');
    var $fieldsContainer = $('#form-builder-fields');
    var $emptyMessage = $('#form-builder-empty');
    
    if ($container.length === 0) {
        console.error('Form builder container not found');
        return;
    }
    
    var existingFields = [];
    var rawData = $container.attr('data-existing-fields') || '[]';
    
    try {
        existingFields = JSON.parse(rawData);
    } catch (e) {
        console.error('Failed to parse existing fields:', e);
        existingFields = [];
    }
    
    var fieldCounter = 0;
    var fields = [];
    
    // Initialize with existing fields
    if (existingFields.length > 0) {
        existingFields.forEach(function (f) {
            fields.push({
                _key: ++fieldCounter,
                name: f.name || '',
                type: f.type || 'text',
                label: f.label || {},
                required: f.required || false,
                options_raw: Array.isArray(f.options)
                    ? f.options.map(function (o) { return o.value + '|' + (o.label || o.value); }).join('\n')
                    : '',
                sort: f.sort || 0
            });
        });
        renderFields();
    }
    
    // Add field button
    $('#form-builder-add-btn').on('click', function (e) {
        e.preventDefault();
        fields.push({
            _key: ++fieldCounter,
            name: '',
            type: 'text',
            label: {},
            required: false,
            options_raw: '',
            sort: fields.length
        });
        renderFields();
    });
    
    // Remove field (delegated)
    $fieldsContainer.on('click', '.form-builder-remove', function () {
        var index = $(this).closest('.form-builder-field').data('index');
        fields.splice(index, 1);
        renderFields();
    });
    
    // Update field data on input change
    $fieldsContainer.on('input', '.field-name', function () {
        var index = $(this).closest('.form-builder-field').data('index');
        fields[index].name = $(this).val();
    });
    
    $fieldsContainer.on('change', '.field-type', function () {
        var index = $(this).closest('.form-builder-field').data('index');
        fields[index].type = $(this).val();
        renderFields();
    });
    
    $fieldsContainer.on('input', '.field-label', function () {
        var index = $(this).closest('.form-builder-field').data('index');
        var locale = $(this).data('locale');
        if (!fields[index].label) fields[index].label = {};
        fields[index].label[locale] = $(this).val();
    });
    
    $fieldsContainer.on('change', '.field-required', function () {
        var index = $(this).closest('.form-builder-field').data('index');
        fields[index].required = $(this).is(':checked');
    });
    
    $fieldsContainer.on('input', '.field-options', function () {
        var index = $(this).closest('.form-builder-field').data('index');
        fields[index].options_raw = $(this).val();
    });
    
    function renderFields() {
        $fieldsContainer.empty();
        
        if (fields.length === 0) {
            $emptyMessage.removeClass('hidden');
            return;
        } else {
            $emptyMessage.addClass('hidden');
        }
        
        fields.forEach(function (field, index) {
            var labelEn = field.label && field.label.en ? field.label.en : field.name || 'New field';
            var isSelect = field.type === 'select';
            
            var html = '<div class="form-builder-field rounded-xl border border-gray-200 bg-white shadow-sm" data-index="' + index + '" data-key="' + field._key + '">' +
                '<div class="flex items-center gap-2 border-b border-gray-100 px-4 py-3">' +
                    '<span class="drag-handle cursor-grab text-gray-300 hover:text-gray-500">' +
                        '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>' +
                    '</span>' +
                    '<span class="text-sm font-medium text-gray-700">' + escapeHtml(labelEn) + '</span>' +
                    '<span class="ml-2 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500">' + field.type + '</span>' +
                    '<button type="button" class="form-builder-remove ml-auto text-gray-400 hover:text-red-500">' +
                        '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>' +
                    '</button>' +
                '</div>' +
                '<div class="grid grid-cols-1 gap-4 px-4 py-4 sm:grid-cols-2">' +
                    // Field name
                    '<div>' +
                        '<label class="mb-1 block text-xs font-medium text-gray-600">Field name (slug)</label>' +
                        '<input type="text" name="fields[' + index + '][name]" value="' + escapeHtml(field.name) + '" placeholder="e.g. bank_account" ' +
                            'class="field-name h-9 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />' +
                    '</div>' +
                    // Field type
                    '<div>' +
                        '<label class="mb-1 block text-xs font-medium text-gray-600">Type</label>' +
                        '<select name="fields[' + index + '][type]" class="field-type h-9 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">' +
                            '<option value="text" ' + (field.type === 'text' ? 'selected' : '') + '>Text</option>' +
                            '<option value="number" ' + (field.type === 'number' ? 'selected' : '') + '>Number</option>' +
                            '<option value="email" ' + (field.type === 'email' ? 'selected' : '') + '>Email</option>' +
                            '<option value="textarea" ' + (field.type === 'textarea' ? 'selected' : '') + '>Textarea</option>' +
                            '<option value="select" ' + (field.type === 'select' ? 'selected' : '') + '>Select</option>' +
                            '<option value="file" ' + (field.type === 'file' ? 'selected' : '') + '>File upload</option>' +
                            '<option value="checkbox" ' + (field.type === 'checkbox' ? 'selected' : '') + '>Checkbox</option>' +
                        '</select>' +
                    '</div>';
            
            // Labels per locale
            @foreach($locales as $locale)
            html += '<div>' +
                '<label class="mb-1 block text-xs font-medium text-gray-600">Label ({{ strtoupper($locale) }})</label>' +
                '<input type="text" name="fields[' + index + '][label][{{ $locale }}]" ' +
                    'value="' + escapeHtml((field.label && field.label['{{ $locale }}']) ? field.label['{{ $locale }}'] : '') + '" ' +
                    'data-locale="{{ $locale }}" placeholder="Label shown to user" ' +
                    'class="field-label h-9 w-full rounded-lg border border-gray-300 px-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />' +
            '</div>';
            @endforeach
            
            // Required toggle
            html += '<div class="flex items-center gap-2 pt-4">' +
                '<input type="checkbox" name="fields[' + index + '][required]" id="req_' + field._key + '" ' + (field.required ? 'checked' : '') + ' ' +
                    'class="field-required h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />' +
                '<label for="req_' + field._key + '" class="text-sm text-gray-700">Required</label>' +
                '<input type="hidden" name="fields[' + index + '][sort]" value="' + index + '" />' +
            '</div>';
            
            // Select options (conditional)
            if (isSelect) {
                html += '<div class="sm:col-span-2">' +
                    '<label class="mb-1 block text-xs font-medium text-gray-600">Options (one per line, format: value|Label)</label>' +
                    '<textarea name="fields[' + index + '][options_raw]" rows="3" placeholder="bank_transfer|Bank Transfer&#10;crypto|Crypto" ' +
                        'class="field-options w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">' + escapeHtml(field.options_raw) + '</textarea>' +
                '</div>';
            }
            
            html += '</div></div>';
            
            $fieldsContainer.append(html);
        });
        
        // Initialize drag and drop if Sortable is available
        if (window.Sortable && $fieldsContainer.length) {
            if ($fieldsContainer.data('sortable')) {
                $fieldsContainer.data('sortable').destroy();
            }
            var sortable = new Sortable($fieldsContainer[0], {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function () {
                    // Reorder fields array based on DOM order
                    var newFields = [];
                    $fieldsContainer.find('.form-builder-field').each(function () {
                        var oldIndex = $(this).data('index');
                        newFields.push(fields[oldIndex]);
                    });
                    fields = newFields;
                    renderFields();
                }
            });
            $fieldsContainer.data('sortable', sortable);
        }
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endpush
