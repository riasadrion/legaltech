(function () {
    'use strict';

    function initPhoneInput(wrap) {
        var trigger  = wrap.querySelector('[data-phone-trigger]');
        var dropdown = wrap.querySelector('[data-phone-dropdown]');
        var search   = wrap.querySelector('[data-phone-search]');
        var list     = wrap.querySelector('[data-phone-list]');
        var dialSpan = wrap.querySelector('[data-phone-dial]');
        var codeInput= wrap.querySelector('input[type="hidden"]');

        if (!trigger || !dropdown) return;

        function open() {
            dropdown.classList.remove('hidden');
            trigger.setAttribute('aria-expanded', 'true');
            if (search) { search.value = ''; search.focus(); filterList(''); }
        }

        function close() {
            dropdown.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
        }

        function filterList(q) {
            var items = list.querySelectorAll('.phone-option');
            items.forEach(function (li) {
                var match = li.dataset.name.indexOf(q.toLowerCase()) !== -1
                         || li.dataset.dial.indexOf(q) !== -1;
                li.style.display = match ? '' : 'none';
            });
        }

        function select(li) {
            var dial = li.dataset.dial;
            // update hidden input
            codeInput.value = dial;
            // update button display
            dialSpan.textContent = dial;
            // update selected state
            list.querySelectorAll('.phone-option').forEach(function (el) {
                el.classList.toggle('is-selected', el === li);
            });
            close();
        }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.contains('hidden') ? open() : close();
        });

        if (search) {
            search.addEventListener('input', function () { filterList(this.value); });
            search.addEventListener('click', function (e) { e.stopPropagation(); });
        }

        list.addEventListener('click', function (e) {
            var li = e.target.closest('.phone-option');
            if (li) select(li);
        });

        document.addEventListener('click', function (e) {
            if (!wrap.contains(e.target)) close();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });
    }

    function init() {
        document.querySelectorAll('[data-phone-wrap]').forEach(initPhoneInput);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
