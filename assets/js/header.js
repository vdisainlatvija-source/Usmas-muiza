(function() {
    const header = document.getElementById('mainHeader');
    if (!header) return;

    const burgerBtn = header.querySelector('.burger-btn');
    const closeBtn = header.querySelector('.close-btn');
    const mobileSidebar = header.querySelector('.mobile-sidebar');
    const themeColor = document.getElementById('themeColor');

    let ticking = false;

    // Scroll
    function handleScroll() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
            if (themeColor) themeColor.setAttribute('content', '#ffffff');
        } else {
            header.classList.remove('scrolled');
            if (themeColor) themeColor.setAttribute('content', '#C8C5B5');
        }
    }

    function onScroll() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                handleScroll();
                ticking = false;
            });
            ticking = true;
        }
    }

    // Mobile menu
    function openMobileMenu() {
        header.classList.add('menu-open');
        if (mobileSidebar) mobileSidebar.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        header.classList.remove('menu-open');
        if (mobileSidebar) mobileSidebar.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Events
    window.addEventListener('scroll', onScroll, { passive: true });
    handleScroll();

    if (burgerBtn) burgerBtn.addEventListener('click', openMobileMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMobileMenu);

    if (mobileSidebar) {
        mobileSidebar.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', closeMobileMenu);
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (header.classList.contains('menu-open')) closeMobileMenu();
        }
    });
})();
