// Init AOS
if (typeof AOS !== 'undefined') {
    AOS.init({
        once: true,
        offset: 50
    });
}

// FAQ Accordion
document.querySelectorAll('.faq-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var item = this.closest('.faq-item');
        item.classList.toggle('-active');
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

        // mobileMenu();
        searchBar();
        contactPopup();

        $('#header-search-trigger').on('click', function(e) {
            e.preventDefault();
            $('body').toggleClass('search-active');
        });

        $('.close-search').on('click', function(e) {
            e.preventDefault();
            $('body').removeClass('search-active');
        });

        $(document).click(function(e) {
            var $target = $(e.target);
            if(!$target.closest('.header-search-inner').length && $('.header-search-inner').is(":visible") && !$target.is('#header-search-trigger')) {
                $('body').removeClass('search-active');
            }
        });

        $('.phone-menu-toggle').on('click', function(e) {
            e.preventDefault();
            $('body').toggleClass('phone-menu-active');
        });
        $('.close-phone-menu').on('click', function(e) {
            e.preventDefault();
            $('body').removeClass('phone-menu-active');
        });

        if($(window).width() < 768) {
            $('.search-form-input').on("focus", function () {
                $(this).parent().addClass("active");
            });
            $('.search-form-input').on("blur", function () {
                if ($(this).val().length < 1) {
                    $(this).parent().removeClass("active");
                }
            });

            $('.menu-item-has-children').on('click', function(e) {
                e.preventDefault();
                $(this).addClass('active');
                $(this).parent().addClass('submenu-active');
                $('.mobile-menu-back').addClass('active');
            });

            // go to the link of the a element
            $('.menu-item-has-children .sub-menu a').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                window.location.href = $this.attr('href');
            });

            $('.mobile-menu-back').on('click', function(e) {
                e.preventDefault();
                $(this).removeClass('active');
                $('.menu-item-has-children').removeClass('active');
                $('.menu-item-has-children').parent().removeClass('submenu-active');
            });
        }

        function searchBar() {
            var xhr = null,
            typingTimer = null,
            doneTypingDelay = 300, // ms after user stops typing
            minChars = 3,
            $input = $(".header-search .search-form-input"),
            $resultsWrap = $(".ajax_search_results"),
            $wrapperBody = $(".search-wrapper-body"),
            $loader = $resultsWrap.find(".searchLoader"),
            $container = $(
                ".search-wrapper-body .ajax_search_results .searchResults"
            );

            $input.on("input", function () {
                clearTimeout(typingTimer);
                var query = $(this).val().trim();
                $container.html("");

                if (query.length >= minChars) {
                    $wrapperBody.addClass("active");
                    $loader.fadeIn();

                    typingTimer = setTimeout(function () {
                        if (xhr) {
                            xhr.abort();
                        }
                        xhr = $.ajax({
                            url: theme.ajaxurl,
                            type: "POST",
                            data: {
                                action: "ajaxSearch",
                                search: query,
                                nonce: theme.nonce,
                            },
                            success: function (res) {
                                console.log(res);
                                if (res.data && res.data.html) {
                                    $container.html(res.data.html);
                                }
                            },
                            complete: function () {
                                $loader.fadeOut();
                            },
                        });
                    }, doneTypingDelay);
                } else {
                    $wrapperBody.removeClass("active");
                }
            });

            $input.on("focus", function () {
                if ($(this).val().length >= minChars) {
                    $wrapperBody.addClass("active");
                }
            });
        }

        function contactPopup() {
            var $popup = $('#contactPopup');
            var $body = $('body');

            // Open popup
            $(document).on('click', '[data-popup="contact"]', function(e) {
                e.preventDefault();
                $popup.removeClass('-confirmed');
                $popup.addClass('-active');
                $body.addClass('popup-active');
            });

            function closePopup() {
                if ($popup.hasClass('-confirmed')) {
                    // After form was submitted, reload to reset form
                    window.location.reload();
                } else {
                    $popup.removeClass('-active');
                    $body.removeClass('popup-active');
                }
            }

            // Close popup - X button
            $popup.find('.popup-contact-close').on('click', closePopup);

            // Close popup - overlay click
            $popup.find('.popup-contact-overlay').on('click', closePopup);

            // Close popup - Escape key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $popup.hasClass('-active')) {
                    closePopup();
                }
            });

            // Select color change on value pick
            $popup.on('change', 'select', function() {
                if ($(this).val()) {
                    $(this).addClass('gf-value-selected');
                } else {
                    $(this).removeClass('gf-value-selected');
                }
            });

            // After Gravity Forms submission - show confirmation state
            $(document).on('gform_confirmation_loaded', function() {
                $popup.addClass('-confirmed');
            });
        }

        function mobileMenu() {
            $(document).on('click', '#mobMenuTrigger', function(e) {
                e.preventDefault();
                $('body').toggleClass('mobMenuActive');
            });
        }

		//console.clear();
        console.log("%c ".concat("Handcrafted with ❤️️ by vDisain - www.vdisain.lv"," "),"\n padding: 0.5em;\n border-radius: 0.4em;\n color: white;\n background: black;\n font-size: 19px;\n font-weight: bold;\n font-family: Courier;\n line-height: 1.5em;\n");
    });
}(window.jQuery));