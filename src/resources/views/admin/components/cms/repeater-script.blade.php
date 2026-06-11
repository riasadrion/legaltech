
<script>
$(function () {
    'use strict';

    $('[data-repeater-add]').on('click', function () {
        var id = $(this).data('repeater-add');
        var $container = $('#' + id);
        var $template = $('#' + id + '-template');
        if (!$template.length) return;

        var nextIndex = $container.children('.repeater-row').length;
        var html = $template.html().replace(/__INDEX__/g, nextIndex);
        var $row = $(html);
        $container.append($row);

        // Initialize any rich text editors that were just inserted.
        if (window.ProseEditor) {
            $row.find('.prose-editor-init').each(function () {
                ProseEditor.init(this);
            });
        }
    });

    $(document).on('click', '.repeater-remove', function () {
        $(this).closest('.repeater-row').remove();
    });
});
</script>
