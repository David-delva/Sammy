import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

function animateCounter(element) {
    if (element.dataset.countAnimated === 'true') {
        return;
    }

    const target = Number(element.dataset.count ?? 0);

    if (Number.isNaN(target)) {
        return;
    }

    element.dataset.countAnimated = 'true';

    if (prefersReducedMotion.matches) {
        element.textContent = target.toLocaleString();
        return;
    }

    const duration = 900;
    const startTime = performance.now();

    const step = (currentTime) => {
        const progress = Math.min((currentTime - startTime) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        element.textContent = Math.round(target * eased).toLocaleString();

        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };

    window.requestAnimationFrame(step);
}

function setupAutoRevealTargets() {
    const selectors = [
        '.page-shell > *',
        '.card',
        '.surface-card',
        '.stat-card',
        '.action-card',
        '.detail-panel',
        '.empty-state',
        '.auth-panel',
    ];

    document.querySelectorAll(selectors.join(', ')).forEach((element) => {
        if (!element.hasAttribute('data-reveal') && !element.closest('[data-no-reveal]')) {
            element.setAttribute('data-reveal', '');
        }
    });
}

function setupRevealAnimations() {
    const revealTargets = Array.from(document.querySelectorAll('[data-reveal]'));

    if (!revealTargets.length) {
        return;
    }

    revealTargets.forEach((element, index) => {
        const delay = element.dataset.revealDelay ?? `${Math.min(index % 6, 5) * 70}ms`;
        element.style.setProperty('--reveal-delay', delay);
        element.classList.add('reveal-enter');
    });

    if (prefersReducedMotion.matches || !('IntersectionObserver' in window)) {
        revealTargets.forEach((element) => {
            element.classList.remove('reveal-enter');
            element.classList.add('reveal-visible');
        });
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.remove('reveal-enter');
                entry.target.classList.add('reveal-visible');
                observer.unobserve(entry.target);
            });
        },
        {
            threshold: 0.14,
            rootMargin: '0px 0px -32px 0px',
        },
    );

    revealTargets.forEach((element) => observer.observe(element));
}

function setupCounters() {
    const counters = Array.from(document.querySelectorAll('[data-count]'));

    if (!counters.length) {
        return;
    }

    if (prefersReducedMotion.matches || !('IntersectionObserver' in window)) {
        counters.forEach((element) => animateCounter(element));
        return;
    }

    const counterObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            });
        },
        {
            threshold: 0.35,
        },
    );

    counters.forEach((element) => counterObserver.observe(element));
}

function setupTiltEffects() {
    if (prefersReducedMotion.matches || !window.matchMedia('(pointer: fine)').matches) {
        return;
    }

    document.querySelectorAll('[data-tilt]').forEach((element) => {
        const reset = () => {
            element.style.transform = '';
        };

        element.addEventListener('pointermove', (event) => {
            const rect = element.getBoundingClientRect();
            const rotateX = ((event.clientY - rect.top) / rect.height - 0.5) * -8;
            const rotateY = ((event.clientX - rect.left) / rect.width - 0.5) * 10;

            element.style.transform = `perspective(1200px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) translateY(-4px)`;
        });

        element.addEventListener('pointerleave', reset);
        element.addEventListener('pointercancel', reset);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setupAutoRevealTargets();
    setupRevealAnimations();
    setupCounters();
    setupTiltEffects();
});
