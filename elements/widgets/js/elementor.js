(function ($) {
    // Register GSAP plugins globally for elementor.js
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined' && typeof SplitText !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger, SplitText);
    }

    const PXLDIV_MAX_DIST = 140;
    const PXLDIV_PEAK = 0.75;
    const PXLDIV_SPREAD_MIN = 170;
    const PXLDIV_SPREAD_MAX = 250;

    const PXLBORDER_MAX_DIST = 300;
    const PXLBORDER_PEAK = 0.75;
    const PXLBORDER_SPREAD_MIN = 170;
    const PXLBORDER_SPREAD_MAX = 300;

    function pxl_clamp(v, a, b) {
        return v < a ? a : (v > b ? b : v);
    }

    function pxl_smoothstep(t) {
        return t * t * (3 - 2 * t);
    }

    (function () {
        function addStyle() {
            if (document.head) {
                var style = document.createElement('style');
                style.textContent = 'body:not(.elementor-editor-active) .pxl-animate.pxl-invisible { opacity: 0 !important; visibility: hidden !important; }';
                document.head.appendChild(style);
            } else {
                setTimeout(addStyle, 10);
            }
        }
        addStyle();
    })();

    function frameflow_animation_handler($scope) {
        var $elements = $scope ? $scope.find('.pxl-animate') : $(document).find('.pxl-animate');

        $elements.each(function () {
            var $el = $(this);
            if (!$el.hasClass('pxl-invisible')) {
                $el.addClass('pxl-invisible');
            }
        });

        if (window.elementorFrontend && typeof elementorFrontend.waypoint === 'function') {
            elementorFrontend.waypoint($elements, function () {
                var $animate_el = $(this),
                    data = $animate_el.data('settings');
                if (typeof data !== 'undefined' && typeof data['animation'] !== 'undefined') {
                    setTimeout(function () {
                        $animate_el.removeClass('pxl-invisible').addClass('animated ' + data['animation']);
                    }, data['animation_delay'] || 0);
                } else {
                    setTimeout(function () {
                        $animate_el.removeClass('pxl-invisible').addClass('animated fadeInUp');
                    }, 300);
                }
            });
        }

        if ($scope && window.elementorFrontend && typeof elementorFrontend.waypoint === 'function') {
            const waypointElements = [
                '.pxl-border-animated',
                '.pxl-section-divider',
                '.TextOutlineAnimation',
                '.pxl-text-banner--left',
                '.pxl-text-banner--right',
                '.pxl-item--rotate-even',
                '.pxl-item--rotate-odd'
            ];
            elementorFrontend.waypoint($scope.find(waypointElements.join(', ')), function () {
                $(this).addClass('pxl-animated');
            });
        }
    }

    function bind_divider_glow(container) {
        if (!container || container.getAttribute('data-divider-glow-bound') === '1') return;

        var dividers = container.querySelectorAll('.pxl-section-divider');
        if (!dividers.length) return;

        container.setAttribute('data-divider-glow-bound', '1');

        var state = {
            x: 0,
            y: 0,
            inside: false,
            raf: null
        };

        function render() {
            state.raf = null;
            var rect = container.getBoundingClientRect();
            if (!rect || rect.width === 0 || rect.height === 0) return;

            var x = pxl_clamp(state.x - rect.left, 0, rect.width);
            var y = pxl_clamp(state.y - rect.top, 0, rect.height);

            dividers.forEach(function (divider) {
                var isHorizontal = divider.classList.contains('pxl-section-divider--top') || divider.classList.contains('pxl-section-divider--bottom');
                var target = isHorizontal ? (divider.classList.contains('pxl-section-divider--top') ? 0 : rect.height) : (divider.classList.contains('pxl-section-divider--left') ? 0 : rect.width);
                var dist = Math.abs((isHorizontal ? y : x) - target);
                var t = state.inside ? pxl_clamp(1 - dist / PXLDIV_MAX_DIST, 0, 1) : 0;
                var eased = pxl_smoothstep(t);
                var alpha = PXLDIV_PEAK * eased;
                var spread = PXLDIV_SPREAD_MAX - (PXLDIV_SPREAD_MAX - PXLDIV_SPREAD_MIN) * eased;

                divider.style.setProperty('--pxl-divider-gx', x + 'px');
                divider.style.setProperty('--pxl-divider-gy', y + 'px');
                divider.style.setProperty('--pxl-divider-alpha', alpha.toFixed(3));
                divider.style.setProperty('--pxl-divider-spread', spread.toFixed(1) + 'px');
                divider.setAttribute('data-glow', state.inside && alpha > 0.003 ? '1' : '0');
            });
        }

        function schedule() {
            if (state.raf) return;
            state.raf = requestAnimationFrame(render);
        }

        function handleMove(e) {
            state.x = e.clientX;
            state.y = e.clientY;
            state.inside = true;
            schedule();
        }

        function handleLeave() {
            state.inside = false;
            schedule();
        }

        var rect = container.getBoundingClientRect();
        state.x = rect.left + rect.width / 2;
        state.y = rect.top + rect.height / 2;

        container.addEventListener('mousemove', handleMove);
        container.addEventListener('mouseenter', handleMove);
        container.addEventListener('mouseleave', handleLeave);

        schedule();
    }

    function frameflow_section_divider_glow($scope) {
        if (window.innerWidth < 1024) return;
        var $context = $scope && $scope.length ? $scope : $(document);
        var hosts = [];

        $context.find('.pxl-section-divider').each(function () {
            var host = this.closest('.elementor-element');
            if (host && hosts.indexOf(host) === -1) {
                hosts.push(host);
            }
        });

        hosts.forEach(function (host) {
            bind_divider_glow(host);
        });
    }

    // Shared Manager for Border Glow to optimize performance
    const BorderGlowManager = {
        instances: new Set(),
        x: 0,
        y: 0,
        raf: null,
        initialized: false,

        init() {
            if (this.initialized) return;
            document.addEventListener('pointermove', (e) => {
                this.x = e.clientX;
                this.y = e.clientY;
                this.schedule();
            }, { passive: true });

            window.addEventListener('resize', () => this.updateAllRects(), { passive: true });
            window.addEventListener('scroll', () => this.updateAllRects(), { passive: true });
            this.initialized = true;
        },

        register(instance) {
            this.init();
            this.instances.add(instance);
        },

        unregister(instance) {
            this.instances.delete(instance);
        },

        updateAllRects() {
            if (this.throttleTimer) return;
            this.throttleTimer = setTimeout(() => {
                this.instances.forEach(inst => {
                    if (inst.inViewport) {
                        inst.updateRect();
                    }
                });
                this.schedule();
                this.throttleTimer = null;
            }, 100);
        },

        schedule() {
            if (this.raf) return;
            this.raf = requestAnimationFrame(() => this.render());
        },

        render() {
            this.raf = null;
            this.instances.forEach(inst => {
                if (inst.inViewport) {
                    inst.render(this.x, this.y);
                }
            });
        }
    };

    function bind_border_glow(host) {
        if (!host || host.getAttribute('data-border-glow-bound') === '1') return;

        const glowHost = host.querySelector('.pxl-border-glow');
        if (!glowHost) return;

        host.setAttribute('data-border-glow-bound', '1');

        const topGlow = glowHost.querySelector('.pxl-bd-glow.top');
        const rightGlow = glowHost.querySelector('.pxl-bd-glow.right');
        const bottomGlow = glowHost.querySelector('.pxl-bd-glow.bottom');
        const leftGlow = glowHost.querySelector('.pxl-bd-glow.left');

        const instance = {
            host: host,
            inViewport: false,
            rect: null,
            obs: null,

            updateRect() {
                this.rect = this.host.getBoundingClientRect();
            },

            // Calculate distance from point to rectangle
            // Returns 0 if point is inside rectangle, otherwise distance to nearest edge
            distanceToRect(px, py, rect) {
                const dx = Math.max(0, Math.max(rect.left - px, px - rect.right));
                const dy = Math.max(0, Math.max(rect.top - py, py - rect.bottom));
                return Math.sqrt(dx * dx + dy * dy);
            },

            render(mx, my) {
                if (!this.inViewport) {
                    this.setAlpha(0);
                    return;
                }

                if (!this.rect) this.updateRect();
                const rect = this.rect;
                if (!rect || !rect.width || !rect.height) return;

                const distance = this.distanceToRect(mx, my, rect);

                if (distance > PXLBORDER_MAX_DIST) {
                    this.setAlpha(0);
                    return;
                }

                const x = pxl_clamp(mx - rect.left, 0, rect.width);
                const y = pxl_clamp(my - rect.top, 0, rect.height);

                const distTop = y;
                const distRight = rect.width - x;
                const distBottom = rect.height - y;
                const distLeft = x;

                const baseAlpha = pxl_clamp(1 - distance / PXLBORDER_MAX_DIST, 0, 1);
                const easedBase = pxl_smoothstep(baseAlpha);

                const tTop = pxl_clamp(1 - distTop / PXLBORDER_MAX_DIST, 0, 1);
                const tRight = pxl_clamp(1 - distRight / PXLBORDER_MAX_DIST, 0, 1);
                const tBottom = pxl_clamp(1 - distBottom / PXLBORDER_MAX_DIST, 0, 1);
                const tLeft = pxl_clamp(1 - distLeft / PXLBORDER_MAX_DIST, 0, 1);

                const alpha = PXLBORDER_PEAK * easedBase;
                const spread = PXLBORDER_SPREAD_MAX - (PXLBORDER_SPREAD_MAX - PXLBORDER_SPREAD_MIN) * easedBase;

                this.host.style.setProperty('--pxl-bd-gx', x + 'px');
                this.host.style.setProperty('--pxl-bd-gy', y + 'px');
                this.host.style.setProperty('--pxl-bd-alpha', alpha.toFixed(3));
                this.host.style.setProperty('--pxl-bd-spread', spread.toFixed(1) + 'px');

                if (topGlow) topGlow.style.opacity = (pxl_smoothstep(tTop) * easedBase).toFixed(3);
                if (rightGlow) rightGlow.style.opacity = (pxl_smoothstep(tRight) * easedBase).toFixed(3);
                if (bottomGlow) bottomGlow.style.opacity = (pxl_smoothstep(tBottom) * easedBase).toFixed(3);
                if (leftGlow) leftGlow.style.opacity = (pxl_smoothstep(tLeft) * easedBase).toFixed(3);
            },

            setAlpha(val) {
                this.host.style.setProperty('--pxl-bd-alpha', val);
                if (topGlow) topGlow.style.opacity = val;
                if (rightGlow) rightGlow.style.opacity = val;
                if (bottomGlow) bottomGlow.style.opacity = val;
                if (leftGlow) leftGlow.style.opacity = val;
            }
        };

        instance.obs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.target !== host) return;
                instance.inViewport = !!entry.isIntersecting;
                if (instance.inViewport) {
                    instance.updateRect();
                    BorderGlowManager.schedule();
                } else {
                    instance.setAlpha(0);
                }
            });
        }, { threshold: 0.1 });

        instance.obs.observe(host);
        BorderGlowManager.register(instance);

        // cleanup
        host._pxlBorderGlowCleanup = function () {
            if (instance.obs) instance.obs.disconnect();
            BorderGlowManager.unregister(instance);
            host.removeAttribute('data-border-glow-bound');
            delete host._pxlBorderGlowCleanup;
        };
    }


    function frameflow_border_glow($scope) {
        if (window.innerWidth < 1024) return;
        var $context = $scope && $scope.length ? $scope : $(document);
        $context.find('.pxl-border-section-anm').each(function () {
            bind_border_glow(this);
        });
    }

    function frameflow_polyfill_waypoint() {
        if (!window.elementorFrontend || typeof elementorFrontend.waypoint === 'function') return;
        elementorFrontend.waypoint = function ($elements, handler, options) {
            options = options || {};
            var triggerOnce = typeof options.triggerOnce === 'boolean' ? options.triggerOnce : true;
            var offset = options.offset;
            var rootMargin = '0px 0px 0px 0px';
            if (typeof offset === 'string' && /%$/.test(offset)) {
                var percent = parseFloat(offset);
                if (!isNaN(percent)) {
                    var bottomPercent = Math.max(0, 100 - percent);
                    rootMargin = '0px 0px -' + bottomPercent + '% 0px';
                }
            } else if (typeof offset === 'number') {
                rootMargin = '0px 0px -' + offset + 'px 0px';
            }
            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting || entry.intersectionRatio > 0) {
                            handler.call(entry.target);
                            if (triggerOnce) {
                                observer.unobserve(entry.target);
                            }
                        }
                    });
                }, { root: null, rootMargin: rootMargin, threshold: 0 });
                $($elements).each(function () {
                    var dom = this instanceof Element ? this : (this && this[0] instanceof Element ? this[0] : null);
                    if (dom) observer.observe(dom);
                });
            } else {
                $($elements).each(function () { handler.call(this); });
            }
        };
    }

    function frameflow_section_start_render() {
        var _elementor = typeof elementor !== 'undefined' ? elementor : elementorFrontend;

        _elementor.hooks.addFilter('pxl_element_container/before-render', function (html, settings) {
            if (typeof settings.pxl_parallax_bg_img !== 'undefined' && settings.pxl_parallax_bg_img && settings.pxl_parallax_bg_img.url !== '') {
                html += '<div class="pxl-section-bg-parallax"></div>';
            }

            if (typeof settings.pxl_color_offset !== 'undefined' && settings.pxl_color_offset !== 'none') {
                html += '<div class="pxl-section-overlay-color"></div>';
            }

            if (typeof settings.pxl_overlay_img !== 'undefined' && settings.pxl_overlay_img && settings.pxl_overlay_img.url !== '') {
                html += '<div class="pxl-overlay--image pxl-overlay--imageLeft"><div class="bg-image"></div></div>';
            }

            if (typeof settings.pxl_overlay_img2 !== 'undefined' && settings.pxl_overlay_img2 && settings.pxl_overlay_img2.url !== '') {
                html += '<div class="pxl-overlay--image pxl-overlay--imageRight"><div class="bg-image"></div></div>';
            }

            return html;
        });

        // Chờ DOM ready trước khi tìm elements
        $(document).ready(function () {
            $('.pxl-section-bg-parallax').closest('.elementor-element').addClass('pxl-section-parallax-overflow');
        });
    }

    function frameflow_css_inline_js() {
        var _inline_css = "<style>";
        $(document).find('.pxl-inline-css').each(function () {
            var _this = $(this);
            _inline_css += _this.attr("data-css") + " ";
            _this.remove();
        });
        _inline_css += "</style>";
        $('head').append(_inline_css);
    }

    function frameflow_section_before_render() {
        var _elementor = typeof elementor !== 'undefined' ? elementor : elementorFrontend;
        _elementor.hooks.addFilter('pxl-custom-section/before-render', function (html, settings, el) {
            if (typeof settings['row_divider'] !== 'undefined') {
                if (settings['row_divider'] === 'angle-top' || settings['row_divider'] === 'angle-bottom' || settings['row_divider'] === 'angle-top-right' || settings['row_divider'] === 'angle-bottom-left') {
                    html = '<svg class="pxl-row-angle" style="fill:#ffffff" xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 100 100" version="1.1" preserveAspectRatio="none" height="130px"><path stroke="" stroke-width="0" d="M0 100 L100 0 L200 100"></path></svg>';
                    return html;
                }
                if (settings['row_divider'] === 'angle-top-bottom' || settings['row_divider'] === 'angle-top-bottom-left') {
                    html = '<svg class="pxl-row-angle pxl-row-angle-top" style="fill:#ffffff" xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 100 100" version="1.1" preserveAspectRatio="none" height="130px"><path stroke="" stroke-width="0" d="M0 100 L100 0 L200 100"></path></svg><svg class="pxl-row-angle pxl-row-angle-bottom" style="fill:#ffffff" xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 100 100" version="1.1" preserveAspectRatio="none" height="130px"><path stroke="" stroke-width="0" d="M0 100 L100 0 L200 100"></path></svg>';
                    return html;
                }
                if (settings['row_divider'] === 'wave-animation-top' || settings['row_divider'] === 'wave-animation-bottom') {
                    html = '<svg class="pxl-row-angle" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" viewBox="0 0 1440 150" fill="#fff"><path d="M 0 26.1978 C 275.76 83.8152 430.707 65.0509 716.279 25.6386 C 930.422 -3.86123 1210.32 -3.98357 1439 9.18045 C 2072.34 45.9691 2201.93 62.4429 2560 26.198 V 172.199 L 0 172.199 V 26.1978 Z"><animate repeatCount="indefinite" fill="freeze" attributeName="d" dur="10s" values="M0 25.9086C277 84.5821 433 65.736 720 25.9086C934.818 -3.9019 1214.06 -5.23669 1442 8.06597C2079 45.2421 2208 63.5007 2560 25.9088V171.91L0 171.91V25.9086Z; M0 86.3149C316 86.315 444 159.155 884 51.1554C1324 -56.8446 1320.29 34.1214 1538 70.4063C1814 116.407 2156 188.408 2560 86.315V232.317L0 232.316V86.3149Z; M0 53.6584C158 11.0001 213 0 363 0C513 0 855.555 115.001 1154 115.001C1440 115.001 1626 -38.0004 2560 53.6585V199.66L0 199.66V53.6584Z; M0 25.9086C277 84.5821 433 65.736 720 25.9086C934.818 -3.9019 1214.06 -5.23669 1442 8.06597C2079 45.2421 2208 63.5007 2560 25.9088V171.91L0 171.91V25.9086Z"></animate></path></svg>';
                    return html;
                }
                if (settings['row_divider'] === 'curved-top' || settings['row_divider'] === 'curved-bottom') {
                    html = '<svg class="pxl-row-angle" xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 1920 128" version="1.1" preserveAspectRatio="none" style="fill:#ffffff"><path stroke-width="0" d="M-1,126a3693.886,3693.886,0,0,1,1921,2.125V-192H-7Z"></path></svg>';
                    return html;
                }
            }
        });
    }

    var PXL_Icon_Contact_Form = function ($scope, $) {

        setTimeout(function () {
            $('.pxl--item').each(function () {
                var icon_input = $(this).find(".pxl--form-icon"),
                    control_wrap = $(this).find('.wpcf7-form-control');
                control_wrap.before(icon_input.clone());
                icon_input.remove();
            });
        }, 10);

    };


    function frameflow_split_text($scope) {
        var st = $scope.find(".pxl-split-text");
        if (st.length === 0) return;

        if (typeof gsap === 'undefined' || typeof SplitText === 'undefined' || typeof ScrollTrigger === 'undefined') {
            console.warn('GSAP, SplitText, or ScrollTrigger plugin not loaded');
            return;
        }


        st.each(function (index, el) {
            // Cleanup previous instances
            if (el.pxl_split_anim) {
                el.pxl_split_anim.kill();
                el.pxl_split_anim = null;
            }
            if (el.pxl_split_instance) {
                el.pxl_split_instance.revert();
                el.pxl_split_instance = null;
            }

            var els = $(el).find('p').length > 0 ? $(el).find('p')[0] : el;

            // Determine split type based on class
            let types = "lines, words, chars";
            if ($(el).hasClass('split-up') || $(el).hasClass('split-words-scale')) {
                types = "words";
            }

            const pxl_split = new SplitText(els, {
                type: types,
                lineThreshold: 0.5,
                linesClass: "split-line"
            });
            el.pxl_split_instance = pxl_split;

            var split_type_set = pxl_split.chars; // Default to chars
            if ($(el).hasClass('split-up') || $(el).hasClass('split-words-scale')) {
                split_type_set = pxl_split.words;
            }

            gsap.set(els, { perspective: 400 });

            if ($(el).hasClass('split-up')) {
                // Optimized: Single tween with stagger instead of loop
                el.pxl_split_anim = gsap.from(split_type_set, {
                    opacity: 0,
                    duration: 0.65,
                    y: 60,
                    stagger: 0.065,
                    delay: 0.25,
                    ease: "expo.out",
                    scrollTrigger: {
                        trigger: el,
                        start: "top 86%",
                        toggleActions: "play none none none",
                    },
                });
            } else if ($(el).hasClass('split-words-scale')) {
                // Pre-set initial state
                split_type_set.forEach((elw, i) => {
                    gsap.set(elw, {
                        opacity: 0,
                        scale: i % 2 === 0 ? 0 : 2,
                        force3D: true
                    });
                });

                el.pxl_split_anim = gsap.to(split_type_set, {
                    scrollTrigger: {
                        trigger: el,
                        toggleActions: "play reverse play reverse",
                        start: "top 86%",
                    },
                    duration: 0.35,
                    stagger: 0.02,
                    ease: "expo.out",
                    rotateX: 0,
                    scale: 1,
                    opacity: 1,
                });
            } else {
                // Standard Char Animations
                var settings = {
                    scrollTrigger: {
                        trigger: els,
                        toggleActions: "play none none none",
                        start: "top 86%",
                        once: true
                    },
                    duration: 0.35,
                    stagger: 0.02,
                    ease: "Expo.out"
                };

                if ($(el).hasClass('split-in-fade')) settings.opacity = 0;
                if ($(el).hasClass('split-in-right')) { settings.opacity = 0; settings.x = "50"; }
                if ($(el).hasClass('split-in-left')) { settings.opacity = 0; settings.x = "-50"; }
                if ($(el).hasClass('split-in-up')) { settings.opacity = 0; settings.y = "80"; }
                if ($(el).hasClass('split-in-down')) { settings.opacity = 0; settings.y = "-80"; }
                if ($(el).hasClass('split-in-rotate')) { settings.opacity = 0; settings.rotateX = "50deg"; }
                if ($(el).hasClass('split-in-scale')) { settings.opacity = 0; settings.scale = "0.5"; }

                el.pxl_split_anim = gsap.from(split_type_set, settings);
            }

            if ($(el).hasClass('hover-split-text')) {
                $(el).on('mouseenter', function () {
                    if (el.pxl_split_anim) el.pxl_split_anim.restart();
                });
            }
        });
    }


    function frameflow_zoom_point() {
        if (!window.elementorFrontend || typeof elementorFrontend.waypoint !== 'function') return;

        var $zoomPoints = $(document).find('.pxl-zoom-point');
        if ($zoomPoints.length === 0) return;

        elementorFrontend.waypoint($zoomPoints, function () {
            var $el = $(this);
            $el.addClass('pxl-zoom-active');
        }, {
            offset: -100,
            triggerOnce: true
        });
    }

    function frameflow_card_carousel($scope) {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

        const $wrapper = $scope.find('.pxl-image-carousel2.pxl-horizontal-pin .pxl-item-wrapper');
        if (!$wrapper.length) return;

        const $section = $scope.closest('#cardSection').length ? $scope.closest('#cardSection') : $scope.closest('.elementor-section, .e-con, .elementor-element');
        const container = $section.get(0);
        const wrapperEl = $wrapper.get(0);
        const $cards = $scope.find('.pxl-item--inner');
        const total = $cards.length;

        if (total === 0) return;

        let mm = gsap.matchMedia();

        mm.add("(min-width: 1201px)", () => {
            const xTranslate = wrapperEl.scrollWidth - wrapperEl.offsetWidth;

            gsap.set(container, {
                height: total * 100 + "vh"
            });

            const tl = gsap.timeline({
                scrollTrigger: {
                    trigger: container,
                    scrub: true,
                    start: 'top top',
                    end: 'bottom bottom',
                    invalidateOnRefresh: true,
                }
            });

            tl.to(wrapperEl, {
                x: -xTranslate,
                ease: 'none',
                duration: total
            });

            return () => {
                tl.kill();
                gsap.set(container, { clearProps: "height" });
                gsap.set(wrapperEl, { clearProps: "x" });
            };
        });

        mm.add("(max-width: 1200px)", () => {
            if (typeof Swiper === 'undefined') return;

            const $container = $scope.find('.pxl-image-carousel2');
            const $wrapper = $scope.find('.pxl-item-wrapper');
            const $items = $scope.find('.pxl-item--inner');

            $container.addClass('swiper');
            $wrapper.addClass('swiper-wrapper');
            $items.addClass('swiper-slide');

            const swiper = new Swiper($container.get(0), {
                slidesPerView: 1,
                spaceBetween: 0,
                pagination: {
                    el: $container.find('.pxl-swiper-dots')[0],
                    clickable: true,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                    },
                    992: {
                        slidesPerView: 3,
                    }
                }
            });

            return () => {
                swiper.destroy(true, true);
                $container.removeClass('swiper');
                $wrapper.removeClass('swiper-wrapper');
                $items.removeClass('swiper-slide');
            };
        });
    }


    function frameflow_scroll_fixed_section() {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

        ScrollTrigger.matchMedia({
            "(min-width: 991px)": function () {
                // Feature 1: Top Fixed Section pinned to Bottom Section
                const fixedTops = document.querySelectorAll('.pxl-section-fix-top');
                const fixedBottoms = document.querySelectorAll(".pxl-section-fix-bottom");

                fixedBottoms.forEach((fixedBottom, index) => {
                    const fixedTop = fixedTops[index];
                    if (fixedTop) {
                        ScrollTrigger.create({
                            trigger: fixedBottom,
                            pin: fixedTop,
                            start: 'top bottom',
                            end: "bottom top",
                            scrub: 1, // Smooth scrub
                            pinSpacing: false
                        });

                        const bottomOverlay = fixedBottom.querySelector(".pxl-section-overlay-color");
                        if (bottomOverlay) {
                            gsap.to(bottomOverlay, {
                                scrollTrigger: {
                                    trigger: fixedBottom,
                                    scrub: 1,
                                    start: 'top bottom',
                                    end: "bottom top",
                                }
                            });
                        }
                    }
                });

                // Feature 2: Scroll Overlay Animation
                const overlayColors = document.querySelectorAll('.pxl-section-overlay-color');
                const overlayScrolls = document.querySelectorAll(".overlay-type-scroll");
                const bgColorScrolls = document.querySelectorAll('.pxl-bg-color-scroll');

                bgColorScrolls.forEach((bgColorScroll, index) => {
                    const overlayColor = overlayColors[index];
                    const overlayScroll = overlayScrolls[index];

                    if (overlayColor && overlayScroll) {
                        const data = overlayColor.dataset;
                        const top = data.spaceTop || 0;
                        const left = data.spaceLeft || 0;
                        const right = data.spaceRight || 0;
                        const bottom = data.spaceBottom || 0;
                        const rTop = data.radiusTop || 0;
                        const rLeft = data.radiusLeft || 0;
                        const rRight = data.radiusRight || 0;
                        const rBottom = data.radiusBottom || 0;
                        const radius = `${rTop}px ${rRight}px ${rBottom}px ${rLeft}px`;

                        gsap.to(overlayScroll, {
                            scrollTrigger: {
                                trigger: bgColorScroll,
                                scrub: 1,
                                pinSpacing: false,
                                start: 'top bottom',
                                end: "bottom top",
                            },
                            left: left + "px",
                            right: right + "px",
                            top: top + "px",
                            bottom: bottom + "px",
                            borderRadius: radius,
                            ease: "none"
                        });
                    }
                });
            }
        });
    }

    function frameflow_animation_btn($scope) {
        const $section = $scope.find('.pxl-video-player:not(.pxl-video-style-button) .pxl-video--inner');
        const cursor = $section.find('.btn-video-wrap.p-cursor')[0];

        if (!cursor || !$section.length || typeof gsap === 'undefined') return;

        if (cursor.__frameflowDestroy) {
            cursor.__frameflowDestroy();
        }

        const cursorW = cursor.offsetWidth / 2;
        const cursorH = cursor.offsetHeight / 2;

        const moveX = gsap.quickTo(cursor, "x", { duration: 0.25, ease: "power3.out" });
        const moveY = gsap.quickTo(cursor, "y", { duration: 0.25, ease: "power3.out" });

        function centerCursor() {
            moveX(($section.width() / 2) - cursorW);
            moveY(($section.height() / 2) - cursorH);
        }

        var rect = $section[0].getBoundingClientRect();
        function updateRect() {
            if ($section.length) {
                rect = $section[0].getBoundingClientRect();
            }
        }
        window.addEventListener('scroll', updateRect, { passive: true });
        window.addEventListener('resize', updateRect, { passive: true });

        function onMove(e) {
            if (
                e.clientX < rect.left ||
                e.clientX > rect.right ||
                e.clientY < rect.top ||
                e.clientY > rect.bottom
            ) return;

            moveX(e.clientX - rect.left - cursorW);
            moveY(e.clientY - rect.top - cursorH);
        }

        $section.on("mousemove", onMove);
        $section.on("mouseleave", centerCursor);

        centerCursor();

        cursor.__frameflowDestroy = () => {
            $section.off("mousemove", onMove);
            $section.off("mouseleave", centerCursor);
            window.removeEventListener('scroll', updateRect);
            window.removeEventListener('resize', updateRect);
        };
    }


    function frameflow_scroll_text($scope) {
        if (typeof gsap === 'undefined' || typeof SplitText === 'undefined' || typeof ScrollTrigger === 'undefined') {
            return;
        }

        const elements = $scope.find(".pxl-item--title.style-text-banner.style-2 .pxl-text-banner--caption");
        if (elements.length === 0) return;

        let mm = gsap.matchMedia();

        mm.add("(min-width: 768px)", () => {
            elements.each(function () {
                const el = this;

                // Cleanup previous animation and SplitText
                if (el.pxl_scroll_tween) {
                    el.pxl_scroll_tween.kill();
                    el.pxl_scroll_tween = null;
                }
                if (el.pxl_split) {
                    el.pxl_split.revert();
                    el.pxl_split = null;
                }

                const text = new SplitText(el, { type: 'words, chars' });
                el.pxl_split = text;

                $(text.words).children().first().addClass("first-char");

                el.pxl_scroll_tween = gsap.fromTo(text.chars,
                    {
                        position: 'relative',
                        display: 'inline-block',
                        opacity: 0.1,
                        x: -10,
                        willChange: 'opacity, transform'
                    },
                    {
                        opacity: 1,
                        x: 0,
                        stagger: 0.05,
                        ease: 'none',
                        scrollTrigger: {
                            trigger: el,
                            toggleActions: "play pause reverse pause",
                            start: "top 85%",
                            end: "top 45%",
                            scrub: 1,
                            onRefresh: () => {
                                // Added to combat initialization order issues
                                if (el.pxl_scroll_tween) el.pxl_scroll_tween.invalidate();
                            }
                        }
                    }
                );
            });

            return () => {
                // Cleanup on context revert
                elements.each(function () {
                    if (this.pxl_scroll_tween) this.pxl_scroll_tween.kill();
                    if (this.pxl_split) this.pxl_split.revert();
                });
            };
        });
    }

    function frameflow_event_timeline_handler($scope) {
        const $wrapper = $scope.find('.pxl-event-timeline-wrap');
        if ($wrapper.hasClass('js-initialized')) return;
        $wrapper.addClass('js-initialized');

        let refreshTimeout;
        const scheduleRefresh = (delay = 100) => {
            clearTimeout(refreshTimeout);
            refreshTimeout = setTimeout(() => {
                ScrollTrigger.refresh();
            }, delay);
        };

        $wrapper.find('.pxl-timeline--tab-item').on('click', function () {
            const targetDay = $(this).data('target');
            if (!targetDay) return;

            $(this).addClass('active').siblings().removeClass('active');
            $wrapper.find('.pxl-timeline--day-pane').removeClass('active');

            const $targetPane = $wrapper.find('[id="' + targetDay + '"]');
            if (!$targetPane.length) return;

            $targetPane.addClass('active');

            if (typeof window.initRipples === 'function') {
                setTimeout(() => {
                    window.initRipples($targetPane, true);
                    scheduleRefresh(300);
                }, 50);
            } else {
                scheduleRefresh();
            }
        });

        // Single comprehensive refresh after images load
        if (typeof $.fn.imagesLoaded === 'function') {
            $wrapper.imagesLoaded(() => scheduleRefresh(500));
        } else {
            scheduleRefresh(1000);
        }
    }

    function frameflow_scroll_checkp($scope) {
        if (!window.IntersectionObserver) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        $scope.find('.pxl-el-divider').each(function () {
            observer.observe(this);
        });
    }

    function renderOrbit($scope) {
        $scope.find(".orbit").each(function () {
            const $orbit = $(this);
            const settings = $orbit.data("settings") || {};
            const size = settings.size || 70;
            const count = settings.count || 1;
            const icons = Array.isArray(settings.icons) ? settings.icons : [];
            const itemClasses = Array.isArray(settings.itemClasses) ? settings.itemClasses : [];
            const iconClass = settings.iconClass || "green";
            const randomStart = !!settings.randomStart;
            const useRandomColor = !!settings.randomColor;
            const borderColor = settings.borderColor || null;

            $orbit.css("--d", size + "%").empty();
            if (borderColor) {
                $orbit.css("--orbit-border-color", borderColor);
            }

            const fragment = $(document.createDocumentFragment());

            const createItem = (idx) => {
                const dur = (6 + Math.random() * 6).toFixed(1) + "s";
                const $rot = $("<div>", { class: "rotator" }).css("--dur", dur);
                if (Math.random() > .5) $rot.addClass("reverse");

                let $item;
                let iconChar = icons.length ? icons[idx % icons.length] : "⭐";
                const iconStr = (typeof iconChar === "string") ? iconChar.trim() : "";
                const isSvg = iconStr.startsWith("<svg");
                const isImg = iconStr.startsWith("<img");
                let classes = ["item", "icon", iconClass];
                if (isSvg) classes.push("is-svg");
                if (isImg) classes.push("is-media");
                // Add class from itemClasses array if available
                if (itemClasses.length > 0) {
                    const itemClass = itemClasses[idx % itemClasses.length];
                    if (itemClass && itemClass.trim() !== '') {
                        classes.push(itemClass);
                    }
                }
                $item = $("<div>", {
                    class: classes.join(" ")
                }).html(iconChar);

                return $rot.append($item);
            };

            for (let i = 0; i < count; i++) {
                fragment.append(createItem(i));
            }

            $orbit.append(fragment);

            const $rotators = $orbit.find(".rotator");
            const c = $rotators.length;
            if (c) {
                const baseAngles = Array.from({ length: c }, (_, idx) => (360 / c) * idx);
                if (randomStart) {
                    for (let i = baseAngles.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [baseAngles[i], baseAngles[j]] = [baseAngles[j], baseAngles[i]];
                    }
                }
                $rotators.each(function (i) {
                    $(this).css("--start", baseAngles[i] + "deg");
                });

                const orbitWidth = $orbit.innerWidth();
                if (orbitWidth > 0) {
                    const radius = orbitWidth / 2;
                    const arcLength = (2 * Math.PI * radius) / c;
                    const maxIconSize = Math.max(28, Math.min(60, arcLength - 8));
                    $orbit.css("--orbit-icon-size", `${maxIconSize}px`);
                }
            }

            const randCol = () => '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
            $orbit.find('.item').each(function () {
                const $it = $(this);
                let color = null;

                if (useRandomColor) {
                    color = randCol();
                }

                if (color && !$it.hasClass('is-media')) {
                    if ($it.hasClass('is-svg')) {
                        $it.find('svg, path, circle, rect, polygon, polyline, ellipse, line')
                            .css({ fill: color, color });
                    } else {
                        $it.css('color', color);
                    }
                }
            });
        });
    }


    function frameflow_parallax_bg() {

        if (typeof $.fn.parallaxBackground === 'undefined') {
            console.warn('parallaxBackground plugin not loaded');
            return;
        }

        $(document).find('.pxl-parallax-background').parallaxBackground({
            event: 'mouse_move',
            animation_type: 'shift',
            animate_duration: 2
        });
        $(document).find('.pxl-pll-basic').parallaxBackground();
        $(document).find('.pxl-pll-rotate').parallaxBackground({
            animation_type: 'rotate',
            zoom: 50,
            rotate_perspective: 500
        });
        $(document).find('.pxl-pll-mouse-move').parallaxBackground({
            event: 'mouse_move',
            animation_type: 'shift',
            animate_duration: 2
        });
        $(document).find('.pxl-pll-mouse-move-rotate').parallaxBackground({
            event: 'mouse_move',
            animation_type: 'rotate',
            animate_duration: 1,
            zoom: 70,
            rotate_perspective: 1000
        });

        $(document).find('.pxl-bg-prx-effect-pinned-zoom-clipped').each(function (index, el) {

            if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
                return;
            }

            var $el = $(el);
            const clipped_bg_pinned = $el.find('.clipped-bg-pinned');
            const clipped_bg = $el.find('.clipped-bg');

            if (!clipped_bg_pinned.length || !clipped_bg.length) return;

            var clipped_bg_animation = gsap.to(clipped_bg, {
                scale: 1,
                duration: 1.1,
                ease: "cubic-bezier(0.25, 1, 0.5, 1)",
            });

            var clipped_bg_scene = ScrollTrigger.create({
                trigger: clipped_bg_pinned,
                start: function () {
                    const start_pin = 350;
                    return "top +=" + start_pin;
                },
                end: function () {
                    const end_pin = 0;
                    return "+=" + end_pin;
                },
                animation: clipped_bg_animation,
                scrub: 1,
                pin: true,
                pinSpacing: false,
            });

            function set_clipped_bg_wrapper_height() {
                if (typeof gsap !== 'undefined' && clipped_bg.length) {
                    gsap.set(clipped_bg, { height: window.innerHeight });
                }
            }

            var resizeHandler = set_clipped_bg_wrapper_height;
            window.addEventListener('resize', resizeHandler);

            // Cleanup khi element bị remove
            $el.on('remove', function () {
                window.removeEventListener('resize', resizeHandler);
                if (clipped_bg_scene && typeof ScrollTrigger !== 'undefined') {
                    clipped_bg_scene.kill();
                }
            });
        });



        $(document).find('.pxl-bg-prx-effect-pinned-circle-zoom-clipped').each(function (index, el) {
            // Kiểm tra GSAP và ScrollTrigger có tồn tại
            if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
                return;
            }

            const $el = $(el);

            var svg = $el.find('.circle-zoom-mask-svg');
            var img = $el.find('.clipped-bg-circle-pinned');
            let circle = $el.find('.circle-zoom');

            // Kiểm tra null/undefined
            if (!circle.length || !circle[0] || !img.length || !img[0]) return;

            let radiusAttr = circle[0].getAttribute("r");
            let radius = radiusAttr ? parseFloat(radiusAttr) : 0;
            if (isNaN(radius)) radius = 0;

            gsap.set(img[0], {
                scale: 2
            });

            var tl = gsap.timeline({
                scrollTrigger: {
                    trigger: el,
                    start: "50% 90%",
                    end: "80% 100%",
                    scrub: 2,
                },
                defaults: {
                    duration: 2
                }
            })
                .to(circle[0], {
                    attr: {
                        r: () => radius
                    }
                }, 0)
                .to(img[0], {
                    scale: 1,
                }, 0)
                .to(".circle-inner-layer", {
                    alpha: 0,
                    ease: "power1.in",
                    duration: 1 - 0.25
                }, 0.25);


            window.addEventListener("load", frameflow_circle_init);
            window.addEventListener("resize", frameflow_circle_resize);

            function frameflow_circle_init() {
                frameflow_circle_resize();
            }

            function frameflow_circle_resize() {
                if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

                tl.progress(0);
                var rect = $(el)[0].getBoundingClientRect();
                const rectWidth = rect.width;
                const rectHeight = rect.height;
                const dx = rectWidth / 2;
                const dy = rectHeight / 2;
                radius = Math.sqrt(dx * dx + dy * dy);

                tl.invalidate();
                ScrollTrigger.refresh();
            }

            // Cleanup khi element bị remove
            $el.on('remove', function () {
                window.removeEventListener("load", frameflow_circle_init);
                window.removeEventListener("resize", frameflow_circle_resize);
                if (tl && typeof ScrollTrigger !== 'undefined') {
                    tl.kill();
                }
            });
        });
    }

    function frameflow_post_carousel_handler($scope) {
        const rootEl = $scope && $scope[0] ? $scope[0] : document;
        const stepsContainer = rootEl.querySelector(".pxl-grid-filter.style-3");
        const pill = rootEl.querySelector("#pxl-item-step-active-pill");
        const steps = Array.from(rootEl.querySelectorAll(".filter-item"));

        if (!stepsContainer || !pill || !steps.length) return;

        const getActiveIndex = () => {
            const i = steps.findIndex((b) => b.classList.contains("active"));
            return i === -1 ? 0 : i;
        };

        const setActive = (index) => {
            index = Math.min(Math.max(index, 0), steps.length - 1);
            steps.forEach((b) => b.classList.remove("active"));
            steps[index].classList.add("active");
            updatePill(index);
        };

        const updatePill = (index = getActiveIndex()) => {
            index = Math.min(Math.max(index, 0), steps.length - 1);
            const containerRect = stepsContainer.getBoundingClientRect();
            const stepRect = steps[index].getBoundingClientRect();
            const stepWidth = stepRect.width || steps[index].offsetWidth || 0;
            const translateX = stepRect.left - containerRect.left;

            pill.style.width = `${Math.max(0, stepWidth - 2)}px`;
            pill.style.transform = `translateX(${translateX}px)`;
        };

        steps.forEach((btn, index) => {
            btn.addEventListener("click", () => {
                setActive(index);
            });
        });

        setActive(getActiveIndex());

        const onResize = () => {
            if (rootEl !== document && !rootEl.isConnected) {
                window.removeEventListener("resize", onResize);
                return;
            }
            updatePill();
        };

        window.addEventListener("resize", onResize, { passive: true });

    }


    function setupGlowAnimation($scope) {
        var $wrapper = $scope.find(".pxl-section-mouse-follower");
        if (!$wrapper.length) return;

        // Check if already initialized
        if ($wrapper.data("glow-animation-initialized")) return;

        var $mouseGlow = $wrapper.find(".pxl-section-mouse-follower-shape1");
        var $blobPurple = $wrapper.find(".pxl-section-mouse-follower-shape2");

        // Create HTML elements if they don't exist
        if (!$mouseGlow.length) {
            $mouseGlow = $('<div class="pxl-section-mouse-follower-shape1"></div>');
            $wrapper.append($mouseGlow);
        }
        if (!$blobPurple.length) {
            $blobPurple = $('<div class="pxl-section-mouse-follower-shape2"></div>');
            $wrapper.append($blobPurple);
        }

        var state = {
            glowIntensity: 0,
            mouseX: 0,
            mouseY: 0,
            isActive: false,
            rafId: null,
            isRunning: true,
            isAnimating: false
        };

        // Unique namespace for this wrapper
        var namespace = "pxlGlow_" + ($wrapper[0] ? $wrapper[0].getAttribute('data-id') || Date.now() : Date.now());

        var rect = $wrapper[0] ? $wrapper[0].getBoundingClientRect() : null;
        function updateRect() {
            rect = $wrapper[0] ? $wrapper[0].getBoundingClientRect() : null;
        }
        window.addEventListener('resize', updateRect, { passive: true });
        window.addEventListener('scroll', updateRect, { passive: true });

        function handleMouseMove(e) {
            if (!state.isRunning) return;

            if (!rect) updateRect();
            if (!rect) return;

            var inside = e.clientX >= rect.left && e.clientX <= rect.right && e.clientY >= rect.top && e.clientY <= rect.bottom;
            if (!inside) {
                state.isActive = false;
                $mouseGlow.css("opacity", 0);
                return;
            }

            state.isActive = true;
            state.mouseX = e.clientX - rect.left;
            state.mouseY = e.clientY - rect.top;

            $mouseGlow.css({
                left: state.mouseX + "px",
                top: state.mouseY + "px",
                opacity: 1
            });
            $blobPurple.css("opacity", 1);

            if (!state.isAnimating) {
                state.isAnimating = true;
                animate();
            }
        }

        // Bind mouse move event with unique namespace
        $(document).on("mousemove." + namespace, handleMouseMove);

        function animate() {
            if (!state.isRunning) return;

            // Safety check: if element is removed from DOM, clean up
            if ($wrapper[0] && !$wrapper[0].isConnected) {
                var cleanup = $wrapper.data("glow-animation-cleanup");
                if (cleanup) cleanup();
                return;
            }

            state.glowIntensity = (state.glowIntensity + 0.06) % (Math.PI * 2);

            if (!state.isActive) {
                state.isAnimating = false;
                return;
            }

            var scale = 1 + Math.sin(state.glowIntensity) * 0.2;
            $mouseGlow.css("transform", "translate(-50%, -50%) scale(" + scale + ")");

            var blobScale = 1 + Math.cos(state.glowIntensity) * 0.15;
            $blobPurple.css("transform", "translate(-50%, -50%) scale(" + blobScale + ")");

            state.rafId = requestAnimationFrame(animate);
        }

        // Start animation
        // state.rafId = requestAnimationFrame(animate); // Removed auto-start

        // Mark as initialized
        $wrapper.data("glow-animation-initialized", true);

        // Cleanup function
        $wrapper.data("glow-animation-cleanup", function () {
            state.isRunning = false;
            if (state.rafId) {
                cancelAnimationFrame(state.rafId);
                state.rafId = null;
            }
            window.removeEventListener('resize', updateRect);
            window.removeEventListener('scroll', updateRect);
            $(document).off("mousemove." + namespace);
            $wrapper.removeData("glow-animation-initialized");
            $wrapper.removeData("glow-animation-cleanup");
        });

        // Cleanup when element is removed
        $wrapper.on('remove', function () {
            var cleanup = $wrapper.data("glow-animation-cleanup");
            if (cleanup && typeof cleanup === 'function') {
                cleanup();
            }
        });
    }


    function wglPhysicsButton($scope) {
        if (typeof Matter === 'undefined') return;

        try {
            const {
                Engine,
                Render,
                Runner,
                Bodies,
                Composite,
                MouseConstraint,
                Events,
                Body
            } = Matter;

            const logoArea = $scope[0].querySelector(".pxl-button_physics");
            if (!logoArea) return;


            try {
                const rect = logoArea.getBoundingClientRect();
                const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                const isVisible = rect.top < viewportHeight && rect.bottom > 0;
                if (!isVisible) {
                    if (typeof IntersectionObserver !== 'undefined') {
                        const observer = new IntersectionObserver(function (entries, obs) {
                            for (var i = 0; i < entries.length; i++) {
                                if (entries[i].isIntersecting) {
                                    obs.unobserve(entries[i].target);
                                    wglPhysicsButton($scope);
                                    break;
                                }
                            }
                        }, { root: null, threshold: 0.1 });
                        observer.observe(logoArea);
                    } else {
                        var onScroll = function onScroll() {
                            var r = logoArea.getBoundingClientRect();
                            var vh = window.innerHeight || document.documentElement.clientHeight;
                            if (r.top < vh && r.bottom > 0) {
                                window.removeEventListener('scroll', onScroll, { passive: true });
                                wglPhysicsButton($scope);
                            }
                        };
                        window.addEventListener('scroll', onScroll, { passive: true });
                    }
                    return;
                }
            } catch (e) {
            }

            if (typeof logoArea.destroyPhysics === "function") {
                logoArea.destroyPhysics();
            }

            let settings = [];
            try {
                const dataAttr = logoArea.getAttribute("data-settings");
                settings = dataAttr ? JSON.parse(dataAttr.replace(/&quot;/g, '"')) : [];
            } catch (e) {
                console.error("Invalid data-settings:", e);
                return;
            }

            let icons = [];
            try {
                const dataIcons = logoArea.getAttribute("data-icons");
                icons = dataIcons ? JSON.parse(dataIcons.replace(/&quot;/g, '"')) : [];
            } catch (e) {
                console.error("Invalid data-icons:", e);
            }

            let w = logoArea.offsetWidth;
            let h = logoArea.offsetHeight;

            const engine = Engine.create();
            engine.world.gravity.x = 0;
            engine.world.gravity.y = 0.35;

            const MAX_VELOCITY = 8;
            const VELOCITY_DAMPING = 0.98;

            const render = Render.create({
                element: logoArea,
                engine: engine,
                options: {
                    width: w,
                    height: h,
                    background: "rgba(0,0,0,0)",
                    wireframes: false,
                    pixelRatio: window.devicePixelRatio
                }
            });

            const wallOptions = { isStatic: true, render: { visible: false } };
            const ceiling = Bodies.rectangle(w / 2, -10, w, 10, wallOptions);
            const ground = Bodies.rectangle(w / 2, h + 10, w, 10, wallOptions);
            const leftWall = Bodies.rectangle(-10, h / 2, 10, h, wallOptions);
            const rightWall = Bodies.rectangle(w + 10, h / 2, 10, h, wallOptions);

            const shapes = [];

            const cols = Math.ceil(Math.sqrt(settings.length));
            const spacingX = w / (cols + 1);
            const spacingY = 100;

            settings.forEach((value, index) => {
                const col = index % cols;
                const row = Math.floor(index / cols);
                const x = spacingX * (col + 1);
                const y = 60 + row * spacingY;

                const textElement = document.createElement("p");
                textElement.className = "pxl-throwable-element";
                Object.assign(textElement.style, {
                    opacity: "1",
                    position: "absolute",
                    display: "inline-flex",
                    alignItems: "center",
                    gap: "8px",
                    textAlign: "center",
                    pointerEvents: "none",
                    whiteSpace: "nowrap"
                });

                let iconNode = null;
                const iconData = Array.isArray(icons) ? icons[index] : null;
                try {
                    const isString = (v) => typeof v === 'string' && v.trim().length > 0;
                    const looksLikeSvg = (v) => isString(v) && v.trim().startsWith('<');
                    const looksLikeUrl = (v) =>
                        isString(v) &&
                        (/^(https?:)?\/\//.test(v) || /\.(svg|png|jpe?g|gif|webp)(\?.*)?$/i.test(v));

                    const makeSvgNode = (svgStr) => {
                        const span = document.createElement('span');
                        span.className = 'pxl-icon-svg pxl-icon';
                        span.style.display = 'inline-flex';
                        span.style.alignItems = 'center';
                        span.innerHTML = svgStr;
                        const svg = span.querySelector('svg');
                        if (svg) {
                            svg.setAttribute('width', 'clamp(60px, 22vw, 150px)');
                            svg.setAttribute('height', 'clamp(60px, 22vw, 150px)');
                            svg.style.width = 'clamp(60px, 22vw, 150px)';
                            svg.style.height = 'clamp(60px, 22vw, 150px)';
                            svg.style.borderRadius = '50%';
                            svg.style.display = 'block';
                            svg.style.verticalAlign = 'middle';
                            svg.style.pointerEvents = 'none';
                        }
                        return span;
                    };
                    const makeImgNode = (url) => {
                        const container = document.createElement('div');
                        container.className = 'pxl-icon';
                        container.style.width = 'clamp(60px, 25vw, 190px)';
                        container.style.height = 'clamp(60px, 25vw, 190px)';
                        container.style.borderRadius = '50%';
                        container.style.display = 'flex';
                        container.style.alignItems = 'center';
                        container.style.justifyContent = 'center';
                        container.style.overflow = 'hidden';

                        const img = document.createElement('img');
                        img.src = url;
                        img.alt = '';
                        img.style.width = 'clamp(60px, 15vw, 100%)';
                        img.style.height = 'clamp(60px, 15vw, 100%)';
                        img.style.objectFit = 'contain';
                        img.style.objectPosition = 'center';
                        img.style.display = 'block';

                        container.appendChild(img);
                        return container;
                    };
                    const makeClassIcon = (cls) => {
                        const i = document.createElement('i');
                        i.className = cls.trim() + ' pxl-icon';
                        i.style.fontSize = 'clamp(60px, 22vw, 150px)';
                        i.style.lineHeight = '1';
                        i.style.borderRadius = '50%';
                        return i;
                    };

                    if (iconData && typeof iconData === 'object') {
                        const rawValue = iconData.value || iconData.class || null;
                        const svgStr = iconData.svg || iconData.SVG || null;
                        const urlVal = iconData.url || iconData.URL || null;

                        if (looksLikeSvg(svgStr)) iconNode = makeSvgNode(svgStr);
                        else if (urlVal && looksLikeUrl(urlVal)) iconNode = makeImgNode(urlVal);
                        else if (isString(rawValue)) {
                            if (looksLikeSvg(rawValue)) iconNode = makeSvgNode(rawValue);
                            else if (looksLikeUrl(rawValue)) iconNode = makeImgNode(rawValue);
                            else iconNode = makeClassIcon(rawValue);
                        } else if (rawValue && typeof rawValue === 'object') {
                            const nestedUrl = rawValue.url || rawValue.URL || null;
                            const nestedSvg = rawValue.svg || rawValue.SVG || null;
                            if (looksLikeSvg(nestedSvg)) iconNode = makeSvgNode(nestedSvg);
                            else if (nestedUrl && looksLikeUrl(nestedUrl)) iconNode = makeImgNode(nestedUrl);
                        }
                    } else if (isString(iconData)) {
                        if (looksLikeSvg(iconData)) iconNode = makeSvgNode(iconData);
                        else if (looksLikeUrl(iconData)) iconNode = makeImgNode(iconData);
                        else iconNode = makeClassIcon(iconData);
                    }
                } catch (e) {
                }

                const spanElement = document.createElement("span");
                spanElement.className = "span-element-rot";
                spanElement.textContent = value;

                if (iconNode) textElement.appendChild(iconNode);
                textElement.appendChild(spanElement);

                const hasText = typeof value === 'string' && value.trim().length > 0;
                const hasIcon = !!iconNode;
                logoArea.appendChild(textElement);

                const measuredWidth = Math.max(40, Math.ceil(textElement.offsetWidth));
                const measuredHeight = Math.max(24, Math.ceil(textElement.offsetHeight));

                const commonOpts = {
                    restitution: 0.2,
                    friction: 0.3,
                    frictionStatic: 0.8,
                    frictionAir: 0.02,
                    slop: 0.001,
                    render: { visible: false }
                };

                let body;
                if (!hasText) {
                    const radius = Math.max(8, Math.ceil(Math.max(measuredWidth, measuredHeight) / 2));
                    body = Bodies.circle(x, y, radius, commonOpts);
                } else {
                    const chamferRadius = Math.min(Math.floor(measuredHeight / 2), 22);
                    const wBody = measuredWidth > 100 ? measuredWidth - 1 : measuredWidth;
                    const hBody = measuredHeight > 100 ? measuredHeight - 1 : measuredHeight;
                    body = Bodies.rectangle(x, y, wBody, hBody, {
                        ...commonOpts,
                        chamfer: { radius: chamferRadius }
                    });
                }

                setTimeout(() => {
                    const angle = Math.random() * Math.PI * 2;
                    const forceMagnitude = 0.02 + Math.random() * 0.03;
                    Body.applyForce(body, body.position, {
                        x: Math.cos(angle) * forceMagnitude,
                        y: Math.sin(angle) * forceMagnitude
                    });
                }, Math.random() * 1000);

                shapes.push({ body, element: textElement });
            });

            const mouseControl = MouseConstraint.create(engine, {
                element: logoArea,
                constraint: { render: { visible: false } }
            });

            logoArea.addEventListener('wheel', (e) => {
            }, { passive: true });

            logoArea.addEventListener('mousedown', (e) => {
                if (e.button === 1) { // Middle mouse button
                    e.preventDefault();
                }
            });

            logoArea.addEventListener('touchstart', (e) => {
            }, { passive: true });

            logoArea.addEventListener('touchmove', (e) => {

            }, { passive: true });

            Composite.add(engine.world, [
                ground, ceiling, rightWall, leftWall,
                mouseControl, ...shapes.map(s => s.body)
            ]);

            Render.run(render);
            const runner = Runner.create();
            Runner.run(runner, engine);

            Events.on(engine, "afterUpdate", () => {
                shapes.forEach(({ body, element }) => {
                    const velocity = body.velocity;
                    const speed = Math.sqrt(velocity.x * velocity.x + velocity.y * velocity.y);

                    if (speed > MAX_VELOCITY) {
                        const scale = MAX_VELOCITY / speed;
                        Body.setVelocity(body, {
                            x: velocity.x * scale,
                            y: velocity.y * scale
                        });
                    }

                    if (speed > 3) {
                        Body.setVelocity(body, {
                            x: velocity.x * VELOCITY_DAMPING,
                            y: velocity.y * VELOCITY_DAMPING
                        });
                    }

                    const buffer = 100;
                    const isOutOfBounds =
                        body.position.x < -buffer ||
                        body.position.x > w + buffer ||
                        body.position.y > h + buffer;

                    if (isOutOfBounds) {
                        Body.setPosition(body, {
                            x: Math.max(buffer, Math.min(w - buffer, body.position.x)),
                            y: Math.max(buffer, Math.min(h - buffer, body.position.y))
                        });
                        Body.setVelocity(body, { x: 0, y: 0 });
                    }

                    element.style.display = isOutOfBounds ? "none" : "block";
                    // element.style.left = `${body.position.x}px`;
                    // element.style.top = `${body.position.y}px`;
                    // element.style.transform = `translate(-50%, -50%) rotate(${body.angle}rad)`;
                    element.style.left = '0px';
                    element.style.top = '0px';
                    element.style.transform = `translate3d(${body.position.x}px, ${body.position.y}px, 0px) translate(-50%, -50%) rotate(${body.angle}rad)`;
                });
            });

            const resizeHandler = () => {
                if (!logoArea.isConnected) {
                    logoArea.destroyPhysics();
                    return;
                }
                w = logoArea.offsetWidth;
                h = logoArea.offsetHeight;
                render.canvas.width = w;
                render.canvas.height = h;
                render.options.pixelRatio = window.devicePixelRatio;

                Body.setPosition(ceiling, { x: w / 2, y: -10 });
                Body.setPosition(ground, { x: w / 2, y: h + 10 });
                Body.setPosition(leftWall, { x: -10, y: h / 2 });
                Body.setPosition(rightWall, { x: w + 10, y: h / 2 });
            };
            window.addEventListener("resize", resizeHandler);

            logoArea.destroyPhysics = function () {
                Render.stop(render);
                Runner.stop(runner);
                Composite.clear(engine.world);
                Engine.clear(engine);
                render.canvas.remove();
                render.textures = {};
                shapes.forEach(({ element }) => {
                    if (element && element.parentNode) {
                        element.parentNode.removeChild(element);
                    }
                });
                window.removeEventListener("resize", resizeHandler);
            };
        } catch (err) {
            console.warn("Physics button error:", err);
        }
    }

    function frameflow_scroll_trigger_circle($scope) {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

        const textElements = $scope.find('.pxl-case-badge .text-circle');
        if (!textElements.length) return;

        gsap.utils.toArray(textElements).forEach(text => {
            gsap.to(text, {
                rotation: 360,
                ease: 'none',
                scrollTrigger: {
                    trigger: text,
                    start: 'center 80%',
                    end: 'center 20%',
                    scrub: 1, // Smoother than 'true'
                },
            });
        });
    }

    function initImageCarousel($scope) {
        if (typeof ScrollTrigger !== 'undefined') gsap.registerPlugin(ScrollTrigger);

        const $carousel = $scope.find(".pxl-image-carousel5");
        const $cardsDiv = $carousel.find(".pxl-item-wrapper");
        const cards = [...$cardsDiv.children(".pxl-item--inner")];

        if (!$carousel.length || cards.length === 0) return;

        const id = $carousel.attr('id') || Math.random().toString(36).substr(2, 9);
        const ns = ".carousel_" + id;
        const nsGlobal = ".carouselGlobal_" + id;

        const n = cards.length;
        const mid = Math.floor(n / 2);

        let dir = 1;

        /* ---------------------------------------------------------
         * 1. Prepare stacked state — NO DOM READ per item
         * --------------------------------------------------------- */
        gsap.set(cards, {
            xPercent: -50,
            rotate: 0,
            scale: 0.8,
            transformOrigin: "50% 300%",
            opacity: 0,
            position: "absolute",
            left: "50%",
            top: "50%"
        });

        cards.forEach((c, i) => {
            const distance = i - mid;
            gsap.set(c, { zIndex: n - Math.abs(distance) });
        });

        /* ---------------------------------------------------------
         * 2. ScrollTrigger: expand effect (giữ nguyên hiệu ứng)
         * --------------------------------------------------------- */
        const trigger = ScrollTrigger.create({
            trigger: $carousel[0],
            start: "top 80%",
            once: true,
            onEnter: () => {
                cards.forEach((c, i) => {
                    const distance = i - mid;
                    gsap.to(c, {
                        opacity: 1,
                        rotate: distance * 6,
                        scale: 1 - Math.abs(distance) * 0.01,
                        duration: 1.2,
                        delay: i * 0.05,
                        ease: "power4.out"
                    });
                });
            }
        });

        /* ---------------------------------------------------------
         * 3. Custom cursor (giữ nguyên)
         * --------------------------------------------------------- */
        const $cursor = $scope.find(".cursor");
        const cursorX = gsap.quickTo($cursor, "x", { ease: "power4", duration: .4 });
        const cursorY = gsap.quickTo($cursor, "y", { ease: "power4", duration: .4 });
        const cursorDir = gsap.quickSetter($cursor, "scaleX");

        $carousel.on("pointerenter" + ns, () =>
            gsap.to($cursor, { opacity: 1, duration: .2 })
        );

        $carousel.on("pointerleave" + ns, () => {
            moveLoop.pause();
            gsap.to($cursor, { opacity: 0, duration: .2 });
        });

        $carousel.on("pointermove" + ns, (e) => {
            dir = e.clientX < window.innerWidth / 2 ? -1 : 1;
            cursorDir(dir);
            cursorX(e.clientX);
            cursorY(e.clientY);
        });

        /* ---------------------------------------------------------
         * 4. Auto move LOOP — now purely GSAP, NO DOM REORDER
         * --------------------------------------------------------- */

        // Precompute positions (-mid → mid)
        let positions = cards.map((_, i) => i - mid);

        function move() {
            if (dir > 0) {
                positions.unshift(positions.pop());
            } else {
                positions.push(positions.shift());
            }

            cards.forEach((card, i) => {
                const dist = positions[i];
                const props = {
                    rotate: dist * 6,
                    scale: 1 - Math.abs(dist) * 0.01,
                    zIndex: n - Math.abs(dist),
                    duration: 0.45,
                    ease: "power2.out",
                };
                gsap.to(card, props);
            });
        }

        const moveLoop = gsap.to({}, {
            paused: true,
            repeat: -1,
            duration: .15,
            onRepeat: move,
            onStart: move
        });

        $carousel.on("pointerdown" + ns, () => moveLoop.play(0));
        $(document).on("pointerup" + nsGlobal, () => moveLoop.pause());

        /* ---------------------------------------------------------
         * 5. Cleanup
         * --------------------------------------------------------- */
        $scope.on('remove', () => {
            moveLoop.kill();
            trigger && trigger.kill();
            gsap.killTweensOf($cursor);
            gsap.killTweensOf(cards);
            $carousel.off(ns);
            $(document).off(nsGlobal);
        });
    }

    $(document).ready(function () {
        if (window.elementorFrontend && typeof elementorFrontend.waypoint === 'function') {
            frameflow_animation_handler();
        }
        frameflow_section_divider_glow();
        frameflow_border_glow();
    });

    $(window).on('elementor/frontend/init', function () {
        // Bổ sung waypoint nếu thiếu
        frameflow_polyfill_waypoint();
        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            frameflow_scroll_checkp($scope);
            frameflow_animation_handler($scope);
            frameflow_section_divider_glow($scope);
            frameflow_border_glow($scope);
            renderOrbit($scope);
            setupGlowAnimation($scope);
            frameflow_animation_btn($scope);
        });
        frameflow_section_start_render();
        frameflow_parallax_bg();
        frameflow_css_inline_js();
        frameflow_section_before_render();
        frameflow_zoom_point();
        frameflow_scroll_fixed_section();
        elementorFrontend.hooks.addAction('frontend/element_ready/pxl_contact_form.default', PXL_Icon_Contact_Form);
        elementorFrontend.hooks.addAction('frontend/element_ready/pxl_heading.default', function ($scope) {
            frameflow_split_text($scope);
            frameflow_scroll_text($scope);
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/pxl_event_timeline.default', function ($scope) {
            frameflow_event_timeline_handler($scope);
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/pxl_post_carousel.default', function ($scope) {
            frameflow_post_carousel_handler($scope);
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/physics_item.default', function ($scope) {
            wglPhysicsButton($scope);
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/pxl_pricing.default', function ($scope) {
            pxl_pricing_handler($scope);
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/pxl_case_badge.default', function ($scope) {
            frameflow_scroll_trigger_circle($scope);
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/pxl_image_carousel.default', function ($scope) {
            initImageCarousel($scope);
            frameflow_card_carousel($scope);
        });
    });

    function pxl_pricing_handler($scope) {
        $scope.find('.pxl-pricing2').each(function () {
            var $container = $(this);
            var $quantityInput = $container.find('input.qty');
            var $subtotal = $container.find('.pxl-ticket-subtotal');
            var $priceLabel = $container.find('.pxl-ticket-price-main');
            var price = parseFloat($container.data('price')) || 0;
            var currency = $container.data('currency') || '$';

            function updateSubtotal() {
                var qty = parseInt($quantityInput.val()) || 1;
                var total = price * qty;
                // Simple formatting
                var formattedTotal = currency + total.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 });
                $subtotal.html(formattedTotal);
            }

            $container.on('click', '.pxl-qty-up', function (e) {
                e.preventDefault();
                var val = parseInt($quantityInput.val()) || 1;
                $quantityInput.val(val + 1).trigger('change');
            });

            $container.on('click', '.pxl-qty-down', function (e) {
                e.preventDefault();
                var val = parseInt($quantityInput.val()) || 1;
                if (val > 1) {
                    $quantityInput.val(val - 1).trigger('change');
                }
            });

            $quantityInput.on('change', function () {
                updateSubtotal();
                // Update Add to Cart link
                var $btn = $container.find('.pxl-add-to-cart');
                var href = $btn.attr('href');
                if (href && href.indexOf('add-to-cart=') !== -1) {
                    var newHref = href.split('&quantity=')[0] + '&quantity=' + $(this).val();
                    $btn.attr('href', newHref);
                }
            });
        });
    }
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined' && typeof ScrollTrigger.normalizeScroll === 'function') {
        gsap.registerPlugin(ScrollTrigger);
        try {
            ScrollTrigger.normalizeScroll({ allowNestedScroll: true });
        } catch (e) { console.warn(e); }
    }

    // Global refresh on window load to ensure all widgets are accounted for
    $(window).on('load', function () {
        if (typeof ScrollTrigger !== 'undefined') {
            setTimeout(() => {
                ScrollTrigger.refresh();
            }, 500);
        }
    });
})(jQuery);
