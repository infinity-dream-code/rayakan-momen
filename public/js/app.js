document.addEventListener('DOMContentLoaded', function () {
    var navbar = document.getElementById('navbar');
    var hamburger = document.getElementById('hamburger');
    var mobileMenu = document.getElementById('mobile-menu');
    var mobileOverlay = document.getElementById('mobile-overlay');
    var mobileClose = document.getElementById('mobile-close');
    var mobileLinks = document.querySelectorAll('.mobile-nav-link');

    function openMobileMenu() {
        if (!mobileMenu || !mobileOverlay) return;
        mobileMenu.classList.add('open');
        mobileOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        if (!mobileMenu || !mobileOverlay) return;
        mobileMenu.classList.remove('open');
        mobileOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (hamburger) hamburger.addEventListener('click', openMobileMenu);
    if (mobileClose) mobileClose.addEventListener('click', closeMobileMenu);
    if (mobileOverlay) mobileOverlay.addEventListener('click', closeMobileMenu);
    mobileLinks.forEach(function (link) {
        link.addEventListener('click', closeMobileMenu);
    });

    window.addEventListener('scroll', function () {
        if (!navbar) return;
        if (window.scrollY > 40) {
            navbar.classList.add('nav-scrolled');
        } else {
            navbar.classList.remove('nav-scrolled');
        }
    });

    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (!targetId || targetId === '#') return;
            var target = document.querySelector(targetId);
            if (!target) return;
            e.preventDefault();
            var offset = navbar ? navbar.offsetHeight + 8 : 80;
            var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top: top, behavior: 'smooth' });
        });
    });

    var revealEls = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var revealObserver = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        revealObserver.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
        );
        revealEls.forEach(function (el) {
            revealObserver.observe(el);
        });
    } else {
        revealEls.forEach(function (el) {
            el.classList.add('revealed');
        });
    }

    document.querySelectorAll('.faq-trigger').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = this.closest('.faq-item');
            var wasOpen = item.classList.contains('open');
            document.querySelectorAll('.faq-item.open').forEach(function (openItem) {
                openItem.classList.remove('open');
            });
            if (!wasOpen) item.classList.add('open');
        });
    });

    var track = document.getElementById('testimonial-track');
    var dots = document.querySelectorAll('.carousel-dot');
    var prevBtn = document.getElementById('testimonial-prev');
    var nextBtn = document.getElementById('testimonial-next');
    var testimonialCards = track ? track.querySelectorAll('.testimonial-card') : [];
    var currentIndex = 0;

    function updateDots(index) {
        dots.forEach(function (dot, i) {
            dot.classList.toggle('active', i === index);
        });
    }

    function scrollToCard(index) {
        if (!track || !testimonialCards.length) return;
        currentIndex = Math.max(0, Math.min(index, testimonialCards.length - 1));
        var card = testimonialCards[currentIndex];
        track.scrollTo({ left: card.offsetLeft, behavior: 'smooth' });
        updateDots(currentIndex);
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            scrollToCard(currentIndex - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            scrollToCard(currentIndex + 1);
        });
    }

    dots.forEach(function (dot, i) {
        dot.addEventListener('click', function () {
            scrollToCard(i);
        });
    });

    if (track) {
        var scrollTimer;
        track.addEventListener('scroll', function () {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function () {
                var scrollLeft = track.scrollLeft;
                var closest = 0;
                var minDist = Infinity;
                testimonialCards.forEach(function (card, i) {
                    var dist = Math.abs(card.offsetLeft - scrollLeft);
                    if (dist < minDist) {
                        minDist = dist;
                        closest = i;
                    }
                });
                currentIndex = closest;
                updateDots(closest);
            }, 80);
        });
    }

    updateDots(0);

    /* Hero slideshow — wedding / couple / ultah */
    (function () {
        var root = document.getElementById('heroSlides');
        if (!root) return;
        var slides = root.querySelectorAll('.hero-slide');
        var dots = document.querySelectorAll('#heroSlideDots button');
        var label = document.getElementById('heroSlideLabel');
        var index = 0;
        var timer;

        function goTo(i) {
            index = (i + slides.length) % slides.length;
            slides.forEach(function (slide, n) {
                slide.classList.toggle('is-active', n === index);
            });
            dots.forEach(function (dot, n) {
                dot.classList.toggle('is-active', n === index);
            });
            if (label) {
                label.textContent = slides[index].getAttribute('data-label') || '';
            }
        }

        function next() {
            goTo(index + 1);
        }

        function start() {
            clearInterval(timer);
            timer = setInterval(next, 5200);
        }

        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                goTo(parseInt(this.getAttribute('data-slide'), 10) || 0);
                start();
            });
        });

        start();
    })();

    /* Marketplace template filter */
    var chips = document.querySelectorAll('#templateFilters .market-chip');
    var catCards = document.querySelectorAll('[data-cat-filter]');
    var marketCards = document.querySelectorAll('#templateGrid .market-card');

    function setFilter(cat) {
        chips.forEach(function (chip) {
            chip.classList.toggle('is-active', chip.getAttribute('data-filter') === cat);
        });
        catCards.forEach(function (card) {
            card.classList.toggle('is-active', card.getAttribute('data-cat-filter') === cat);
        });
        marketCards.forEach(function (card) {
            var match = cat === 'all' || card.getAttribute('data-category') === cat;
            card.classList.toggle('is-hidden', !match);
        });
    }

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            setFilter(this.getAttribute('data-filter') || 'all');
        });
    });

    catCards.forEach(function (card) {
        card.addEventListener('click', function () {
            setFilter(this.getAttribute('data-cat-filter') || 'all');
            var section = document.getElementById('templateGrid');
            if (section) {
                var offset = navbar ? navbar.offsetHeight + 8 : 80;
                var top = section.getBoundingClientRect().top + window.pageYOffset - offset - 60;
                window.scrollTo({ top: top, behavior: 'smooth' });
            }
        });
    });

    document.querySelectorAll('[data-jump-cat]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            var cat = this.getAttribute('data-jump-cat');
            if (!cat) return;
            e.preventDefault();
            setFilter(cat);
            var section = document.getElementById('template');
            if (!section) return;
            var offset = navbar ? navbar.offsetHeight + 8 : 80;
            var top = section.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top: top, behavior: 'smooth' });
        });
    });
});
