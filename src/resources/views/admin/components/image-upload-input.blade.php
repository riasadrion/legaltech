@props(['name', 'label' => null, 'current' => null, 'maxWidth' => 1920, 'quality' => 0.8])

<div class="image-upload-field" data-name="{{ $name }}" data-max-width="{{ $maxWidth }}" data-quality="{{ $quality }}">
    @if($label)
        <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
        </label>
    @endif

    @if($current)
        <div class="mb-4">
            <img src="{{ $current }}" alt="Current image" class="max-w-xs h-auto rounded border">
            <button type="button" class="mt-2 text-sm text-red-600 hover:text-red-800 remove-image" data-name="{{ $name }}">
                Remove current image
            </button>
        </div>
    @endif

    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-gray-400 transition file-drop-zone"
         data-name="{{ $name }}">
        <input type="file" name="{{ $name }}" class="hidden image-input" accept="image/*" data-name="{{ $name }}">

        <div class="upload-placeholder">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20m-12-8v8m0 0l-3-3m3 3l3-3m-12 14a2 2 0 100-4 2 2 0 000 4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <p class="mt-2 text-sm text-gray-600">
                <span class="font-medium text-blue-600">Click to upload</span> or drag and drop
            </p>
            <p class="text-xs text-gray-500">PNG, JPG, WebP up to 4MB</p>
        </div>

        <div class="upload-progress hidden">
            <div class="flex items-center justify-center space-x-2">
                <div class="animate-spin">
                    <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <p class="text-sm text-gray-600">Resizing image...</p>
            </div>
        </div>
    </div>

    <p class="mt-2 text-xs text-gray-500">
        <span class="file-info hidden">
            File size: <span class="file-size">0 MB</span>
        </span>
    </p>
</div>

<script>
    $(function () {
        'use strict';

        const field = $('[data-name="{{ $name }}"]');
        const fileInput = field.find('[data-name="{{ $name }}"]');
        const dropZone = field.find('[data-name="{{ $name }}"].file-drop-zone');
        const maxWidth = parseInt(dropZone.data('max-width'), 10);
        const quality = parseFloat(dropZone.data('quality'));

        // Click to open file picker
        dropZone.on('click', function () {
            fileInput.click();
        });

        // Prevent default drag behavior
        $(document).on('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
        });

        // Drag and drop
        dropZone.on('dragover', function (e) {
            e.preventDefault();
            $(this).addClass('border-blue-400 bg-blue-50');
        });

        dropZone.on('dragleave', function () {
            $(this).removeClass('border-blue-400 bg-blue-50');
        });

        dropZone.on('drop', function (e) {
            e.preventDefault();
            $(this).removeClass('border-blue-400 bg-blue-50');

            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                fileInput[0].files = files;
                handleFile(files[0]);
            }
        });

        // File input change
        fileInput.on('change', function () {
            if (this.files.length > 0) {
                handleFile(this.files[0]);
            }
        });

        async function handleFile(file) {
            // Validate MIME type
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                (window.toast || alert)('Please upload a JPEG, PNG, or WebP image.', 'error');
                fileInput.val('');
                return;
            }

            // Show progress
            dropZone.find('.upload-placeholder').addClass('hidden');
            dropZone.find('.upload-progress').removeClass('hidden');

            try {
                // Resize image
                const resizedBlob = await ImageResizer.resize(file, maxWidth, quality);

                // Create a new File object from the Blob
                const resizedFile = new File([resizedBlob], file.name, {
                    type: file.type,
                    lastModified: Date.now(),
                });

                // Update file input with resized file
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(resizedFile);
                fileInput[0].files = dataTransfer.files;

                // Show file info
                const fileSizeMb = ImageResizer.getFileSizeMb(resizedBlob);
                field.find('.file-info').removeClass('hidden');
                field.find('.file-size').text(fileSizeMb);

                // Hide progress, show result
                dropZone.find('.upload-progress').addClass('hidden');
                dropZone.find('.upload-placeholder').addClass('hidden');

                const resultHtml = `
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">
                            <strong>${file.name}</strong> (${fileSizeMb} MB)
                        </span>
                        <button type="button" class="text-xs text-blue-600 hover:text-blue-800 change-image" data-name="{{ $name }}">
                            Change
                        </button>
                    </div>
                `;
                dropZone.html(resultHtml);
            } catch (error) {
                console.error('Image resize failed:', error);
                (window.toast || alert)('Failed to resize image. Please try again.', 'error');
                dropZone.find('.upload-placeholder').removeClass('hidden');
                dropZone.find('.upload-progress').addClass('hidden');
                fileInput.val('');
            }
        }

        // Change image button
        $(document).on('click', '[data-name="{{ $name }}"].change-image', function () {
            fileInput.val('');
            dropZone.html(`
                <div class="upload-placeholder">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20m-12-8v8m0 0l-3-3m3 3l3-3m-12 14a2 2 0 100-4 2 2 0 000 4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">
                        <span class="font-medium text-blue-600">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-xs text-gray-500">PNG, JPG, WebP up to 4MB</p>
                </div>
            `);
            field.find('.file-info').addClass('hidden');
        });

        // Remove current image button
        field.find('.remove-image').on('click', function () {
            $(this).parent().remove();
            const removeInput = $('<input type="hidden" name="{{ $name }}_remove" value="1">');
            field.append(removeInput);
        });
    });
</script>
