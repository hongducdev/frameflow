; (function ($) {

    "use strict";

    var pxl_scroll_top;
    var pxl_window_height;
    var pxl_window_width;
    var pxl_scroll_status = '';
    var pxl_last_scroll_top = 0;

    var scrollTimeout = null;
    var scrollRAF = null;
    var $pinSpacer = null;

    var resizeTimeout = null;

    $(window).on('load', function () {
        setTimeout(function () {
            $(".pxl-loader").addClass("is-loaded");
        }, 60);
        $('.pxl-swiper-slider, .pxl-header-mobile-elementor').css('opacity', '1');
        pxl_window_width = $(window).width();
        pxl_window_height = $(window).height();
        $pinSpacer = $('.elementor > .pin-spacer');
        frameflow_header_sticky();
        frameflow_header_mobile();
        frameflow_scroll_to_top();
        frameflow_footer_fixed();
        // frameflow_check_scroll();
        dropdown_offices();
        frameflow_shop_quantity();
        frameflow_submenu_responsive();
        frameflow_panel_anchor_toggle();
        frameflow_slider_column_offset();
        frameflow_height_ct_grid();
        frameflow_shop_view_layout();
        frameflow_menu_divider_move();
        frameflow_el_parallax();
        setTimeout(function () {
            if (typeof initTeamGridUrlState === 'function') {
                initTeamGridUrlState();
            }
        }, 400);
    });

    $(window).on('scroll', function () {
        if (scrollRAF) {
            cancelAnimationFrame(scrollRAF);
        }

        scrollRAF = requestAnimationFrame(function () {
            pxl_scroll_top = $(window).scrollTop();
            // pxl_window_height/width updated in resize event

            if (pxl_scroll_top < pxl_last_scroll_top) {
                pxl_scroll_status = 'up';
            } else {
                pxl_scroll_status = 'down';
            }
            pxl_last_scroll_top = pxl_scroll_top;

            frameflow_header_sticky();
            frameflow_scroll_to_top();
            frameflow_backtotop_update();
            frameflow_zoom_point_update();
            // frameflow_check_scroll();
            frameflow_ptitle_scroll_opacity();

            if (pxl_scroll_top < 100) {
                if ($pinSpacer && $pinSpacer.length) {
                    $pinSpacer.removeClass('scroll-top-active');
                }
            }

            scrollRAF = null;
        });
    });

    $(window).on('resize', function () {
        if (resizeTimeout) {
            clearTimeout(resizeTimeout);
        }

        resizeTimeout = setTimeout(function () {
            pxl_window_height = $(window).height();
            pxl_window_width = $(window).width();
            frameflow_submenu_responsive();
            frameflow_height_ct_grid();
            frameflow_header_mobile();
            frameflow_slider_column_offset();
            frameflow_zoom_point();
            setTimeout(function () {
                frameflow_menu_divider_move();
            }, 500);

            resizeTimeout = null;
        }, 150);
    });

    $(document).ready(function () {
        pxl_window_width = $(window).width();
        frameflow_backtotop_progess_bar();
        frameflow_type_file_upload();
        frameflow_zoom_point();
        if (pxl_window_width > 767) {
            frameflow_button_parallax();
            frameflow_button_parallax1();
            HeightTitles();
        }

        setTimeout(function () {
            $('.pxl-section-bg-parallax').closest('.elementor-element').addClass('pxl-section-parallax-overflow');
        }, 500);

        $('.pxl-circle-svg svg').each(function () {
            var linearGradient = $(this).find('.linear-dot1');
            if (linearGradient.length > 0) {
                var linearGradientId = linearGradient.attr('id');
            }
            var linearGradient1 = $(this).find('.linear-dot2');
            if (linearGradient1.length > 0) {
                var linearGradientId1 = linearGradient1.attr('id');
            }
            frameflow_circle_svg(this, linearGradientId, linearGradientId1);
        });

        let runningColumnAnimations = 0;
        const maxColumnAnimations = 4;

        function animateColumn(colId, speed) {
            if (runningColumnAnimations >= maxColumnAnimations) return;

            const $col = $('#' + colId);
            if (!$col.length || $col.data('colTween')) return;

            const slideHeight = $col.outerHeight() / 2;
            if (slideHeight <= 0) return;

            const tween = gsap.to($col[0], {
                y: -slideHeight,
                ease: "none",
                duration: speed * 0.5,
                repeat: -1,
                modifiers: {
                    y: gsap.utils.unitize(y => parseFloat(y) % slideHeight)
                },
                onStart: () => runningColumnAnimations++,
                onKill: () => {
                    runningColumnAnimations = Math.max(0, runningColumnAnimations - 1);
                    $col.removeData('colTween');
                }
            });

            $col.data('colTween', tween);
        }

        if (pxl_window_width > 767) {
            setTimeout(() => {
                if ($('#col1').length) animateColumn("col1", 34);
            }, 100);

            setTimeout(() => {
                if ($('#col2').length) animateColumn("col2", 32);
            }, 200);

            setTimeout(() => {
                if ($('#col3').length) animateColumn("col3", 30);
            }, 300);

            setTimeout(() => {
                if ($('#col4').length) animateColumn("col4", 28);
            }, 400);
        }

        /* Section Particles - chỉ chạy khi thư viện tồn tại và có phần tử sử dụng */
        setTimeout(function () {
            var $rowsParticles = $(".pxl-row-particles");
            if (typeof particlesJS !== 'function' || !$rowsParticles.length) {
                return;
            }

            // Kiểm tra thiết bị mobile/tablet
            var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth <= 768;

            if (isMobile) {
                // Ẩn particles trên mobile
                $rowsParticles.hide();
                return;
            }

            // Chỉ chạy particlesJS trên desktop
            $rowsParticles.each(function () {
                particlesJS($(this).attr('id'), {
                    "particles": {
                        "number": {
                            "value": $(this).data('number'),
                        },
                        "color": {
                            "value": $(this).data('color')
                        },
                        "shape": {
                            "type": "circle",
                        },
                        "size": {
                            "value": $(this).data('size'),
                            "random": $(this).data('size-random'),
                        },
                        "line_linked": {
                            "enable": false,
                        },
                        "move": {
                            "enable": true,
                            "speed": 2,
                            "direction": $(this).data('move-direction'),
                            "random": true,
                            "out_mode": "out",
                        }
                    },
                    "retina_detect": true
                });
            });
        }, 400);

        /* Start Menu Mobile */
        $('.pxl-header-menu li.menu-item-has-children').each(function () {
            if ($(this).find('.pxl-menu-toggle').length === 0) {
                $(this).append('<span class="pxl-menu-toggle"></span>');
            }
        });
        $(document).on('click.pxl_menu', '.pxl-menu-toggle', function (e) {
            e.preventDefault();
            var $toggle = $(this);
            if ($toggle.hasClass('active')) {
                $toggle.closest('ul').find('.pxl-menu-toggle.active').not($toggle).toggleClass('active');
                $toggle.closest('ul').find('.sub-menu.active').toggleClass('active').slideToggle();
            } else {
                $toggle.closest('ul').find('.pxl-menu-toggle.active').toggleClass('active');
                $toggle.closest('ul').find('.sub-menu.active').toggleClass('active').slideToggle();
                $toggle.toggleClass('active');
                $toggle.parent().find('> .sub-menu').toggleClass('active').slideToggle();
            }
        });

        $('li.pxl-megamenu').on('mouseenter.pxl_mega', function () {
            $(this).parents('.elementor-element').addClass('section-mega-active')
        }).on('mouseleave.pxl_mega', function () {
            $(this).parents('.elementor-element').removeClass('section-mega-active')
        });

        $(document).on('click.pxl_mobile_nav', "#pxl-nav-mobile, .pxl-anchor-mobile-menu", function (e) {
            e.preventDefault();
            $(this).toggleClass('active');
            $('body').toggleClass('body-overflow');
            $('.pxl-header-menu').toggleClass('active');
        });

        $(document).on('click.pxl_mobile_close', ".pxl-menu-close, .pxl-header-menu-backdrop, #pxl-header-mobile .pxl-menu-primary a.is-one-page", function (e) {
            // Check if is-one-page link, maybe preventDefault not needed or only if not link
            if ($(this).hasClass('is-one-page')) {
                // let it navigate
            } else {
                e.preventDefault();
            }
            $(this).parents('.pxl-header-main').find('.pxl-header-menu').removeClass('active');
            $('#pxl-nav-mobile').removeClass('active');
            $('body').toggleClass('body-overflow');
        });
        /* End Menu Mobile */

        /* Menu Vertical */
        $('.pxl-nav-vertical li.menu-item-has-children > a').append('<span class="pxl-arrow-toggle"><i class="bi-chevron-right"></i></span>');
        $('.pxl-nav-vertical li.menu-item-has-children > a').on('click', function () {
            if ($(this).hasClass('active')) {
                $(this).next().toggleClass('active').slideToggle();
            } else {
                $(this).closest('ul').find('.sub-menu.active').toggleClass('active').slideToggle();
                $(this).closest('ul').find('a.active').toggleClass('active');
                $(this).find('.pxl-menu-toggle.active').toggleClass('active');
                $(this).toggleClass('active');
                $(this).next().toggleClass('active').slideToggle();
            }
        });

        $(".comments-area .btn-submit").append(`
          <span class="btn-icon-left">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none"><path d="M9.6 12.7999C9.39526 12.7999 9.19053 12.7219 9.03432 12.5657C8.7219 12.2532 8.7219 11.7467 9.03432 11.4343L13.2686 7.19999H0.800009C0.358159 7.19999 0 6.8418 0 6.39998C0 5.95813 0.358159 5.59997 0.800009 5.59997H13.2686L9.03432 1.36567C8.7219 1.05326 8.7219 0.546725 9.03432 0.234311C9.3467 -0.0781035 9.8533 -0.0781035 10.1657 0.234311L15.7657 5.8343L15.7674 5.83604C15.7677 5.83632 15.768 5.83667 15.7683 5.83695C15.7686 5.83723 15.7688 5.83751 15.7691 5.83778C15.7695 5.8382 15.7699 5.83862 15.7703 5.83904C15.7705 5.83918 15.7706 5.83932 15.7708 5.83949C15.7713 5.84005 15.7718 5.84057 15.7724 5.84109L15.7724 5.84116C15.8444 5.91483 15.8992 5.9989 15.937 6.08847C15.9371 6.08872 15.9372 6.089 15.9373 6.08924C15.9374 6.08952 15.9376 6.08983 15.9377 6.09011C15.9778 6.18543 15.9999 6.29015 15.9999 6.40002C15.9999 6.50989 15.9778 6.61461 15.9377 6.70993C15.9376 6.71017 15.9374 6.71052 15.9373 6.7108C15.9372 6.71104 15.9371 6.71128 15.937 6.71153C15.8992 6.80114 15.8444 6.88521 15.7724 6.95888L15.7724 6.95891C15.7718 6.95947 15.7713 6.95999 15.7708 6.96051C15.7707 6.96065 15.7705 6.96079 15.7703 6.96096C15.7699 6.96142 15.7695 6.9618 15.7691 6.96225C15.7688 6.9625 15.7686 6.96281 15.7683 6.96305C15.768 6.96333 15.7677 6.96368 15.7674 6.96396C15.7668 6.96455 15.7662 6.96514 15.7657 6.9657L10.1657 12.5657C10.0095 12.7219 9.80474 12.7999 9.6 12.7999Z" fill="#1A1A1A"></path></svg>            </span>
            `);

        /* Mega Menu Max Height */
        var m_h_mega = $('li.pxl-megamenu > .sub-menu > .pxl-mega-menu-elementor').outerHeight();
        var w_h_mega = $(window).height();
        var w_h_mega_css = w_h_mega - 120;
        if (m_h_mega > w_h_mega) {
            $('li.pxl-megamenu > .sub-menu > .pxl-mega-menu-elementor').css('max-height', w_h_mega_css + 'px');
            $('li.pxl-megamenu > .sub-menu > .pxl-mega-menu-elementor').css('overflow-y', 'scroll');
            $('li.pxl-megamenu > .sub-menu > .pxl-mega-menu-elementor').css('overflow-x', 'hidden');
        }
        // Active Mega Menu Hover
        $('li.pxl-megamenu').hover(function () {
            $(this).parents('.elementor-element').addClass('section-mega-active');
        }, function () {
            $(this).parents('.elementor-element').removeClass('section-mega-active');
        });
        /* End Mega Menu Max Height */
        /* Search Popup */
        var $search_wrap_init = $("#pxl-search-popup");
        var search_field = $('#pxl-search-popup .search-field');
        var $body = $('body');

        $(".pxl-search-popup-button").on('click', function (e) {
            if (!$search_wrap_init.hasClass('active')) {
                $search_wrap_init.addClass('active');
                setTimeout(function () { search_field.get(0).focus(); }, 500);
            } else if (search_field.val() === '') {
                $search_wrap_init.removeClass('active');
                search_field.get(0).focus();
            }
            e.preventDefault();
            return false;
        });

        $(".pxl-subscribe-popup .pxl-item--overlay, .pxl-subscribe-popup .pxl-item--close").on('click', function (e) {
            $(this).parents('.pxl-subscribe-popup').removeClass('pxl-active');
            e.preventDefault();
            return false;
        });

        $("#pxl-search-popup .pxl-item--overlay, #pxl-search-popup .pxl-item--close").on('click', function (e) {
            $body.addClass('pxl-search-out-anim');
            setTimeout(function () {
                $body.removeClass('pxl-search-out-anim');
            }, 800);
            setTimeout(function () {
                $search_wrap_init.removeClass('active');
            }, 800);
            e.preventDefault();
            return false;
        });

        /* Scroll To Top */
        $('.pxl-scroll-top').click(function () {
            $('html, body').animate({ scrollTop: 0 }, 1200);
            $(this).parents('.pxl-wapper').find('.elementor > .pin-spacer').addClass('scroll-top-active');
            return false;
        });

        /* custom grid filter moving border */
        $('.pxl-grid-filter').each(function () {
            var marker = $(this).find('.filter-marker'),
                item = $(this).find('.filter-item'),
                current = $(this).find('.filter-item.active');

            var offsettop = current.length ? current.position().top : 0;

            marker.css({
                top: offsettop + (current.length ? current.outerHeight() : 0),
                left: current.length ? current.position().left : 0,
                width: current.length ? current.outerWidth() : 0,
                display: "block"
            });

            item.mouseover(function () {
                var self = $(this),
                    offsetactop = self.position().top,
                    offsetleft = self.position().left,
                    width = self.outerWidth() || current.outerWidth(),
                    top = offsetactop == 0 ? 0 : offsetactop || offsettop,
                    left = offsetleft == 0 ? 0 : offsetleft || current.position().left;

                marker.stop().animate({
                    top: top + (current.length ? current.outerHeight() : 0),
                    left: left,
                    width: width,
                }, 300);
            });

            item.on('click', function () {
                current = $(this);
            });

            item.mouseleave(function () {
                var offsetlvtop = current.length ? current.position().top : 0;
                marker.stop().animate({
                    top: offsetlvtop + (current.length ? current.outerHeight() : 0),
                    left: current.length ? current.position().left : 0,
                    width: current.length ? current.outerWidth() : 0
                }, 300);
            });
        });


        /* Animate Time Delay */

        /* Related Post - Swiper Slider */
        $('.pxl-related-post, .pxl-related-event').each(function () {
            var $this = $(this);
            var $container = $this.find('.pxl-swiper-container');
            if ($container.length > 0 && typeof Swiper !== 'undefined') {
                var settings = $container.data('settings');
                new Swiper($container[0], {
                    slidesPerView: settings['slides_to_show'] || 3,
                    slidesPerGroup: settings['slides_to_scroll'] || 1,
                    spaceBetween: settings['slides_gutter'] || 20,
                    loop: true,
                    navigation: {
                        nextEl: $this.find('.pxl-swiper-arrow-next')[0],
                        prevEl: $this.find('.pxl-swiper-arrow-prev')[0],
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: settings['slides_to_show_xs'] || 1,
                        },
                        768: {
                            slidesPerView: settings['slides_to_show_sm'] || 2,
                        },
                        992: {
                            slidesPerView: settings['slides_to_show_md'] || 2,
                        },
                        1200: {
                            slidesPerView: settings['slides_to_show_lg'] || 3,
                        }
                    }
                });
            }
        });

        $('.pxl-grid-masonry').each(function () {
            var eltime = 80;
            var elt_inner = $(this).children().length;
            var _elt = elt_inner - 1;
            $(this).find('> .pxl-grid-item > .wow').each(function (index, obj) {
                $(this).css('animation-delay', eltime + 'ms');
                if (_elt === index) {
                    eltime = 80;
                    _elt = _elt + elt_inner;
                } else {
                    eltime = eltime + 80;
                }
            });
        });

        $('.btn-text-nina').each(function () {
            var eltime = 0.045;
            var elt_inner = $(this).children().length;
            var _elt = elt_inner - 1;
            $(this).find('> .pxl--btn-text > span').each(function (index, obj) {
                $(this).css('transition-delay', eltime + 's');
                eltime = eltime + 0.045;
            });
        });

        $('.btn-text-nanuk').each(function () {
            var eltime = 0.05;
            var elt_inner = $(this).children().length;
            var _elt = elt_inner - 1;
            $(this).find('> .pxl--btn-text > span').each(function (index, obj) {
                $(this).css('animation-delay', eltime + 's');
                eltime = eltime + 0.05;
            });
        });

        $('.btn-text-smoke').each(function () {
            var eltime = 0.05;
            var elt_inner = $(this).children().length;
            var _elt = elt_inner - 1;
            $(this).find('> .pxl--btn-text > span > span > span').each(function (index, obj) {
                $(this).css('--d', eltime + 's');
                eltime = eltime + 0.05;
            });
        });

        $('.btn-text-reverse .pxl-text--front, .btn-text-reverse .pxl-text--back').each(function () {
            var eltime = 0.05;
            var elt_inner = $(this).children().length;
            var _elt = elt_inner - 1;
            $(this).find('.pxl-text--inner > span').each(function (index, obj) {
                $(this).css('transition-delay', eltime + 's');
                eltime = eltime + 0.05;
            });
        });

        /* End Animate Time Delay */

        $('.label-text-fillter').on('click', function () {
            $(this).parents('.pxl-grid-filter').addClass('active');
        });
        $('.filter-item').on('click', function () {
            $('.pxl-grid-filter').removeClass('active');
        });


        /* Lightbox Popup */
        $('.pxl-action-popup').magnificPopup({
            type: 'iframe',
            mainClass: 'mfp-fade',
            removalDelay: 160,
            preloader: false,
            fixedContentPos: false
        });

        $('.pxl-gallery-lightbox').each(function () {
            $(this).magnificPopup({
                delegate: 'a.lightbox',
                type: 'image',
                gallery: {
                    enabled: true
                },
                mainClass: 'mfp-fade',
            });
        });

        /* Page Title Parallax */
        if (pxl_window_width > 1024) {
            if ($('#pxl-page-title-default').hasClass('pxl--parallax')) {
                $(this).stellar();
            }
        }

        /* Cart Sidebar Popup */
        $(".pxl-cart-sidebar-button").on('click', function () {
            $('body').addClass('body-overflow');
            $('#pxl-cart-sidebar').addClass('active');
        });
        $("#pxl-cart-sidebar .pxl-popup--overlay, #pxl-cart-sidebar .pxl-item--close").on('click', function () {
            $('body').removeClass('body-overflow');
            $('#pxl-cart-sidebar').removeClass('active');
        });
        /* Hover Active Item */
        $('.pxl--widget-hover').each(function () {
            $(this).hover(function () {
                $(this).parents('.elementor-row').find('.pxl--widget-hover').removeClass('pxl--item-active');
                $(this).parents('.elementor-container').find('.pxl--widget-hover').removeClass('pxl--item-active');
                $(this).addClass('pxl--item-active');
            });
        });
        /* Hover Active button */

        var wobbleElements = document.querySelectorAll('.pxl-wobble');
        wobbleElements.forEach(function (el) {
            el.addEventListener('mouseover', function () {
                if (!el.classList.contains('animating') && !el.classList.contains('mouseover')) {
                    el.classList.add('animating', 'mouseover');
                    var letters = el.innerText.split('');
                    setTimeout(function () { el.classList.remove('animating'); }, (letters.length + 1) * 50);
                    var animationName = el.dataset.animation;
                    if (!animationName) { animationName = "pxl-jump"; }
                    el.innerText = '';
                    letters.forEach(function (letter) {
                        if (letter == " ") {
                            letter = "&nbsp;";
                        }
                        el.innerHTML += '<span class="letter">' + letter + '</span>';
                    });
                    var letterElements = el.querySelectorAll('.letter');
                    letterElements.forEach(function (letter, i) {
                        setTimeout(function () {
                            letter.classList.add(animationName);
                        }, 50 * i);
                    });
                }
            });
            el.addEventListener('mouseout', function () {
                el.classList.remove('mouseover');
            });
        });

        /* End Icon Bounce */
        var boxEls = $('.el-bounce, .pxl-image-effect1, .el-effect-zigzag');
        if (boxEls.length > 0) {
            if ('IntersectionObserver' in window) {
                var bounceObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('pxl-in-view');
                        } else {
                            entry.target.classList.remove('pxl-in-view');
                        }
                    });
                }, { threshold: 0.1 });

                boxEls.each(function () {
                    bounceObserver.observe(this);
                });
            } else {
                boxEls.addClass('pxl-in-view');
            }
        }

        /* Select Theme Style */
        $('.widget.widget_search input').attr('required', true);
        $('.wpcf7-select').each(function () {
            var $this = $(this), numberOfOptions = $(this).children('option').length;

            $this.addClass('pxl-select-hidden');
            $this.wrap('<div class="pxl-select"></div>');
            $this.after('<div class="pxl-select-higthlight"></div>');

            var $styledSelect = $this.next('div.pxl-select-higthlight');
            $styledSelect.text($this.children('option').eq(0).text());

            var $list = $('<ul />', {
                'class': 'pxl-select-options'
            }).insertAfter($styledSelect);

            for (var i = 0; i < numberOfOptions; i++) {
                $('<li />', {
                    text: $this.children('option').eq(i).text(),
                    rel: $this.children('option').eq(i).val()
                }).appendTo($list);
            }

            var $listItems = $list.children('li');

            $styledSelect.click(function (e) {
                e.stopPropagation();
                $('div.pxl-select-higthlight.active').not(this).each(function () {
                    $(this).removeClass('active').next('ul.pxl-select-options').addClass('pxl-select-lists-hide');
                });
                $(this).toggleClass('active');
            });

            $listItems.click(function (e) {
                e.stopPropagation();
                $styledSelect.text($(this).text()).removeClass('active');
                $this.val($(this).attr('rel'));
            });

            $(document).click(function () {
                $styledSelect.removeClass('active');
            });

        });

        /* Nice Select */
        $('.woocommerce-ordering .orderby, #filter-label, .pxl-filter-dropdown, #pxl-sidebar-area select, .variations_form.cart .variations select, .pxl-open-table select, .pxl-nice-select').each(function () {
            $(this).niceSelect();
        });

        $('.pxl-post-list .nice-select').each(function () {
            $(this).niceSelect();
        });

        /* Typewriter */
        if ($('.pxl-title--typewriter').length) {
            function typewriterOut(elements, callback) {
                if (elements.length) {
                    elements.eq(0).addClass('is-active');
                    elements.eq(0).delay(3000);
                    elements.eq(0).removeClass('is-active');
                    typewriterOut(elements.slice(1), callback);
                }
                else {
                    callback();
                }
            }

            function typewriterIn(elements, callback) {
                if (elements.length) {
                    elements.eq(0).addClass('is-active');
                    elements.eq(0).delay(3000).slideDown(3000, function () {
                        elements.eq(0).removeClass('is-active');
                        typewriterIn(elements.slice(1), callback);
                    });
                }
                else {
                    callback();
                }
            }

            function typewriterInfinite() {
                typewriterOut($('.pxl-title--typewriter .pxl-item--text'), function () {
                    typewriterIn($('.pxl-title--typewriter .pxl-item--text'), function () {
                        typewriterInfinite();
                    });
                });
            }
            $(function () {
                typewriterInfinite();
            });
        }
        /* End Typewriter */

        /* Get checked input - Mailchimpp */
        $('.mc4wp-form input:checkbox').change(function () {
            if ($(this).is(":checked")) {
                $('.mc4wp-form').addClass("pxl-input-checked");
            } else {
                $('.mc4wp-form').removeClass("pxl-input-checked");
            }
        });

    });

    jQuery(document).ajaxComplete(function (event, xhr, settings) {
        frameflow_shop_quantity();
        frameflow_height_ct_grid();
        HeightTitles();
    });

    jQuery(document).on('updated_wc_div', function () {
        frameflow_shop_quantity();
    });

    /* Header Sticky */
    function frameflow_header_sticky() {
        if ($('#pxl-header-elementor').hasClass('is-sticky')) {
            if (pxl_scroll_top > 100) {
                $('.pxl-header-elementor-sticky.pxl-sticky-stb').addClass('pxl-header-fixed');
                $('#pxl-header-mobile').addClass('pxl-header-mobile-fixed');
            } else {
                $('.pxl-header-elementor-sticky.pxl-sticky-stb').removeClass('pxl-header-fixed');
                $('#pxl-header-mobile').removeClass('pxl-header-mobile-fixed');
            }

            if (pxl_scroll_status == 'up' && pxl_scroll_top > 100) {
                $('.pxl-header-elementor-sticky.pxl-sticky-stt').addClass('pxl-header-fixed');
            } else {
                $('.pxl-header-elementor-sticky.pxl-sticky-stt').removeClass('pxl-header-fixed');
            }
        }

        $('.pxl-header-elementor-sticky').parents('body').addClass('pxl-header-sticky');
    }

    /* Header Mobile */
    function frameflow_header_mobile() {
        var h_header_mobile = $('#pxl-header-elementor').outerHeight();
        if (pxl_window_width < 1199) {
            $('#pxl-header-elementor').css('min-height', h_header_mobile + 'px');
        }
    }

    /* Scroll To Top */
    function frameflow_scroll_to_top() {
        if (pxl_scroll_top < pxl_window_height) {
            $('.pxl-scroll-top').addClass('pxl-off').removeClass('pxl-on');
        }
        if (pxl_scroll_top > pxl_window_height) {
            $('.pxl-scroll-top').addClass('pxl-on').removeClass('pxl-off');
        }
    }

    /* Footer Fixed */
    function frameflow_footer_fixed() {
        setTimeout(function () {
            var h_footer = $('.pxl-footer-fixed #pxl-footer-elementor').outerHeight() - 1;
            $('.pxl-footer-fixed #pxl-main').css('margin-bottom', h_footer + 'px');
        }, 600);
    }

    /* Custom Check Scroll */
    function frameflow_check_scroll() {
        var $gridItems = $('.pxl-check-scroll .pxl-swiper-slide');
        var viewportBottom = pxl_scroll_top + $(window).height();

        $gridItems.each(function () {
            var $gridItem = $(this);
            var elementTop = $gridItem.offset().top;
            var elementBottom = elementTop + $gridItem.outerHeight();

            if (elementTop < viewportBottom && elementBottom > pxl_scroll_top) {
                $gridItem.addClass('visible');
            } else {
                $gridItem.removeClass('visible');
            }
        });
    }

    function dropdown_offices() {
        const filterDropdown = $("#filter-label");
        const items = document.querySelectorAll(".pxl-offices-list .pxl--item");

        if (!filterDropdown.length || items.length === 0) return;

        filterDropdown.on("change", function () {
            const selectedLabel = this.value.toLowerCase();

            items.forEach(item => {
                const itemLabel = item.dataset.label?.toLowerCase() || "";
                item.classList.toggle("hidden", selectedLabel !== "" && itemLabel !== selectedLabel);
            });
        });
    }


    /* Button Parallax */
    /* Button Parallax */
    function frameflow_button_parallax() {
        const $buttons = $('.btn.btn-circle, .pxl-anchor-button.style-2');
        if ($buttons.length === 0) return;

        $buttons.each(function () {
            const $btn = $(this);
            const $text = $btn.find('svg');
            let rect = null;
            let isAnimating = false;

            $btn.on('mouseenter', function () {
                rect = this.getBoundingClientRect();
                if ($text.length > 0) {
                    gsap.set($text, { transformOrigin: "50% 50%" });
                }
            });

            $btn.on('mousemove', function (e) {
                if (isAnimating || !rect) return;
                isAnimating = true;

                requestAnimationFrame(() => {
                    const centerX = rect.left + rect.width / 2;
                    const centerY = rect.top + rect.height / 2;
                    const deltaX = (e.clientX - centerX) * 0.5;
                    const deltaY = (e.clientY - centerY) * 0.5;

                    const targets = [$btn[0]];
                    if ($text.length > 0) targets.push($text[0]);

                    gsap.to(targets, {
                        duration: 0.2,
                        x: deltaX,
                        y: deltaY,
                        ease: "power2.out",
                        overwrite: "auto"
                    });
                    isAnimating = false;
                });
            });

            $btn.on('mouseleave', function () {
                rect = null;
                const targets = [$btn[0]];
                if ($text.length > 0) targets.push($text[0]);

                gsap.to(targets, {
                    duration: 0.4,
                    x: 0,
                    y: 0,
                    ease: "elastic.out(1, 0.3)",
                    overwrite: "auto"
                });
            });
        });
    }

    /* WooComerce Quantity */
    $(document).on('click.pxl_qty', '.quantity-up', function () {
        $(this).parents('.quantity').find('input[type="number"]').get(0).stepUp();
        $(this).parents('.woocommerce-cart-form').find('.actions .button').removeAttr('disabled');
        $(this).parents('.quantity').find('input[type="number"]').trigger('change');
    });

    $(document).on('click.pxl_qty', '.quantity-down', function () {
        $(this).parents('.quantity').find('input[type="number"]').get(0).stepDown();
        $(this).parents('.woocommerce-cart-form').find('.actions .button').removeAttr('disabled');
        $(this).parents('.quantity').find('input[type="number"]').trigger('change');
    });

    $(document).on('click.pxl_qty', '.quantity-icon', function () {
        var quantity_number = $(this).parents('.quantity').find('input[type="number"]').val();
        var add_to_cart_button = $(this).parents(".product, .woocommerce-product-inner").find(".add_to_cart_button");
        if (add_to_cart_button.length > 0) {
            add_to_cart_button.attr('data-quantity', quantity_number);
            add_to_cart_button.attr("href", "?add-to-cart=" + add_to_cart_button.attr("data-product_id") + "&quantity=" + quantity_number);
        }
    });

    function frameflow_shop_quantity() {
        "use strict";
        $('#pxl-wapper .quantity').each(function () {
            var $qty = $(this);
            if ($qty.find('.quantity-icon').length === 0) {
                $qty.append('<span class="quantity-icon quantity-down pxl-icon--minus"></span><span class="quantity-icon quantity-up pxl-icon--plus"></span>');
            }
        });
        $('.woocommerce-cart-form .actions .button').removeAttr('disabled');
    }

    /* Menu Responsive Dropdown */
    function frameflow_submenu_responsive() {
        var $frameflow_menu = $('.pxl-header-elementor-main, .pxl-header-elementor-sticky');
        $frameflow_menu.find('.pxl-menu-primary li').each(function () {
            var $frameflow_submenu = $(this).find('> ul.sub-menu');
            if ($frameflow_submenu.length == 1) {
                if (($frameflow_submenu.offset().left + $frameflow_submenu.width() + 0) > $(window).width()) {
                    $frameflow_submenu.addClass('pxl-sub-reverse');
                }
            }
        });
    }

    function frameflow_panel_anchor_toggle() {
        'use strict';
        $(document).on('click', '.pxl-anchor-button', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var target = $(this).attr('data-target');
            $(target).toggleClass('active');
            $('body').addClass('body-overflow');
            $('.pxl-popup--conent .wow').addClass('animated').removeClass('aniOut');
            $('.pxl-popup--conent .fadeInPopup').removeClass('aniOut');
            if ($(target).find('.pxl-search-form').length > 0) {
                setTimeout(function () {
                    $(target).find('.pxl-search-form .pxl-search-field').focus();
                }, 1000);
            }
        });

        $(document).ready(function () {
            $('.pxl-post-taxonomy .pxl-count').each(function () {
                var content = $(this).html();
                if (content) {
                    var newContent = content.replace('(', '');
                    var newContent2 = newContent.replace(')', '');
                    $(this).html(newContent2);
                }
            });
        });


        $('.pxl-anchor-button').each(function () {
            var t_target = $(this).attr('data-target');
            var t_delay = $(this).attr('data-delay-hover');
            $(t_target).find('.pxl-popup--conent').css('transition-delay', t_delay + 'ms');
            $(t_target).find('.pxl-popup--overlay').css('transition-delay', t_delay + 'ms');
        });

        $(".pxl-hidden-panel-popup .pxl-popup--overlay, .pxl-hidden-panel-popup .pxl-close-popup").on('click', function () {
            $('body').removeClass('body-overflow');
            $('.pxl-hidden-panel-popup').removeClass('active');
            $('.pxl-popup--conent .wow').addClass('aniOut').removeClass('animated');
            $('.pxl-popup--conent .fadeInPopup').addClass('aniOut');
        });

        $(".pxl-popup--close").on('click', function () {
            $('body').removeClass('body-overflow');
            $(this).parent().removeClass('active');
        });
        $(".pxl-close-popup").on('click', function () {
            $('body').removeClass('body-overflow');
            $('.pxl-page-popup').removeClass('active');
        });
    }

    /* Page Title Scroll Opacity */
    function frameflow_ptitle_scroll_opacity() {
        var divs = $('#pxl-page-title-elementor.pxl-scroll-opacity .elementor-widget'),
            limit = $('#pxl-page-title-elementor.pxl-scroll-opacity').outerHeight();
        if (pxl_scroll_top <= limit) {
            divs.css({ 'opacity': (1 - pxl_scroll_top / limit) });
        }
    }

    /* Preloader Default */
    $.fn.extend({
        jQueryImagesLoaded: function () {
            var $imgs = this.find('img[src!=""]')

            if (!$imgs.length) {
                return $.Deferred()
                    .resolve()
                    .promise()
            }

            var dfds = []

            $imgs.each(function () {
                var dfd = $.Deferred()
                dfds.push(dfd)
                var img = new Image()
                img.onload = function () {
                    dfd.resolve()
                }
                img.onerror = function () {
                    dfd.resolve()
                }
                img.src = this.src
            })

            return $.when.apply($, dfds)
        }
    })

    /* Button Parallax */
    /* Button Parallax */
    function frameflow_button_parallax1() {
        $('.btn-text-parallax, .pxl-blog-style2, .pxl-hover-parallax').on('mouseenter', function () {
            $(this).addClass('hovered');
            // Cache rect
            this._pxlRect = this.getBoundingClientRect();
        });

        $('.btn-text-parallax, .pxl-blog-style2, .pxl-hover-parallax').on('mouseleave', function () {
            $(this).removeClass('hovered');
        });

        $('.btn-text-parallax').on('mousemove', function (e) {
            const $this = $(this);
            if ($this.data('is-animating')) return;

            $this.data('is-animating', true);
            requestAnimationFrame(() => {
                const bounds = this._pxlRect || this.getBoundingClientRect();
                const centerX = bounds.left + bounds.width / 2;
                const centerY = bounds.top + bounds.height;
                const deltaX = Math.floor((centerX - e.clientX)) * 0.222;
                const deltaY = Math.floor((centerY - e.clientY)) * 0.333;
                $this.find('.pxl--btn-text').css({
                    transform: 'translate3d(' + deltaX * 0.32 + 'px, ' + deltaY * 0.32 + 'px, 0px)'
                });
                $this.data('is-animating', false);
            });
        });

        $('.pxl-blog-style2 .pxl-post--featured, .pxl-hover-parallax').on('mousemove', function (e) {
            const $this = $(this);
            if ($this.data('is-animating')) return;

            $this.data('is-animating', true);
            requestAnimationFrame(() => {
                const bounds = this._pxlRect || this.getBoundingClientRect();
                const centerX = bounds.left + bounds.width / 2;
                const centerY = bounds.top + bounds.height;
                const deltaX = Math.floor((centerX - e.clientX)) * 0.222;
                const deltaY = Math.floor((centerY - e.clientY)) * 0.333;
                $this.find('.pxl-item-parallax, .pxl-post--button').css({
                    transform: 'translate3d(' + deltaX * 0.32 + 'px, ' + deltaY * 0.32 + 'px, 0px)'
                });
                $this.data('is-animating', false);
            });
        });
    }


    function frameflow_el_parallax() {
        $('.el-parallax-wrap').on({
            mouseenter: function () {
                const $this = $(this);
                $this.addClass('hovered');
                $this.find('.el-parallax-item').css({
                    transition: 'none'
                });
            },
            mouseleave: function () {
                const $this = $(this);
                $this.removeClass('hovered');
                $this.find('.el-parallax-item').css({
                    transition: 'transform 0.5s ease',
                    transform: 'translate3d(0px, 0px, 0px)'
                });
            },
            mousemove: function (e) {
                const $this = $(this);
                const bounds = this.getBoundingClientRect();
                const centerX = bounds.left + bounds.width / 2;
                const centerY = bounds.top + bounds.height / 2;
                const deltaX = (centerX - e.clientX) * 0.07104;
                const deltaY = (centerY - e.clientY) * 0.10656;

                requestAnimationFrame(() => {
                    $this.find('.el-parallax-item').css({
                        transform: `translate3d(${deltaX}px, ${deltaY}px, 0px)`
                    });
                });
            }
        });
    }

    /* Menu Divider Move */
    function frameflow_menu_divider_move() {
        $('.pxl-nav-menu1.fr-style-divider').each(function () {
            var current = $(this).find('.pxl-menu-primary > .current-menu-item, .pxl-menu-primary > .current-menu-parent, .pxl-menu-primary > .current-menu-ancestor');
            if (current.length > 0) {
                var marker = $(this).find('.pxl-divider-move');
                marker.css({
                    left: current.position().left,
                    width: current.outerWidth(),
                    display: "block"
                });
                marker.addClass('active');
                current.addClass('pxl-shape-active');
                if (Modernizr.csstransitions) {
                    $(this).find('.pxl-menu-primary > li').mouseover(function () {
                        var self = $(this),
                            offsetLeft = self.position().left,
                            width = self.outerWidth() || current.outerWidth(),
                            left = offsetLeft == 0 ? 0 : offsetLeft || current.position().left;
                        marker.css({
                            left: left,
                            width: width,
                        });
                        marker.addClass('active');
                        current.removeClass('pxl-shape-active');
                    });
                    $(this).find('.pxl-menu-primary').mouseleave(function () {
                        marker.css({
                            left: current.position().left,
                            width: current.outerWidth()
                        });
                        current.addClass('pxl-shape-active');
                    });
                }
            } else {
                var marker = $(this).find('.pxl-divider-move');
                var current = $(this).find('.pxl-menu-primary > li:nth-child(1)');
                marker.css({
                    left: current.position().left,
                    width: current.outerWidth(),
                    display: "block"
                });
                if (Modernizr.csstransitions) {
                    $(this).find('.pxl-menu-primary > li').mouseover(function () {
                        var self = $(this),
                            offsetLeft = self.position().left,
                            width = self.outerWidth() || current.outerWidth(),
                            left = offsetLeft == 0 ? 0 : offsetLeft || current.position().left;
                        marker.css({
                            left: left,
                            width: width,
                        });
                        marker.addClass('active');
                    });
                    $(this).find('.pxl-menu-primary').mouseleave(function () {
                        marker.css({
                            left: current.position().left,
                            width: current.outerWidth()
                        });
                        marker.removeClass('active');
                    });
                }
            }
        });
    }

    /* Back To Top Progress Bar */
    var progressPath, pathLength, $scrollTopBtn;
    var pxl_doc_height;

    function frameflow_backtotop_progess_bar() {
        $scrollTopBtn = $('.pxl-scroll-top');
        if ($scrollTopBtn.length > 0) {
            progressPath = document.querySelector('.pxl-scroll-top path');
            pathLength = progressPath.getTotalLength();
            progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
            progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
            progressPath.style.strokeDashoffset = pathLength;
            progressPath.getBoundingClientRect();
            progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';

            // Cache doc height initially
            pxl_doc_height = $(document).height();

            // Initial call
            frameflow_backtotop_update();

            // Update doc height on resize
            $(window).on('resize', function () {
                pxl_doc_height = $(document).height();
            });
        }
    }

    function frameflow_backtotop_update() {
        if (!progressPath || !$scrollTopBtn) return;

        const scroll = pxl_scroll_top;
        const docHeight = document.documentElement.scrollHeight;
        const height = docHeight - pxl_window_height;

        if (height > 0) {
            const progress = pathLength - (scroll * pathLength / height);
            progressPath.style.strokeDashoffset = progress;
        }

        if (scroll > 50) {
            $scrollTopBtn.addClass('active-progress');
        } else {
            $scrollTopBtn.removeClass('active-progress');
        }
    }

    /* Custom Type File Upload*/
    function frameflow_type_file_upload() {

        var multipleSupport = typeof $('<input/>')[0].multiple !== 'undefined',
            isIE = /msie/i.test(navigator.userAgent);

        $.fn.pxl_custom_type_file = function () {

            return this.each(function () {

                var $file = $(this).addClass('pxl-file-upload-hidden'),
                    $wrap = $('<div class="pxl-file-upload-wrapper">'),
                    $button = $('<button type="button" class="pxl-file-upload-button">Choose File</button>'),
                    $input = $('<input type="text" class="pxl-file-upload-input" placeholder="No File Choose" />'),
                    $label = $('<label class="pxl-file-upload-button" for="' + $file[0].id + '">Choose File</label>');
                $file.css({
                    position: 'absolute',
                    opacity: '0',
                    visibility: 'hidden'
                });

                $wrap.insertAfter($file)
                    .append($file, $input, (isIE ? $label : $button));

                $file.attr('tabIndex', -1);
                $button.attr('tabIndex', -1);

                $button.click(function () {
                    $file.focus().click();
                });

                $file.change(function () {

                    var files = [], fileArr, filename;

                    if (multipleSupport) {
                        fileArr = $file[0].files;
                        for (var i = 0, len = fileArr.length; i < len; i++) {
                            files.push(fileArr[i].name);
                        }
                        filename = files.join(', ');
                    } else {
                        filename = $file.val().split('\\').pop();
                    }

                    $input.val(filename)
                        .attr('title', filename)
                        .focus();
                });

                $input.on({
                    blur: function () { $file.trigger('blur'); },
                    keydown: function (e) {
                        if (e.which === 13) {
                            if (!isIE) {
                                $file.trigger('click');
                            }
                        } else if (e.which === 8 || e.which === 46) {
                            $file.replaceWith($file = $file.clone(true));
                            $file.trigger('change');
                            $input.val('');
                        } else if (e.which === 9) {
                            return;
                        } else {
                            return false;
                        }
                    }
                });

            });

        };
        $('.wpcf7-file[type=file]').pxl_custom_type_file();
    }



    //Shop View Grid/List
    function frameflow_shop_view_layout() {

        $(document).on('click', '.pxl-view-layout .view-icon a', function (e) {
            e.preventDefault();
            if (!$(this).parent('li').hasClass('active')) {
                $('.pxl-view-layout .view-icon').removeClass('active');
                $(this).parent('li').addClass('active');
                $(this).parents('.pxl-content-area').find('ul.products').removeAttr('class').addClass($(this).attr('data-cls'));
            }
        });
    }

    function frameflow_height_ct_grid($scope) {
        $('.pxl-portfolio-grid-layout1 .pxl-grid-item,.pxl-portfolio-carousel2 .pxl-swiper-slide').each(function () {
            var elementHeight = $(this).find(".pxl-post-content-hide").height();
            $(this).find(".pxl-post-content-hide").css("margin-bottom", "-" + elementHeight + "px");
        });
    }
    // Zoom Point
    // Zoom Point
    var zoomPointImages = [];
    function frameflow_zoom_point() {
        zoomPointImages = [];
        const $zoomElements = $(".pxl-zoom-point");
        if ($zoomElements.length === 0) return;

        $zoomElements.each(function () {
            const container = this;
            const scaleOffset = $(container).data('offset') || 0;
            let scaleAmount = $(container).data('scale-mount') || 0;
            scaleAmount = scaleAmount / 100;

            const images = container.querySelectorAll("[data-scroll-zoom]");
            if (!images.length) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const idx = entry.target._pxlZoomIdx;
                    if (zoomPointImages[idx]) {
                        zoomPointImages[idx].isVisible = entry.isIntersecting;
                    }
                });
            }, { threshold: 0 });

            images.forEach(image => {
                const parent = image.parentNode;
                const rect = parent.getBoundingClientRect();
                const scrollY = window.pageYOffset || document.documentElement.scrollTop;

                const idx = zoomPointImages.length;
                image._pxlZoomIdx = idx;

                zoomPointImages.push({
                    element: image,
                    offset: scaleOffset,
                    scaleAmount: scaleAmount,
                    isVisible: false,
                    cachedTop: rect.top + scrollY,
                    cachedHeight: parent.offsetHeight
                });

                observer.observe(image);
                updateZoomImage(zoomPointImages[idx]);
            });
        });
    }

    function frameflow_zoom_point_update() {
        for (let i = 0; i < zoomPointImages.length; i++) {
            const item = zoomPointImages[i];
            if (item.isVisible) {
                updateZoomImage(item);
            }
        }
    }

    function updateZoomImage(item) {
        const viewportHeight = pxl_window_height;
        const scrollY = pxl_scroll_top;
        const elPosY = item.cachedTop + item.offset;
        const elHeight = item.cachedHeight;

        if (elPosY > scrollY + viewportHeight || elPosY + elHeight < scrollY) {
            return;
        }

        const distance = scrollY + viewportHeight - elPosY;
        const percentage = Math.min(Math.max(distance / (viewportHeight + elHeight), 0), 1);
        const scale = 1 + (item.scaleAmount * percentage * 100);

        item.element.style.transform = `scale(${scale})`;
    }

    $(document).ready(function () {
        /* Shop Filter Sidebar */
        $(".pxl-filter-toggle").on('click', function () {
            $('body').addClass('body-overflow');
            $('.pxl-filter-sidebar').addClass('active');
        });
        $(".pxl-filter-sidebar .pxl-sidebar-overlay, .pxl-filter-sidebar .pxl-close-sidebar").on('click', function () {
            $('body').removeClass('body-overflow');
            $('.pxl-filter-sidebar').removeClass('active');
        });
    });
})(jQuery);