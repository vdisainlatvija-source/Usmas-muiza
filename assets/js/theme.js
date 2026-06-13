// Init AOS
if (typeof AOS !== 'undefined') {
    AOS.init({
        once: true,
        offset: 50,
        duration: 800,
        easing: 'ease-out-cubic'
    });
}

// FAQ Accordion
document.querySelectorAll('.faq-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var item = this.closest('.faq-item');
        item.classList.toggle('-active');
    });
});

// Media + Text — image slider (pagination dots + click-drag, no arrows)
document.querySelectorAll('.mt-slider').forEach(function(slider) {
    var track = slider.querySelector('.mt-slider__track');
    if (!track) return;
    var dots = slider.querySelectorAll('.mt-slider__dot');

    // Click-and-drag scrolling
    var isDown = false, startX = 0, startScroll = 0;
    track.addEventListener('mousedown', function(e) {
        isDown = true;
        startX = e.pageX;
        startScroll = track.scrollLeft;
        track.classList.add('is-dragging');
        e.preventDefault();
    });
    window.addEventListener('mousemove', function(e) {
        if (!isDown) return;
        track.scrollLeft = startScroll - (e.pageX - startX);
    });
    window.addEventListener('mouseup', function() {
        if (!isDown) return;
        isDown = false;
        // Smooth-snap to the nearest slide, then re-enable CSS snap once settled
        // (removing .is-dragging immediately would make snap jump instantly).
        var w = track.clientWidth;
        track.scrollTo({ left: Math.round(track.scrollLeft / w) * w, behavior: 'smooth' });
        setTimeout(function() { track.classList.remove('is-dragging'); }, 400);
    });

    // Sync the active dot with the scroll position
    function updateDots() {
        if (!dots.length) return;
        var i = Math.round(track.scrollLeft / track.clientWidth);
        dots.forEach(function(d, di) { d.classList.toggle('is-active', di === i); });
    }
    var ticking = false;
    track.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() { updateDots(); ticking = false; });
            ticking = true;
        }
    });

    // Dot click → scroll to that slide
    dots.forEach(function(dot, di) {
        dot.addEventListener('click', function() {
            track.scrollTo({ left: di * track.clientWidth, behavior: 'smooth' });
        });
    });
});

// Content-builder table — expandable rows (the "+" toggle)
document.querySelectorAll('.cb-table__row.is-expandable .cb-table__head').forEach(function(head) {
    head.addEventListener('click', function() {
        var row = this.closest('.cb-table__row');
        var open = row.classList.toggle('is-open');
        this.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
});

// Lightbox — [data-lightbox="group"] links open in a fullscreen overlay with
// prev/next navigation through all images sharing the same group value.
(function() {
    var triggers = document.querySelectorAll('[data-lightbox]');
    if (!triggers.length) return;

    var overlay = document.createElement('div');
    overlay.className = 'lightbox';
    overlay.innerHTML =
        '<button class="lightbox__close" aria-label="Close">&times;</button>' +
        '<button class="lightbox__nav lightbox__nav--prev" aria-label="Previous">&#8249;</button>' +
        '<img class="lightbox__img" src="" alt="">' +
        '<button class="lightbox__nav lightbox__nav--next" aria-label="Next">&#8250;</button>';
    document.body.appendChild(overlay);

    var imgEl = overlay.querySelector('.lightbox__img');
    var prevBtn = overlay.querySelector('.lightbox__nav--prev');
    var nextBtn = overlay.querySelector('.lightbox__nav--next');
    var group = [];
    var index = 0;

    function show(i) {
        if (!group.length) return;
        index = (i + group.length) % group.length; // wrap around
        imgEl.src = group[index].href;
        imgEl.alt = group[index].alt;
        var multi = group.length > 1;
        prevBtn.style.display = multi ? '' : 'none';
        nextBtn.style.display = multi ? '' : 'none';
    }
    function openFrom(el) {
        var key = el.getAttribute('data-lightbox');
        var items = Array.prototype.slice.call(document.querySelectorAll('[data-lightbox="' + key + '"]'));
        group = items.map(function(it) {
            var im = it.querySelector('img');
            return { href: it.getAttribute('href'), alt: im ? im.alt : '' };
        });
        show(items.indexOf(el));
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    function close() {
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    triggers.forEach(function(t) {
        t.addEventListener('click', function(e) { e.preventDefault(); openFrom(this); });
    });
    prevBtn.addEventListener('click', function(e) { e.stopPropagation(); show(index - 1); });
    nextBtn.addEventListener('click', function(e) { e.stopPropagation(); show(index + 1); });
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay || e.target.classList.contains('lightbox__close')) close();
    });
    document.addEventListener('keydown', function(e) {
        if (!overlay.classList.contains('is-open')) return;
        if (e.key === 'Escape') close();
        else if (e.key === 'ArrowLeft') show(index - 1);
        else if (e.key === 'ArrowRight') show(index + 1);
    });
})();

// Gallery — category tabs switch the visible image track
document.querySelectorAll('.section-gallery').forEach(function(section) {
    var tabs = section.querySelectorAll('.gallery-tab');
    var panels = section.querySelectorAll('.gallery-panel');

    // "Skatīt vairāk" → carry the active category to the gallery archive.
    var ctaLink = section.querySelector('.gallery-cta a');
    function syncCta(tab) {
        if (!ctaLink || !tab || !tab.dataset.slug) return;
        var base = ctaLink.dataset.archive;
        if (!base) return;
        ctaLink.href = base + (base.indexOf('?') > -1 ? '&' : '?') + 'cat=' + encodeURIComponent(tab.dataset.slug);
    }
    syncCta(section.querySelector('.gallery-tab.is-active') || tabs[0]);

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var idx = this.dataset.tab;
            tabs.forEach(function(t) { t.classList.toggle('is-active', t === tab); });
            panels.forEach(function(p) { p.classList.toggle('is-active', p.dataset.panel === idx); });
            syncCta(tab);
        });
    });

    // Trigger the image entrance animation once the section scrolls into view.
    // threshold:0 fires as soon as any part enters — reliable for the tall
    // archive grid (a high threshold can never be met on very tall sections).
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) {
                if (e.isIntersecting) {
                    section.classList.add('in-view');
                    io.unobserve(section);
                }
            });
        }, { threshold: 0, rootMargin: '0px 0px -10% 0px' });
        io.observe(section);
    } else {
        section.classList.add('in-view');
    }
});

// Testimonials — seamless infinite slider (cloned sets) + drag + auto-advance.
// Clone the whole set before and after the originals. Every set is identical,
// so jumping the scroll position by one set-width is visually invisible — this
// gives an endless loop in both directions without a "snap back to start".
document.querySelectorAll('.section-testimonials .testimonials-track').forEach(function(track) {
    var originals = Array.prototype.slice.call(track.querySelectorAll('.testimonial'));
    if (originals.length < 2) return; // nothing to loop

    var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap) || 0;
    function measure() {
        var w = 0;
        originals.forEach(function(c) { w += c.getBoundingClientRect().width + gap; });
        return w;
    }

    // Only loop when the cards actually overflow the track.
    if (measure() <= track.clientWidth + 1) return;

    function cloneSet() {
        return originals.map(function(c) {
            var clone = c.cloneNode(true);
            clone.classList.add('is-visible', 'testimonial--clone'); // visible immediately (no re-entrance)
            clone.setAttribute('aria-hidden', 'true');
            clone.style.transitionDelay = '0s';
            return clone;
        });
    }
    cloneSet().forEach(function(c) { track.appendChild(c); });                              // after
    cloneSet().reverse().forEach(function(c) { track.insertBefore(c, track.firstChild); }); // before

    var SET = measure();
    track.scrollLeft = SET; // start on the middle (original) set

    function wrap() {
        if (SET <= 0) return;
        if (track.scrollLeft < SET * 0.5) { track.scrollLeft += SET; }
        else if (track.scrollLeft >= SET * 1.5) { track.scrollLeft -= SET; }
    }

    var isDown = false, startX = 0, startScroll = 0, timer = null, settle = null;

    function advance() {
        var card = originals[0];
        var step = card ? card.getBoundingClientRect().width + gap : track.clientWidth * 0.5;
        // Reposition before stepping so the smooth scroll always stays within
        // the cloned buffer (never interrupts the animation mid-flight).
        if (track.scrollLeft >= SET * 1.5 - step) { track.scrollLeft -= SET; }
        track.scrollBy({ left: step, behavior: 'smooth' });
    }
    function startAuto() { stopAuto(); timer = setInterval(advance, 4000); }
    function stopAuto() { if (timer) { clearInterval(timer); timer = null; } }

    track.addEventListener('mousedown', function(e) {
        isDown = true;
        startX = e.pageX;
        startScroll = track.scrollLeft;
        track.classList.add('is-dragging');
        stopAuto();
        e.preventDefault();
    });
    window.addEventListener('mousemove', function(e) {
        if (!isDown) return;
        track.scrollLeft = startScroll - (e.pageX - startX);
    });
    window.addEventListener('mouseup', function() {
        if (!isDown) return;
        isDown = false;
        track.classList.remove('is-dragging');
        wrap();
        startAuto();
    });

    // Touch / momentum scrolling: wrap once it settles (avoids interrupting it).
    track.addEventListener('scroll', function() {
        if (isDown) return;
        clearTimeout(settle);
        settle = setTimeout(wrap, 120);
    });

    window.addEventListener('resize', function() { SET = measure(); });

    startAuto();
});

// Testimonials — staggered card entrance as each card enters the slider view
document.querySelectorAll('.section-testimonials').forEach(function(section) {
    var track = section.querySelector('.testimonials-track');
    var cards = track ? track.querySelectorAll('.testimonial') : [];
    if (!cards.length) return;

    if (!('IntersectionObserver' in window)) {
        cards.forEach(function(c) { c.classList.add('is-visible'); });
        return;
    }

    function revealCards() {
        // Reveal cards as they become visible inside the (horizontal) track.
        var cardIO = new IntersectionObserver(function(entries) {
            var newly = entries.filter(function(e) {
                return e.isIntersecting && !e.target.classList.contains('is-visible');
            });
            // Stagger left-to-right within each batch (initial batch = the ~6
            // already visible; later, cards enter one at a time → delay 0).
            newly.sort(function(a, b) { return a.boundingClientRect.left - b.boundingClientRect.left; });
            newly.forEach(function(e, i) {
                e.target.style.transitionDelay = (i * 0.12) + 's';
                e.target.classList.add('is-visible');
                cardIO.unobserve(e.target);
            });
        }, { root: track, threshold: 0.4 });
        cards.forEach(function(c) { cardIO.observe(c); });
    }

    // Only start once the section itself scrolls into the page viewport.
    var sectionIO = new IntersectionObserver(function(entries) {
        if (entries[0].isIntersecting) {
            sectionIO.disconnect();
            revealCards();
        }
    }, { threshold: 0.15 });
    sectionIO.observe(section);
});

// Intro "read more" toggle — reveals hidden text within the same section
document.querySelectorAll('.intro-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var section = this.closest('.section-intro');
        if (!section) return;
        var open = section.classList.toggle('is-open');
        this.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (this.dataset.labelOpen && this.dataset.labelClose) {
            this.textContent = open ? this.dataset.labelClose : this.dataset.labelOpen;
        }
    });
});

// Counter animation with easeOutCubic
document.querySelectorAll('.stat-number[data-count]').forEach(function(el) {
    var target = parseInt(el.dataset.count);
    var suffix = el.dataset.suffix || '';
    var counted = false;
    var duration = 2000; // ms

    function easeOutCubic(t) {
        return 1 - Math.pow(1 - t, 2);
    }

    var observer = new IntersectionObserver(function(entries) {
        if (entries[0].isIntersecting && !counted) {
            counted = true;
            var start = performance.now();

            function animate(now) {
                var progress = Math.min((now - start) / duration, 1);
                var eased = easeOutCubic(progress);
                var current = Math.round(eased * target);
                el.textContent = current + suffix;
                if (progress < 1) requestAnimationFrame(animate);
            }

            requestAnimationFrame(animate);
        }
    }, { threshold: 0.5 });

    observer.observe(el);
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            const topOffset = 150;
            const elementPosition = target.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.scrollY - topOffset;
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    });
});

(function($){
    $(document).ready(function() {

        var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        if (isSafari) {
            document.body.classList.add('safari');
        }

	//console.clear();
        console.log("%c ".concat("Handcrafted with ❤️️ by vDisain - www.vdisain.lv"," "),"\n padding: 0.5em;\n border-radius: 0.4em;\n color: white;\n background: black;\n font-size: 19px;\n font-weight: bold;\n font-family: Courier;\n line-height: 1.5em;\n");
    });
}(window.jQuery));

// Preselect the contact form "Topic" dropdown from the ?temats= URL param.
// CTA buttons link to the contacts page with ?temats=<slug> (e.g.
// telpu-rezervacija). Gravity Forms' native dynamic population matches by the
// choice *value*, but on the translated (EN) form those values may be
// translated, breaking the match — so we match by value first and fall back to
// position, since the choices keep the same order in every language.
(function () {
    var temats = new URLSearchParams(window.location.search).get('temats');
    if (!temats) return;

    // Original (Latvian) choice values, in the order they appear in the form.
    var ORDER = ['restorans', 'spa', 'telpu-rezervacija', 'ipasie-piedavajumi', 'jaunumi', 'cits'];

    function preselect() {
        var selects = document.querySelectorAll('.gform_wrapper select');
        for (var i = 0; i < selects.length; i++) {
            var sel = selects[i];
            // Real options, skipping the empty "Select a topic" placeholder.
            var real = Array.prototype.filter.call(sel.options, function (o) { return o.value !== ''; });

            // 1) Exact value match (works when the values aren't translated).
            var match = real.filter(function (o) { return o.value === temats; })[0];

            // 2) Fall back to position — only on a dropdown that has all our
            //    choices, so we never touch an unrelated select.
            if (!match && real.length >= ORDER.length) {
                var idx = ORDER.indexOf(temats);
                if (idx > -1 && real[idx]) match = real[idx];
            }

            if (match) {
                sel.value = match.value;
                sel.dispatchEvent(new Event('change', { bubbles: true }));
                return;
            }
        }
    }

    if (document.readyState !== 'loading') preselect();
    else document.addEventListener('DOMContentLoaded', preselect);
})();