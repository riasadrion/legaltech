'use strict';

export const confirmModal = {
    onConfirm: null,

    show(title, message, onConfirm) {
        const modal = document.getElementById('confirm-modal');
        if (!modal) {
            if (window.confirm(title + '\n\n' + message)) {
                onConfirm && onConfirm();
            }
            return;
        }
        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-message').textContent = message;
        this.onConfirm = onConfirm;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    },

    close() {
        const modal = document.getElementById('confirm-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    },
};

const TOAST_VARIANTS = {
    success: {
        accent: 'bg-emerald-500',
        iconBg: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
    },
    error: {
        accent: 'bg-red-500',
        iconBg: 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-300',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
    },
    warning: {
        accent: 'bg-amber-500',
        iconBg: 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>',
    },
    info: {
        accent: 'bg-blue-500',
        iconBg: 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-300',
        icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>',
    },
};

function ensureToastContainer() {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 end-4 z-[100] flex flex-col gap-3 pointer-events-none';
        document.body.appendChild(container);
    }
    return container;
}

export function toast(message, type = 'info') {
    const variant = TOAST_VARIANTS[type] || TOAST_VARIANTS.info;
    const container = ensureToastContainer();

    const el = document.createElement('div');
    el.className = [
        'pointer-events-auto relative flex items-center gap-3 w-80 max-w-[calc(100vw-2rem)]',
        'rounded-xl border border-gray-200 dark:border-slate-700',
        'bg-white dark:bg-slate-800',
        'shadow-lg shadow-black/5 dark:shadow-black/40',
        'px-4 py-3 pe-10 overflow-hidden',
    ].join(' ');
    el.style.opacity = '0';
    el.style.transform = 'translateX(8px)';
    el.style.transition = 'opacity 220ms ease, transform 220ms ease';

    el.innerHTML = `
        <span class="absolute inset-y-0 start-0 w-1 ${variant.accent}"></span>
        <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-full ${variant.iconBg}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${variant.icon}
            </svg>
        </span>
        <p class="flex-1 text-sm leading-snug text-gray-800 dark:text-slate-100"></p>
        <button type="button" class="absolute top-1/2 -translate-y-1/2 end-2 inline-flex items-center justify-center w-6 h-6 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:text-slate-500 dark:hover:text-slate-300 dark:hover:bg-slate-700 transition-colors" aria-label="Close">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    el.querySelector('p').textContent = message;

    const dismiss = () => {
        el.style.opacity = '0';
        el.style.transform = 'translateX(8px)';
        setTimeout(() => el.remove(), 220);
    };
    el.querySelector('button').addEventListener('click', dismiss);

    container.appendChild(el);

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateX(0)';
        });
    });

    setTimeout(dismiss, 4000);
}
