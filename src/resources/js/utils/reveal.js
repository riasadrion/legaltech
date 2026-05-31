'use strict';

// Lightweight scroll-reveal. Add data-reveal to any element.
// Optional: data-reveal="left|right|up" and data-reveal-delay="120" (ms).
export function initReveal() {
    const els = Array.from(document.querySelectorAll('[data-reveal]'));
    if (!els.length) return;

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReduced || !('IntersectionObserver' in window)) {
        els.forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    els.forEach((el) => {
        const delay = el.getAttribute('data-reveal-delay');
        if (delay) el.style.transitionDelay = delay + 'ms';
    });

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.35 }
    );

    els.forEach((el) => observer.observe(el));
}
