/**
 * Bazaar Burger Menu
 * Active on screens <= 900px
 */
(function () {
    'use strict';

    const nav = document.querySelector('.main-nav');
    const headcon = document.querySelector('.headcon');
    const body = document.body;

    if (!nav || !headcon) {
        return;
    }

    // Create burger button
    const burgerBtn = document.createElement('button');
    burgerBtn.className = 'burger-toggle';
    burgerBtn.setAttribute('aria-label', 'Toggle navigation menu');
    burgerBtn.setAttribute('aria-expanded', 'false');

    burgerBtn.innerHTML =
        '<span class="burger-line"></span>' +
        '<span class="burger-line"></span>' +
        '<span class="burger-line"></span>';

    const logo = headcon.querySelector('#logo-holder');

    if (logo && logo.nextSibling) {
        headcon.insertBefore(burgerBtn, logo.nextSibling);
    } else {
        headcon.insertBefore(burgerBtn, nav);
    }

    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'mobile-overlay';
    overlay.setAttribute('aria-hidden', 'true');
    body.appendChild(overlay);

    let isOpen = false;

    function openMenu() {
        isOpen = true;

        nav.classList.add('open');
        burgerBtn.classList.add('active');
        overlay.classList.add('active');

        burgerBtn.setAttribute('aria-expanded', 'true');
        overlay.setAttribute('aria-hidden', 'false');

        body.style.overflow = 'hidden';
    }

    function closeMenu() {
        isOpen = false;

        nav.classList.remove('open');
        burgerBtn.classList.remove('active');
        overlay.classList.remove('active');

        burgerBtn.setAttribute('aria-expanded', 'false');
        overlay.setAttribute('aria-hidden', 'true');

        body.style.overflow = '';

        document.querySelectorAll('.has-submenu.submenu-open').forEach(function (item) {
            item.classList.remove('submenu-open');
        });
    }

    function toggleMenu() {
        if (isOpen) {
            closeMenu();
        } else {
            openMenu();
        }
    }

    burgerBtn.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', closeMenu);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && isOpen) {
            closeMenu();
            burgerBtn.focus();
        }
    });

    // Mobile submenu accordion
    const submenuParents = nav.querySelectorAll(':scope > ul > li.has-submenu');

    submenuParents.forEach(function (parentLi) {
        const toggleLink = parentLi.querySelector(':scope > a');

        if (!toggleLink) {
            return;
        }

        toggleLink.addEventListener('click', function (event) {
            const isMobile = window.getComputedStyle(burgerBtn).display !== 'none';

            if (!isMobile) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            document.querySelectorAll('.main-nav .has-submenu.submenu-open').forEach(function (openItem) {
                if (openItem !== parentLi) {
                    openItem.classList.remove('submenu-open');
                }
            });

            parentLi.classList.toggle('submenu-open');
        });
    });

    // Close menu when normal link is clicked
    nav.addEventListener('click', function (event) {
        const link = event.target.closest('a');

        if (!link) {
            return;
        }

        const isMobile = window.getComputedStyle(burgerBtn).display !== 'none';

        if (!isMobile) {
            return;
        }

        const parentLi = link.closest('li.has-submenu');
        const isToggle = parentLi && link === parentLi.querySelector(':scope > a');

        if (!isToggle) {
            setTimeout(closeMenu, 150);
        }
    });

    // Close automatically when resizing to desktop
    let resizeTimer;

    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);

        resizeTimer = setTimeout(function () {
            const isMobile = window.getComputedStyle(burgerBtn).display !== 'none';

            if (!isMobile && isOpen) {
                closeMenu();
            }
        }, 250);
    });

    console.log('Burger menu ready');
})();
