
<script>
$(function () {
    'use strict';

    $('[data-repeater-add]').on('click', function () {
        var id = $(this).data('repeater-add');
        var $container = $('#' + id);
        var template = document.getElementById(id + '-template');
        if (!template) return;

        var nextIndex = $container.children('.repeater-row').length;
        var html = template.innerHTML.replace(/__INDEX__/g, nextIndex);
        var $row = $(html);
        $container.append($row);

        // Initialize any rich text editors that were just inserted.
        if (window.ProseEditor) {
            $row.find('.prose-editor-init').each(function () {
    'use strict';
                ProseEditor.init(this);
            });
        }
    });

    $(document).on('click', '.repeater-remove', function () {
        $(this).closest('.repeater-row').remove();
    });
});
</script>
