; (function ($) {

    "use strict";

    $(document).ready(function () {

        /* AJAX Filter Shop */
        function frameflow_ajax_filter(url) {
            $('.pxl-shop-topbar-wrap, .products, .woocommerce-pagination').addClass('pxl-loading');
            $.ajax({
                url: url,
                dataType: 'html',
                success: function (response) {
                    var $html = $(response);

                    // Replace Products
                    var $products = $html.find('.products');
                    if ($products.length) {
                        $('.products').replaceWith($products);
                    } else {
                        $('.products').html('<p class="woocommerce-info">No products found matching your selection.</p>');
                    }

                    // Replace Pagination
                    var $pagination = $html.find('.woocommerce-pagination');
                    if ($pagination.length) {
                        $('.woocommerce-pagination').replaceWith($pagination);
                    } else {
                        $('.woocommerce-pagination').empty();
                    }

                    // Replace Result Count
                    var $count = $html.find('.woocommerce-result-count');
                    $('.woocommerce-result-count').replaceWith($count);

                    // Replace Sidebar (to update counts/active states)
                    var $sidebar = $html.find('.pxl-filter-sidebar .pxl-sidebar-content');
                    if ($sidebar.length) {
                        $('.pxl-filter-sidebar .pxl-sidebar-content').html($sidebar.html());
                    }

                    // Update URL
                    history.pushState(null, null, url);

                    $('.pxl-shop-topbar-wrap, .products, .woocommerce-pagination').removeClass('pxl-loading');

                    // Re-init WooCommerce scripts
                    $(document.body).trigger('init_price_filter');
                    $(document.body).trigger('post-load'); // Triggers generic post-load events
                },
                error: function () {
                    $('.pxl-shop-topbar-wrap, .products, .woocommerce-pagination').removeClass('pxl-loading');
                    console.log('Error loading filter');
                }
            });
        }

        // Filter Links (Categories, Tags, Attributes, Layered Nav)
        $(document).on('click', '.pxl-filter-sidebar .widget_product_categories a, .pxl-filter-sidebar .widget_product_tag_cloud a, .pxl-filter-sidebar .widget_layered_nav a, .pxl-filter-sidebar .widget_layered_nav_filters a', function (e) {
            var url = $(this).attr('href');
            if (url && (url.indexOf('?') !== -1 || url.indexOf('/page/') !== -1 || url.indexOf('/product-category/') !== -1)) {
                e.preventDefault();
                frameflow_ajax_filter(url);
            }
        });

        // Search Form
        $(document).on('submit', '.pxl-filter-sidebar .widget_product_search form, .pxl-filter-sidebar .woocommerce-product-search, .widget_search form', function (e) {
            e.preventDefault();
            var $form = $(this);
            var url = $form.attr('action');
            var params = $form.serialize();

            // Handle search URL construction
            if (url.indexOf('?') === -1) {
                url += '?' + params;
            } else {
                url += '&' + params;
            }
            frameflow_ajax_filter(url);
        });

        // Price Filter Form
        $(document).on('submit', '.widget_price_filter form', function (e) {
            e.preventDefault();
            var $form = $(this);
            var url = $form.attr('action');
            var params = $form.serialize();
            if (url.indexOf('?') === -1) {
                url += '?' + params;
            } else {
                url += '&' + params;
            }
            frameflow_ajax_filter(url);
        });

        // Pagination
        $(document).on('click', '.woocommerce-pagination a', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            frameflow_ajax_filter(url);
            // Scroll to top of shop
            $('html, body').animate({
                scrollTop: $(".pxl-shop-topbar-wrap").offset().top - 100
            }, 500);
        });

        /* End AJAX Filter */


        $('.single_variation_wrap').addClass('clearfix');
        $('.woocommerce-variation-add-to-cart').addClass('clearfix');

        $('.cart-total-wrap').on('click', function () {
            $('.widget-cart-sidebar').toggleClass('open');
            $(this).toggleClass('cart-open');
            $('.site-overlay').toggleClass('open');
        });

        $('.site-overlay').on('click', function () {
            $(this).removeClass('open');
            $(this).parents('#page').find('.widget-cart-sidebar').removeClass('open');
        });

        $('.woocommerce-tab-heading').on('click', function () {
            $(this).toggleClass('open');
            $(this).parent().find('.woocommerce-tab-content').slideToggle('');
        });

        $('.site-menu-right .h-btn-cart, .mobile-menu-cart .h-btn-cart').on('click', function (e) {
            e.preventDefault();
            $(this).parents('#ct-header-wrap').find('.widget_shopping_cart').toggleClass('open');
            $('.ct-hidden-sidebar').removeClass('open');
            $('.ct-search-popup').removeClass('open');
        });

        $('.woocommerce-add-to-cart a.button').on('click', function () {
            $(this).parents('.woocommerce-product-inner').addClass('cart-added');
        });

        setTimeout(function () {
            $('.ct-grid .product_type_variable, .ct-slick-slider .product_type_variable').removeAttr('data-product_id');
        }, 200);

        $(".woocommerce .products").on("click", ".quantity input", function () {
            return false;
        });
        $(".woocommerce .products").on("change input", ".quantity .qty", function () {
            var add_to_cart_button = $(this).parents(".product").find(".add_to_cart_button");
            add_to_cart_button.attr('data-quantity', $(this).val());
            add_to_cart_button.attr("href", "?add-to-cart=" + add_to_cart_button.attr("data-product_id") + "&quantity=" + $(this).val());
        });
        $('.flex-viewport').parents('.woocommerce-gallery-inner').addClass('flex-slider-active');

        /* Add Placeholder Review Form */
        var $text_name = $('.single-product #review_form .comment-form-author label').text();
        $('.single-product #review_form .comment-form-author input').each(function (ev) {
            if (!$(this).val()) {
                $(this).attr("placeholder", $text_name);
            }
        });
        var $text_email = $('.single-product #review_form .comment-form-email label').text();
        $('.single-product #review_form .comment-form-email input').each(function (ev) {
            if (!$(this).val()) {
                $(this).attr("placeholder", $text_email);
            }
        });
        var $text_comment = $('.single-product #review_form .comment-form-comment label').text();
        $('.single-product #review_form .comment-form-comment textarea').each(function (ev) {
            if (!$(this).val()) {
                $(this).attr("placeholder", $text_comment);
            }
        });

        $('.pxl-item--attr .pxl-button--info').on('click', function () {
            $(this).toggleClass('active');
        });

    });

})(jQuery);


jQuery(document).on('qv_loader_stop', function () {
    jQuery(this).ready(function ($) {
        $('#yith-quick-view-modal .quantity').append('<span class="quantity-icon quantity-down"></span><span class="quantity-icon quantity-up"></span>');
        $('#yith-quick-view-modal .quantity-up').on('click', function () {
            $(this).parents('.quantity').find('input[type="number"]').get(0).stepUp();
        });
        $('#yith-quick-view-modal .quantity-down').on('click', function () {
            $(this).parents('.quantity').find('input[type="number"]').get(0).stepDown();
        });
    });
});
