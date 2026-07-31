import.meta.glob(['../images/**']);

/**
 * Lightweight public-layout enhancements.
 *
 * Adds a subtle shadow to the navbar once the user scrolls down, and a
 * hover-lift effect to any element carrying the `custom-shadow` class.
 * These are progressive enhancements only — the layout is fully usable
 * without JavaScript.
 */
document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('[data-public-nav]');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('shadow-md', 'bg-surface/95');
                navbar.classList.remove('bg-surface/80');
            } else {
                navbar.classList.remove('shadow-md', 'bg-surface/95');
                navbar.classList.add('bg-surface/80');
            }
        });
    }

    document.querySelectorAll('.custom-shadow').forEach((card) => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-4px)';
            card.style.transition = 'transform 0.3s cubic-bezier(0.2, 0, 0, 1)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });
});
