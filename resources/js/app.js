import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.querySelector('[data-nav-toggle]');
    const navPanel = document.querySelector('[data-nav-panel]');
    if (navToggle && navPanel) {
        navToggle.addEventListener('click', () => {
            const open = navPanel.classList.toggle('hidden') === false;
            navToggle.setAttribute('aria-expanded', String(open));
        });
    }

    const reveals = document.querySelectorAll('.reveal');
    if (reveals.length && 'IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) {
                    e.target.classList.add('is-visible');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.08 });
        reveals.forEach((el) => io.observe(el));
    } else {
        reveals.forEach((el) => el.classList.add('is-visible'));
    }

    const topnav = document.querySelector('[data-topnav]');
    if (topnav) {
        const onScroll = () => topnav.classList.toggle('shadow-soft', window.scrollY > 4);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }
});
