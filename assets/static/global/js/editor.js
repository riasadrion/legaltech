// Simple editor — contenteditable + execCommand
// Realtime toolbar state via selectionchange

var ProseEditor = {
    init: function (el) {
        if (!el || el.dataset.proseEditor) return

        var inputId = el.dataset.input
        var input = document.getElementById(inputId)
        if (!input) return

        var wrapper = document.createElement('div')
        wrapper.className = 'prose-editor'

        var toolbar = document.createElement('div')
        toolbar.className = 'prose-toolbar'
        toolbar.innerHTML =
            '<button type="button" data-cmd="bold" title="Bold" style="font-weight:bold">B</button>' +
            '<button type="button" data-cmd="italic" title="Italic"><i>I</i></button>' +
            '<button type="button" data-cmd="underline" title="Underline"><u>U</u></button>' +
            '<button type="button" data-cmd="strikeThrough" title="Strikethrough"><s>S</s></button>' +
            '<span class="separator"></span>' +
            '<button type="button" data-cmd="formatBlock" data-val="h1" title="Heading 1">H1</button>' +
            '<button type="button" data-cmd="formatBlock" data-val="h2" title="Heading 2">H2</button>' +
            '<button type="button" data-cmd="formatBlock" data-val="h3" title="Heading 3">H3</button>' +
            '<button type="button" data-cmd="formatBlock" data-val="p" title="Paragraph">P</button>' +
            '<span class="separator"></span>' +
            '<button type="button" data-cmd="insertUnorderedList" title="Bullet List">☰</button>' +
            '<button type="button" data-cmd="insertOrderedList" title="Numbered List">1.</button>' +
            '<span class="separator"></span>' +
            '<button type="button" data-cmd="formatBlock" data-val="blockquote" title="Quote">"</button>' +
            '<button type="button" data-cmd="formatBlock" data-val="pre" title="Code">&lt;/&gt;</button>' +
            '<button type="button" data-cmd="createLink" title="Link">🔗</button>'

        var content = document.createElement('div')
        content.className = 'prose-content'
        content.contentEditable = true
        content.innerHTML = input.value || ''

        wrapper.appendChild(toolbar)
        wrapper.appendChild(content)
        el.parentNode.replaceChild(wrapper, el)
        el.dataset.proseEditor = '1'

        // Toolbar clicks
        toolbar.addEventListener('click', function (e) {
            var btn = e.target.closest('button')
            if (!btn) return
            var cmd = btn.dataset.cmd
            var val = btn.dataset.val || null
            content.focus()

            if (cmd === 'createLink') {
                var sel = window.getSelection()
                if (sel.toString()) {
                    var url = prompt('Enter URL:', 'https://')
                    if (url) document.execCommand('createLink', false, url)
                } else {
                    var text = prompt('Link text:')
                    if (!text) return
                    var url = prompt('Enter URL:', 'https://')
                    if (url) {
                        document.execCommand('insertHTML', false, '<a href="' + url + '">' + text + '</a>')
                    }
                }
                return
            }

            document.execCommand(cmd, false, val)
            content.focus()
        })

        // Sync content to hidden input
        var sync = function () {
            input.value = content.innerHTML
        }
        content.addEventListener('input', sync)

        // Realtime toolbar state
        function updateToolbar() {
            toolbar.querySelectorAll('button[data-cmd]').forEach(function (btn) {
                var cmd = btn.dataset.cmd
                var val = btn.dataset.val
                var active = false

                if (cmd === 'formatBlock') {
                    var block = document.queryCommandValue('formatBlock')
                    if (block) {
                        block = block.replace(/[<>]/g, '').toLowerCase()
                        active = block === val
                    }
                } else if (cmd === 'createLink') {
                    active = false // link button doesn't have a useful state
                } else {
                    active = document.queryCommandState(cmd)
                }

                btn.classList.toggle('active', active)
            })
        }

        document.addEventListener('selectionchange', function () {
            if (document.activeElement === content || content.contains(document.activeElement)) {
                updateToolbar()
            }
        })
    }
}
