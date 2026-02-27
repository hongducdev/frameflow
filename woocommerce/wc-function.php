<?php

//Custom products layout on archive page
add_filter('loop_shop_columns', 'frameflow_loop_shop_columns', 20);
function frameflow_loop_shop_columns()
{
	$columns = isset($_GET['product-column']) ? sanitize_text_field($_GET['product-column']) : frameflow()->get_theme_opt('products_columns', 3);
	return $columns;
}

// Change number of products that are displayed per page (shop page)
add_filter('loop_shop_per_page', 'frameflow_loop_shop_per_page', 20);
function frameflow_loop_shop_per_page($limit)
{
	$limit = isset($_GET['product-limit']) ? sanitize_text_field($_GET['product-limit']) : frameflow()->get_theme_opt('product_per_page', 9);
	return $limit;
}

if (!function_exists('frameflow_woocommerce_catalog_result')) {
	// remove

	// add back
	add_action('woocommerce_before_shop_loop', 'frameflow_woocommerce_catalog_result', 20);
	add_action('frameflow_woocommerce_catalog_ordering', 'woocommerce_catalog_ordering');
	add_action('frameflow_woocommerce_result_count', 'woocommerce_result_count');
	function frameflow_woocommerce_catalog_result()
	{
		$columns = isset($_GET['col']) ? sanitize_text_field($_GET['col']) : frameflow()->get_theme_opt('products_columns', '2');
		$display_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : frameflow()->get_theme_opt('shop_display_type', 'grid');
		$active_grid = 'active';
		$active_list = '';
		if ($display_type == 'list') {
			$active_list = $display_type == 'list' ? 'active' : '';
			$active_grid = '';
		}
?>
		<div class="pxl-shop-topbar-wrap ">
			<div class="text-heading number-result">
				<?php do_action('frameflow_woocommerce_result_count'); ?>
			</div>
			<div class="pxl-view-layout-wrap ">
				<div class="woocommerce-topbar-ordering">
					<?php woocommerce_catalog_ordering(); ?>
				</div>
                <div class="pxl-filter-toggle">
                    <span class="pxl-filter-text"><?php echo esc_html__('Filter', 'frameflow'); ?></span>
                    <i class="pxl-icon-filter"></i>
                </div>
			</div>
		</div>

        <!-- Filter Sidebar -->
        <div class="pxl-filter-sidebar">
            <div class="pxl-sidebar-overlay"></div>
            <div class="pxl-sidebar-inner">
                <div class="pxl-sidebar-header">
                    <h4 class="pxl-sidebar-title"><?php echo esc_html__('Filter', 'frameflow'); ?></h4>
                    <div class="pxl-close-sidebar">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
                <div class="pxl-sidebar-content">
                    <?php dynamic_sidebar('sidebar-shop'); ?>
                </div>
            </div>
        </div>
	<?php
	}
}

add_action('woocommerce_thankyou', 'add_custom_order_meta_to_thank_you', 20);
function add_custom_order_meta_to_thank_you($order_id)
{
	$order = wc_get_order($order_id);
	$custom_meta = $order->get_meta('_your_custom_meta_key');

	if ($custom_meta) {
		echo '<p>Custom Meta: ' . esc_html($custom_meta) . '</p>';
	}
}

add_action('woocommerce_thankyou', 'custom_thank_you_message', 20);
function custom_thank_you_message($order_id)
{
	$order = wc_get_order($order_id);
	echo '<p>Thank you for your order!</p>';
	echo '<p>Your order number is: ' . esc_html($order->get_order_number()) . '</p>';
}

function utero_wc_cart_totals_shipping_method_label($method)
{
	$label     = $method->get_label();
	$has_cost  = 0 < $method->cost;
	$hide_cost = ! $has_cost && in_array($method->get_method_id(), array('free_shipping', 'local_pickup'), true);

	if ($has_cost && ! $hide_cost) {
		if (WC()->cart->display_prices_including_tax()) {
			$label .= ' (' . wc_price($method->cost + $method->get_shipping_tax()) . ')';
			if ($method->get_shipping_tax() > 0 && ! wc_prices_include_tax()) {
				$label .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
			}
		} else {
			$label .= ' (' . wc_price($method->cost) . ')';
			if ($method->get_shipping_tax() > 0 && wc_prices_include_tax()) {
				$label .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
			}
		}
	}

	return apply_filters('woocommerce_cart_shipping_method_full_label', $label, $method);
}

add_filter('wc_get_template', 'frameflow_wc_update_get_template', 10, 5);
function frameflow_wc_update_get_template($template, $template_name, $args, $template_path, $default_path)
{
	switch ($template_name) {
		case 'cart/cart-totals.php':
			$template = get_template_directory() . '/' . WC()->template_path() . 'cart/pxl-cart-totals.php';
			break;
		case 'cart/cart.php':
			$template = get_template_directory() . '/' . WC()->template_path() . 'cart/pxl-cart-content.php';
			break;
		case 'cart/cart-shipping.php':
			$template = get_template_directory() . '/' . WC()->template_path() . 'cart/pxl-cart-shipping.php';
			break;
		case 'checkout/thankyou.php':
			$template = get_template_directory() . '/' . WC()->template_path() . 'checkout/pxl-thankyou.php';
			break;
		case 'checkout/form-checkout.php':
			$template = get_template_directory() . '/' . WC()->template_path() . 'checkout/form-checkout.php';
			break;
		case 'checkout/form-shipping.php':
			$template = get_template_directory() . '/' . WC()->template_path() . 'checkout/form-shipping.php';
			break;
	}

	return $template;
}

add_action('woocommerce_cart_totals_after_order_total', 'add_terms_conditions_to_cart_page');

function add_terms_conditions_to_cart_page()
{
	?>
	<div class="woocommerce-terms-and-conditions">
		<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
			<input type="checkbox" class="woocommerce-form__input-checkbox" name="terms_conditions" id="terms_conditions" />
			<span><?php _e('I agree with term and conditions', 'frameflow'); ?></span>
		</label>
		<p class="terms-error-message" style="color: red; display: none;"><?php _e('You must agree to the terms and conditions before proceeding.', 'frameflow'); ?></p>
	</div>
	<script>
		jQuery(function($) {
			$('form.woocommerce-cart-form').on('submit', function(e) {
				if (!$('#terms_conditions').is(':checked')) {
					e.preventDefault();
					$('.terms-error-message').show();
				} else {
					$('.terms-error-message').hide();
				}
			});
		});
	</script>
<?php
}

/* Cart action */
add_filter('woocommerce_add_to_cart_fragments', 'frameflow_woocommerce_add_to_cart_fragments', 10, 1);
function frameflow_woocommerce_add_to_cart_fragments($fragments)
{

	ob_start();
?>
	<span class="header-count cart_total"><?php echo WC()->cart->cart_contents_count; ?></span>
<?php
	$fragments['.cart_total'] = ob_get_clean();
	$fragments['.mini-cart-count'] = '<span class="mini-cart-total mini-cart-count">' . WC()->cart->cart_contents_count . '</span>';

	ob_start();
	wc_get_template('cart/mini-cart-totals.php');
	$mini_cart_totals = ob_get_clean();
	$fragments['.pxl-hidden-template-canvas-cart .cart-footer-inner'] = $mini_cart_totals;
	$fragments['.pxl-cart-dropdown .cart-footer-inner'] = $mini_cart_totals;

	$fragments['.pxl-anchor-cart .anchor-cart-count'] = '<span class="anchor-cart-count">' . WC()->cart->cart_contents_count . '</span>';
	$fragments['.pxl-anchor-cart .anchor-cart-total'] = '<span class="anchor-cart-total">' . WC()->cart->get_cart_subtotal() . '</span>';

	ob_start();
	wc_get_template('cart/pxl-cart-content.php');
	$fragments['.cart-list-wrapper .cart-list-content'] = ob_get_clean();

	return $fragments;
}


/* Remove result count & product ordering & item product category..... */
function frameflow_cwoocommerce_remove_function()
{
	remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10, 0);
	remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5, 0);
	remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10, 0);
	remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10, 0);
	remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10, 0);
	remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
	remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);

	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 60 );
	add_filter( 'woocommerce_product_tabs', '__return_empty_array', 98 );	
}
add_action('init', 'frameflow_cwoocommerce_remove_function');

/* Exclude Tickets from Shop Loop */
add_action( 'woocommerce_product_query', 'frameflow_exclude_tickets_from_shop' );
function frameflow_exclude_tickets_from_shop( $q ) {
    if ( is_admin() || $q->is_search() ) return;
    
    // Check if we are on shop, archive (category/tag). 
    if ( $q->is_main_query() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
        $meta_query = $q->get( 'meta_query' );
        if( ! is_array( $meta_query ) ) {
            $meta_query = array();
        }
        
        // Exclude products that have the _me_event_id meta key (Tickets)
        $meta_query[] = array(
            'key'     => '_me_event_id',
            'compare' => 'NOT EXISTS'
        );
        
        $q->set( 'meta_query', $meta_query );
    }
}

add_action('template_redirect', function() {

    if ( is_product() || is_shop() || is_product_taxonomy() ) {
        add_action('pxl_anchor_target', 'frameflow_hook_anchor_cart');
    }

});


// Remove review title "3 reviews for..."
add_filter( 'woocommerce_product_review_list_args', function( $args ) {
    $args['title_reply'] = '';
    return $args;
});

// Remove review title "3 reviews for..."
add_filter( 'woocommerce_reviews_title', '__return_empty_string' );

add_filter( 'woocommerce_product_review_comment_form_args', function( $args ) {
    $args['title_reply'] = '';
    $args['title_reply_to'] = '';
    return $args;
});

add_action( 'woocommerce_after_single_product_summary', 'bt_show_sections_after_tabs', 5 );
function bt_show_sections_after_tabs() {
    global $product;

    echo '<div class="bt-sections-wrap">';

    // 1. DESCRIPTION
    echo '<div class="bt-section description">';
    echo '<h2>DESCRIPTIONS</h2>';
    the_content(); // Woo mô tả
    echo '</div>';

    // 2. ADDITIONAL INFORMATION
    echo '<div class="bt-section additional-info">';
    wc_get_template( 'single-product/tabs/additional-information.php' );
    echo '</div>';

    // 3. REVIEWS
    echo '<div class="bt-section reviews">';
    echo '<h2>REVIEWS (' . $product->get_review_count() . ')</h2>';
    comments_template(); // list + form
    echo '</div>';

    echo '</div>';
}


/* Product Category */
//add_action( 'woocommerce_before_shop_loop', 'frameflow_woocommerce_nav_top', 2 );
function frameflow_woocommerce_nav_top()
{ ?>
	<div class="woocommerce-topbar">
		<div class="woocommerce-result-count pxl-pr-20">
			<?php woocommerce_result_count(); ?>
		</div>
		<div class="woocommerce-topbar-ordering">
			<?php woocommerce_catalog_ordering(); ?>
		</div>
	</div>
<?php }

add_filter('woocommerce_after_shop_loop_item', 'frameflow_woocommerce_product');
function frameflow_woocommerce_product()
{
	global $product;
	$product_id = $product->get_id();
	$shop_featured_img_size = frameflow()->get_theme_opt('shop_featured_img_size');
?>
	<div class="woocommerce-product-inner">
		<?php if (has_post_thumbnail()) {
			$img  = pxl_get_image_by_size(array(
				'attach_id'  => get_post_thumbnail_id($product_id),
				'thumb_size' => $shop_featured_img_size,
			));
			$thumbnail    = $img['thumbnail'];
			$thumbnail_url    = $img['url']; ?>
			<div class="woocommerce-product-header">
				<a class="woocommerce-product-details" href="<?php the_permalink(); ?>">
					<?php if (!empty($shop_featured_img_size)) {
						echo wp_kses_post($thumbnail);
					} else {
						woocommerce_template_loop_product_thumbnail();
					} ?>
				</a>
				<div class="woocommerce-product--buttons">
					<?php if (! $product->managing_stock() && ! $product->is_in_stock()) { ?>
					<?php } else { ?>
						<div class="woocommerce-add-to-cart">
							<div class="woocommerce-product-meta">
								<?php if (class_exists('WPCleverWoosw')) { ?>
									<div class="woocommerce-wishlist">
										<?php echo do_shortcode('[woosw id="' . esc_attr($product->get_id()) . '"]'); ?>
									</div>
								<?php } ?>
								<?php if (class_exists('WPCleverWoosc')) { ?>
									<div class="woocommerce-btn-item woocommerce-compare">
										<?php echo do_shortcode('[woosc id="'.esc_attr( $product->get_id() ).'"]'); ?>
									</div>
								<?php } ?>	
								<?php if (! $product->managing_stock() && ! $product->is_in_stock()) { ?>
								<?php } else { ?>
									<div class="woocommerce-add-to-cart">
										<?php woocommerce_template_loop_add_to_cart(); ?>
									</div>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="woocommerce-product-content">
				<div class="woocommerce-product--rating">
					<?php woocommerce_template_loop_rating(); ?>
				</div>
				<h4 class="woocommerce-product--title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h4>
				<?php woocommerce_template_loop_price(); ?>
				<div class="woocommerce-product--excerpt" style="display: none;">
					<?php woocommerce_template_single_excerpt(); ?>
				</div>
				<div class="woocommerce-add-to--cart list-v" style="display: none;">
					<?php woocommerce_template_loop_add_to_cart(); ?>
				</div>
				<?php if (class_exists('WPCleverWoosw')) { ?>
					<div class="woocommerce-wishlist" style="display: none;">
						<?php echo do_shortcode('[woosw id="' . esc_attr($product->get_id()) . '"]'); ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
<?php }

/* Replace text Onsale */
add_filter('woocommerce_sale_flash', 'frameflow_custom_sale_text', 10, 3);
function frameflow_custom_sale_text($text, $post, $_product)
{
	return '<span class="onsale">' . esc_html__('Sale!', 'frameflow') . '</span>';
}
/* Removes the "shop" title on the main shop page */
function frameflow_hide_page_title()
{
	return false;
}
add_filter('woocommerce_show_page_title', 'frameflow_hide_page_title');

add_action('woocommerce_before_single_product_summary', 'frameflow_woocommerce_single_summer_start', 0);
function frameflow_woocommerce_single_summer_start()
{ ?>
	<?php echo '<div class="woocommerce-summary-wrap row">'; ?>
<?php }

add_action('woocommerce_before_add_to_cart_quantity', 'custom_before_quantity_input_field', 25);
function custom_before_quantity_input_field()
{ ?>
	<?php echo '<div class="quantity-label">' . esc_html__('Quantity', 'frameflow') . '</div>'; ?>
<?php }

add_action('woocommerce_single_product_summary', 'custom_after_quantity_input_field', 30);
function custom_after_quantity_input_field()
{
	global $product;
?>
	<div class="wooc-product-meta">
		<?php if (class_exists('WPCleverWoosw')) { ?>
			<?php echo do_shortcode('[woosw id="' . esc_attr($product->get_id()) . '"]'); ?>
		<?php } ?>
		<?php if (class_exists('WPCleverWoosc')) { ?>
			<div class="woocommerce-btn-item woocommerce-compare">
				<?php echo do_shortcode('[woosc id="' . esc_attr($product->get_id()) . '"]'); ?>
			</div>
		<?php } ?>
	</div>
<?php
}

add_filter( 'get_comment_author', function ( $author, $comment_ID, $comment ) {
    if ( $comment->user_id ) {
        $user = get_user_by( 'id', $comment->user_id );
        if ( $user ) {
            return $user->display_name;
        }
    }
    return $author;
}, 10, 3 );

// add_action('woocommerce_after_add_to_cart_button', 'frameflow_render_buy_now_button');
// function frameflow_render_buy_now_button()
// {
// 	// Support only purchasable products.
// 	global $product;

// 	if (! $product || ! $product->is_purchasable() || ! $product->is_in_stock()) {
// 		return;
// 	}

// 	$product_id = $product->get_id();

// 	echo '<input type="hidden" name="buy_it_now" value="0" class="buy-it-now-flag" />';
// 	echo '<button type="submit" name="add-to-cart" value="' . esc_attr($product_id) . '" class="single_buy_it_now_button alt" onclick="this.form.buy_it_now.value=1;"><span class="btn-icon-left"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none"><path d="M9.6 12.7999C9.39526 12.7999 9.19053 12.7219 9.03432 12.5657C8.7219 12.2532 8.7219 11.7467 9.03432 11.4343L13.2686 7.19999H0.800009C0.358159 7.19999 0 6.8418 0 6.39998C0 5.95813 0.358159 5.59997 0.800009 5.59997H13.2686L9.03432 1.36567C8.7219 1.05326 8.7219 0.546725 9.03432 0.234311C9.3467 -0.0781035 9.8533 -0.0781035 10.1657 0.234311L15.7657 5.8343L15.7674 5.83604C15.7677 5.83632 15.768 5.83667 15.7683 5.83695C15.7686 5.83723 15.7688 5.83751 15.7691 5.83778C15.7695 5.8382 15.7699 5.83862 15.7703 5.83904C15.7705 5.83918 15.7706 5.83932 15.7708 5.83949C15.7713 5.84005 15.7718 5.84057 15.7724 5.84109L15.7724 5.84116C15.8444 5.91483 15.8992 5.9989 15.937 6.08847C15.9371 6.08872 15.9372 6.089 15.9373 6.08924C15.9374 6.08952 15.9376 6.08983 15.9377 6.09011C15.9778 6.18543 15.9999 6.29015 15.9999 6.40002C15.9999 6.50989 15.9778 6.61461 15.9377 6.70993C15.9376 6.71017 15.9374 6.71052 15.9373 6.7108C15.9372 6.71104 15.9371 6.71128 15.937 6.71153C15.8992 6.80114 15.8444 6.88521 15.7724 6.95888L15.7724 6.95891C15.7718 6.95947 15.7713 6.95999 15.7708 6.96051C15.7707 6.96065 15.7705 6.96079 15.7703 6.96096C15.7699 6.96142 15.7695 6.9618 15.7691 6.96225C15.7688 6.9625 15.7686 6.96281 15.7683 6.96305C15.768 6.96333 15.7677 6.96368 15.7674 6.96396C15.7668 6.96455 15.7662 6.96514 15.7657 6.9657L10.1657 12.5657C10.0095 12.7219 9.80474 12.7999 9.6 12.7999Z" fill="#1A1A1A"></path></svg></span><span class="add-to-cart-content">' . esc_html__('Buy Now', 'frameflow') . '</span></button>';
// }

// Helper function to save cart backup
function frameflow_save_cart_backup($exclude_item_key = null)
{
	if (!WC()->session) {
		return false;
	}

	// Only save backup if not already saved
	if (WC()->session->get('frameflow_cart_backup')) {
		return true;
	}

	$current_cart = WC()->cart->get_cart();

	// Remove excluded item if provided
	if ($exclude_item_key && isset($current_cart[$exclude_item_key])) {
		unset($current_cart[$exclude_item_key]);
	}

	// Convert cart items to serializable format
	$cart_backup = array();
	if (!empty($current_cart)) {
		foreach ($current_cart as $item) {
			$item_data = $item;
			unset($item_data['data']); // Remove product object to avoid serialization errors
			$cart_backup[] = $item_data;
		}
	}

	$backup_count = count($cart_backup);
	
	WC()->session->set('frameflow_cart_backup', $cart_backup);
	WC()->session->save_data();

	return true;
}

// Helper function to check if item is Buy Now product
function frameflow_is_buy_now_item($item, $buy_now_product_id, $buy_now_variation_id)
{
	$item_product_id = $item['product_id'];
	$item_variation_id = isset($item['variation_id']) ? $item['variation_id'] : 0;

    $is_valid_product = false;
    if (is_array($buy_now_product_id)) {
        $is_valid_product = in_array($item_product_id, $buy_now_product_id);
    } else {
        $is_valid_product = ($item_product_id == $buy_now_product_id);
    }

	if ($buy_now_variation_id > 0) {
		return ($is_valid_product && $item_variation_id == $buy_now_variation_id);
	} else {
		return ($is_valid_product && $item_variation_id == 0);
	}
}

// Clear cart BEFORE adding Buy Now product - use filter to intercept early
add_filter('woocommerce_add_to_cart_validation', 'frameflow_buy_now_clear_cart_before_add', 1, 5);
function frameflow_buy_now_clear_cart_before_add($passed, $product_id, $quantity, $variation_id = 0, $variations = array())
{
	// Don't interfere if restoring cart
	if (WC()->session && WC()->session->get('frameflow_restoring_cart')) {
		return $passed;
	}

	// Check if buy_it_now flag is set and we haven't processed yet
	if (isset($_POST['buy_it_now']) && $_POST['buy_it_now'] == '1' && !WC()->session->get('frameflow_buy_now_processed')) {
		// Ensure session is initialized
		if (WC()->session && !WC()->session->has_session()) {
			WC()->session->set_customer_session_cookie(true);
		}

		if (WC()->session) {
			// Save cart backup
			frameflow_save_cart_backup();

			// Clear entire cart BEFORE adding Buy Now product
			$all_cart_items = WC()->cart->get_cart();
			foreach ($all_cart_items as $key => $item) {
				WC()->cart->remove_cart_item($key);
			}
			WC()->cart->empty_cart(false);

			// Store Buy Now product info
            // Only set if not already set (to avoid overwriting if using multi-add)
            if (!WC()->session->get('frameflow_buy_now_product_id')) {
			    WC()->session->set('frameflow_buy_now_product_id', $product_id);
            }
			WC()->session->set('frameflow_buy_now_variation_id', $variation_id);
			WC()->session->set('frameflow_buy_now', true);
			WC()->session->save_data();
		}
	}

	return $passed;
}

// Handle Buy Now button - ensure only Buy Now product remains after add
add_action('woocommerce_add_to_cart', 'frameflow_buy_now_clear_other_items', 99, 6);
function frameflow_buy_now_clear_other_items($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
{
	// Don't interfere if restoring cart
	if (WC()->session && WC()->session->get('frameflow_restoring_cart')) {
		return;
	}

	// Check if buy_it_now flag is set and we haven't processed yet
	if (isset($_POST['buy_it_now']) && $_POST['buy_it_now'] == '1' && !WC()->session->get('frameflow_buy_now_processed')) {
		// Ensure session is initialized
		if (WC()->session && !WC()->session->has_session()) {
			WC()->session->set_customer_session_cookie(true);
		}

		$buy_now_product_id = WC()->session->get('frameflow_buy_now_product_id') ?: $product_id;
        $buy_now_product_ids = WC()->session->get('frameflow_buy_now_product_ids');
        if (!empty($buy_now_product_ids) && is_array($buy_now_product_ids)) {
            $buy_now_product_id = $buy_now_product_ids;
        }

		$buy_now_variation_id = WC()->session->get('frameflow_buy_now_variation_id') ?: $variation_id;

		// Store if not already stored
		if (!WC()->session->get('frameflow_buy_now_product_id')) {
			WC()->session->set('frameflow_buy_now_product_id', $buy_now_product_id); // This might store array if we passed array, but here $buy_now_product_id is ID or ID-array.
			WC()->session->set('frameflow_buy_now_variation_id', $buy_now_variation_id);
		}

		// Remove any items that are NOT the Buy Now product
		$all_cart_items = WC()->cart->get_cart();
		foreach ($all_cart_items as $key => $item) {
			if (!frameflow_is_buy_now_item($item, $buy_now_product_id, $buy_now_variation_id)) {
				WC()->cart->remove_cart_item($key);
			}
		}

		// Final verification: ensure cart only has Buy Now product(s)
		$final_items = WC()->cart->get_cart();
		$invalid_items_found = false;
		foreach ($final_items as $item) {
			if (!frameflow_is_buy_now_item($item, $buy_now_product_id, $buy_now_variation_id)) {
				$invalid_items_found = true;
				break;
			}
		}

		// If cart is wrong, clear and re-add Buy Now product
		if ($invalid_items_found || empty($final_items)) {
			WC()->cart->empty_cart(false);
			remove_action('woocommerce_add_to_cart', 'frameflow_buy_now_clear_other_items', 99);
            
            // If it's single product add, re-add it. If it was multi-add, it's already handled by its own AJAX handler loop, 
            // but this hook runs for EVERY product added, so we only re-add $product_id (single).
            if (!is_array($buy_now_product_id)) {
			    WC()->cart->add_to_cart($product_id, $quantity, $buy_now_variation_id, $variation, $cart_item_data);
            }
			add_action('woocommerce_add_to_cart', 'frameflow_buy_now_clear_other_items', 99, 6);
		}

		// Set flags
		WC()->session->set('frameflow_buy_now', true);
		WC()->session->set('frameflow_buy_now_processed', true);
		WC()->session->__unset('frameflow_buy_now_redirected');
		WC()->session->save_data();
	}
}

// Redirect to checkout immediately after adding to cart with Buy Now
// This handles the redirect right after add to cart action
add_filter('woocommerce_add_to_cart_redirect', 'frameflow_buy_now_redirect_filter', 10, 1);
function frameflow_buy_now_redirect_filter($url)
{
	// Check if buy_it_now flag is set
	if (isset($_POST['buy_it_now']) && $_POST['buy_it_now'] == '1') {
		// Mark as redirected so template_redirect won't redirect again
		if (WC()->session) {
			WC()->session->set('frameflow_buy_now_redirected', true);
		}
		return wc_get_checkout_url();
	}

	return $url;
}

// Ensure cart only has Buy Now product when on checkout page
add_action('template_redirect', 'frameflow_ensure_buy_now_cart_only', 20); // Run after redirect_to_checkout
function frameflow_ensure_buy_now_cart_only()
{
    // detailed logging is enabled in prev step, keeping function logic but adding page check
    if (!is_checkout() || is_order_received_page()) {
        return;
    }

	// Only check if Buy Now flag is set
	if (WC()->session && WC()->session->get('frameflow_buy_now')) {
		$buy_now_product_id = WC()->session->get('frameflow_buy_now_product_id');
        $buy_now_product_ids = WC()->session->get('frameflow_buy_now_product_ids');
        
        // Prefer array if set
        if (!empty($buy_now_product_ids) && is_array($buy_now_product_ids)) {
            $buy_now_product_id = $buy_now_product_ids;
        }

		$buy_now_variation_id = WC()->session->get('frameflow_buy_now_variation_id');
		$cart_items = WC()->cart->get_cart();

		// If no Buy Now product ID stored, something went wrong - clear flags
		if (empty($buy_now_product_id)) {
			WC()->session->__unset('frameflow_buy_now');
			WC()->session->__unset('frameflow_buy_now_processed');
			WC()->session->__unset('frameflow_buy_now_product_id');
            WC()->session->__unset('frameflow_buy_now_product_ids');
			WC()->session->__unset('frameflow_buy_now_variation_id');
			return;
		}

		// Check if cart has wrong items
		$has_wrong_items = false;
        $valid_items_count = 0;
		$buy_now_item = null; // Used for single item fallback logic

		foreach ($cart_items as $key => $item) {
			if (frameflow_is_buy_now_item($item, $buy_now_product_id, $buy_now_variation_id)) {
				$buy_now_item = $item;
                $valid_items_count++;
			} else {
				$has_wrong_items = true;
			}
		}

		// If cart has wrong items or doesn't have at least one Buy Now product, fix it
		if ($has_wrong_items || $valid_items_count === 0) {
			// Remove all items one by one
			$all_items = WC()->cart->get_cart();
			foreach ($all_items as $key => $item) {
				WC()->cart->remove_cart_item($key);
			}

			// Also call empty_cart
			WC()->cart->empty_cart(false);

			// Re-add valid items if we have them (this is complex for multi-item, so we clear session if somehow lost)
            if ($valid_items_count === 0) {
				// If we don't have the item info, clear flags
				WC()->session->__unset('frameflow_buy_now');
				WC()->session->__unset('frameflow_buy_now_processed');
				WC()->session->__unset('frameflow_buy_now_product_id');
                WC()->session->__unset('frameflow_buy_now_product_ids');
				WC()->session->__unset('frameflow_buy_now_variation_id');
			}
		}
	}
}

// Fallback redirect (only if add_to_cart_redirect didn't work, and only once)
add_action('template_redirect', 'frameflow_buy_now_redirect_to_checkout', 5);
function frameflow_buy_now_redirect_to_checkout()
{
	// Only redirect if flag is set and hasn't been redirected yet
	if (WC()->session && WC()->session->get('frameflow_buy_now') && !WC()->session->get('frameflow_buy_now_redirected')) {
		// Don't redirect if already on checkout, shop, cart, or product pages
		// Let users navigate freely on these pages
		if (is_checkout() || is_cart() || is_shop() || is_product_category() || is_product_tag() || is_product()) {
			// Mark as redirected so we don't redirect again
			WC()->session->set('frameflow_buy_now_redirected', true);
			return;
		}

		// Mark as redirected
		WC()->session->set('frameflow_buy_now_redirected', true);

		// Redirect to checkout (only from other pages)
		wp_safe_redirect(wc_get_checkout_url());
		exit;
	}
}

// Function to restore cart from backup
function frameflow_restore_cart_from_backup()
{
	if (!WC()->session) {
		return false;
	}

	$cart_backup = WC()->session->get('frameflow_cart_backup');
	$buy_now_flag = WC()->session->get('frameflow_buy_now');

	// Only restore if Buy Now flag exists
	if (!$buy_now_flag) {
		return false;
	}

	// Set flag to prevent Buy Now hooks from interfering
	WC()->session->set('frameflow_restoring_cart', true);

	// Clear current cart completely (including Buy Now item)
	$all_cart_items = WC()->cart->get_cart();
	foreach ($all_cart_items as $key => $item) {
		WC()->cart->remove_cart_item($key);
	}
	WC()->cart->empty_cart(false);

	// Restore backup cart (check if backup exists and has items)
	$restored = false;
	$attempted_count = 0;
	$success_count = 0;

	if ($cart_backup !== null && is_array($cart_backup)) {
		$attempted_count = count($cart_backup);
		foreach ($cart_backup as $cart_item) {
			// Validate cart item data
			if (!isset($cart_item['product_id']) || empty($cart_item['product_id'])) {
				continue;
			}

			$product_id = $cart_item['product_id'];
			$quantity = isset($cart_item['quantity']) ? $cart_item['quantity'] : 1;
			$variation_id = isset($cart_item['variation_id']) && $cart_item['variation_id'] > 0 ? $cart_item['variation_id'] : 0;
			$variation = isset($cart_item['variation']) && is_array($cart_item['variation']) ? $cart_item['variation'] : array();

			// Reconstruct other data (like ticket fields)
			$other_data = $cart_item;
			$exclude_keys = ['product_id', 'quantity', 'variation_id', 'variation', 'line_subtotal', 'line_total', 'line_tax', 'line_subtotal_tax', 'line_tax_data'];
			foreach ($exclude_keys as $key) {
				unset($other_data[$key]);
			}

			$result = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation, $other_data);

			if ($result) {
				$success_count++;
				$restored = true;
			}
		}
	}

	// Recalculate and save to ensure items stick
	WC()->cart->calculate_totals();
	
	if (function_exists('wc_set_cart_cookie')) {
		wc_set_cart_cookie(true);
	}
	
	if (isset(WC()->cart->session) && method_exists(WC()->cart->session, 'set_session')) {
		WC()->cart->session->set_session();
	}

	WC()->session->save_data();
	
	if ($success_count > 0 && method_exists(WC()->cart, 'persistent_cart_update')) {
		WC()->cart->persistent_cart_update();
	}

	$final_count = count(WC()->cart->get_cart());
	// ONLY clear backup if we successfully restored what we had (or if backup was empty)
	if ($attempted_count === 0 || $success_count > 0) {
		WC()->session->__unset('frameflow_cart_backup');
		WC()->session->__unset('frameflow_buy_now');
		WC()->session->__unset('frameflow_buy_now_processed');
		WC()->session->__unset('frameflow_buy_now_redirected');
		WC()->session->__unset('frameflow_buy_now_product_id');
		WC()->session->__unset('frameflow_buy_now_variation_id');
	}
	
	WC()->session->__unset('frameflow_restoring_cart');
	WC()->session->save_data();

	return $restored || ($cart_backup !== null);
}

// Ensure cart is restored when user navigates away from checkout/thankyou
// we removed the direct woocommerce_thankyou hook to avoid persistence issues

// Handle restore cart action via URL
add_action('init', 'frameflow_handle_restore_cart_action');
function frameflow_handle_restore_cart_action()
{
	// Check if restore action is triggered
	if (isset($_GET['restore_cart']) && $_GET['restore_cart'] == '1' && WC()->session) {
		// Verify nonce for security
		if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'restore_cart')) {
			if (frameflow_restore_cart_from_backup()) {
				// Redirect to shop page
				$shop_url = wc_get_page_permalink('shop') ? wc_get_page_permalink('shop') : home_url('/shop/');
				wp_safe_redirect($shop_url);
				exit;
			}
		}
	}
}

// Restore cart if user leaves checkout page without completing order
add_action('template_redirect', 'frameflow_restore_cart_on_shop_page');
function frameflow_restore_cart_on_shop_page()
{
	// Restore if we are NOT in the middle of a checkout process
	// IMPORTANT: Don't restore on Thank You page (order_received) because WC might still be clearing the cart
	if (is_checkout() || is_order_received_page()) {
		return;
	}

	// Don't restore if user just clicked restore button (restore_cart action)
	if (isset($_GET['restore_cart']) && $_GET['restore_cart'] == '1') {
		return;
	}

	// Check if there's a cart backup and Buy Now was used
	if (WC()->session && WC()->session->get('frameflow_cart_backup') !== null && WC()->session->get('frameflow_buy_now')) {
		$cart_contents = WC()->cart->get_cart();
		$buy_now_product_id = WC()->session->get('frameflow_buy_now_product_id');
		$buy_now_product_ids = WC()->session->get('frameflow_buy_now_product_ids');
		if (!empty($buy_now_product_ids) && is_array($buy_now_product_ids)) {
			$buy_now_product_id = $buy_now_product_ids;
		}
		$buy_now_variation_id = WC()->session->get('frameflow_buy_now_variation_id');

		// Check if current cart ONLY has Buy Now product(s)
		$only_buy_now = !empty($cart_contents);
		foreach ($cart_contents as $item) {
			if (!frameflow_is_buy_now_item($item, $buy_now_product_id, $buy_now_variation_id)) {
				$only_buy_now = false;
				break;
			}
		}

		// If cart is empty or only has Buy Now item(s), restore backup
		if (count($cart_contents) == 0 || $only_buy_now) {
			frameflow_restore_cart_from_backup();
			// Re-initialize cart fragments to update count UI
			if (WC()->cart) {
				WC()->cart->calculate_totals();
			}
		}
	}
}

// Add "Quay lại mua sắm" button on checkout page - multiple hooks to ensure it shows
add_action('woocommerce_before_checkout_form', 'frameflow_add_restore_cart_button', 5);
add_action('woocommerce_checkout_before_order_review_heading', 'frameflow_add_restore_cart_button', 5);
add_action('woocommerce_checkout_before_order_review', 'frameflow_add_restore_cart_button', 5);
add_action('woocommerce_checkout_order_review', 'frameflow_add_restore_cart_button', 1); // Before order review
add_action('wp_footer', 'frameflow_add_restore_cart_button_footer'); // Fallback in footer
function frameflow_add_restore_cart_button()
{
	// Only show on checkout page
	if (!is_checkout() || is_order_received_page()) {
		return;
	}

	// Only output once (use static variable to prevent duplicate output)
	static $already_output = false;
	if ($already_output) {
		return;
	}

	// Check if session exists
	if (!WC()->session) {
		return;
	}

	$cart_backup = WC()->session->get('frameflow_cart_backup');
	$buy_now_flag = WC()->session->get('frameflow_buy_now');

	// Show if Buy Now flag exists (backup can be empty array if cart was empty)
	// cart_backup can be empty array (not null) if original cart was empty
	if (!$buy_now_flag) {
		return;
	}

	// Additional check: current cart should ONLY have Buy Now product(s)
	$current_cart = WC()->cart->get_cart();
	$buy_now_product_id = WC()->session->get('frameflow_buy_now_product_id');
	$buy_now_product_ids = WC()->session->get('frameflow_buy_now_product_ids');
	if (!empty($buy_now_product_ids) && is_array($buy_now_product_ids)) {
		$buy_now_product_id = $buy_now_product_ids;
	}
	$buy_now_variation_id = WC()->session->get('frameflow_buy_now_variation_id');

	foreach ($current_cart as $item) {
		if (!frameflow_is_buy_now_item($item, $buy_now_product_id, $buy_now_variation_id)) {
			// If cart has any item that is not a Buy Now product, it's not a pure Buy Now checkout
			WC()->session->__unset('frameflow_cart_backup');
			WC()->session->__unset('frameflow_buy_now');
			WC()->session->__unset('frameflow_buy_now_product_id');
			WC()->session->__unset('frameflow_buy_now_product_ids');
			WC()->session->__unset('frameflow_buy_now_variation_id');
			return;
		}
	}

	$already_output = true;

	// Prepare URLs
	$shop_url = wc_get_page_permalink('shop') ? wc_get_page_permalink('shop') : home_url('/shop/');
	$restore_url = wp_nonce_url(add_query_arg('restore_cart', '1', $shop_url), 'restore_cart');

	// Check if there's actually a backup to restore
	$has_backup = !empty($cart_backup) && is_array($cart_backup) && count($cart_backup) > 0;

	// Prepare text strings
	$notice_title = esc_html__('You are checking out a "Buy Now" product', 'frameflow');
	if ($has_backup) {
		$notice_message = esc_html__('Your previous cart has been saved. Click the button below to return to shopping and your cart will be automatically restored.', 'frameflow');
	} else {
		$notice_message = esc_html__('You are checking out a "Buy Now" product. Click the button below to return to shopping.', 'frameflow');
	}
	$button_text = esc_html__('← Return to Shopping', 'frameflow');

	// Prepare styles
	$notice_style = 'margin-bottom: 20px; padding: 15px; background: #f5f5f5; border-left: 4px solid #5B57E7; border-radius: 4px; clear: both; z-index: 9999; position: relative;';
	$title_style = 'margin: 0 0 10px 0; font-weight: bold; color: #000;';
	$message_style = 'margin: 0 0 15px 0; color: #000;';
	$button_style = 'display: inline-block; cursor: pointer;';

	// Output notice
	echo '<div class="frameflow-restore-cart-notice" style="' . esc_attr($notice_style) . '">';
	echo '<p style="' . esc_attr($title_style) . '">' . esc_html($notice_title) . '</p>';
	echo '<p style="' . esc_attr($message_style) . '">' . esc_html($notice_message) . '</p>';
	echo '<a href="' . esc_url($restore_url) . '" class="button" style="' . esc_attr($button_style) . '">' . esc_html($button_text) . '</a>';
	echo '</div>';
}

// Fallback: Add button via JavaScript if not shown above
function frameflow_add_restore_cart_button_footer()
{
	if (!is_checkout() || is_order_received_page()) {
		return;
	}

	if (!WC()->session) {
		return;
	}

	$cart_backup = WC()->session->get('frameflow_cart_backup');
	$buy_now_flag = WC()->session->get('frameflow_buy_now');

	// Show if Buy Now flag exists (backup can be empty array if cart was empty)
	if (!$buy_now_flag) {
		return;
	}

	// Additional check: current cart should ONLY have Buy Now product(s)
	$current_cart = WC()->cart->get_cart();
	$buy_now_product_id = WC()->session->get('frameflow_buy_now_product_id');
	$buy_now_product_ids = WC()->session->get('frameflow_buy_now_product_ids');
	if (!empty($buy_now_product_ids) && is_array($buy_now_product_ids)) {
		$buy_now_product_id = $buy_now_product_ids;
	}
	$buy_now_variation_id = WC()->session->get('frameflow_buy_now_variation_id');

	foreach ($current_cart as $item) {
		if (!frameflow_is_buy_now_item($item, $buy_now_product_id, $buy_now_variation_id)) {
			// If cart has any item that is not a Buy Now product, it's not a pure Buy Now checkout
			WC()->session->__unset('frameflow_cart_backup');
			WC()->session->__unset('frameflow_buy_now');
			WC()->session->__unset('frameflow_buy_now_product_id');
			WC()->session->__unset('frameflow_buy_now_product_ids');
			WC()->session->__unset('frameflow_buy_now_variation_id');
			return;
		}
	}

	// Check if there's actually a backup to restore
	$has_backup = !empty($cart_backup) && is_array($cart_backup) && count($cart_backup) > 0;

	// Prepare URLs and text
	$shop_url = wc_get_page_permalink('shop') ? wc_get_page_permalink('shop') : home_url('/shop/');
	$restore_url = wp_nonce_url(add_query_arg('restore_cart', '1', $shop_url), 'restore_cart');

	// Prepare text strings for JavaScript
	// Prepare text strings
	$js_notice_title = __('You are checking out a "Buy Now" product', 'frameflow');
	if ($has_backup) {
		$js_notice_message = __('Your previous cart has been saved. Click the button below to return to shopping and your cart will be automatically restored.', 'frameflow');
	} else {
		$js_notice_message = __('You are checking out a "Buy Now" product. Click the button below to return to shopping.', 'frameflow');
	}
	$js_button_text = __('← Return to Shopping', 'frameflow');

	// Prepare styles
	$js_notice_style = 'margin-bottom: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #5B57E7; border-radius: 4px; clear: both; z-index: 9999; position: relative;';
	$js_title_style = 'margin: 0 0 10px 0; font-weight: bold; color: #000;';
	$js_message_style = 'margin: 0 0 15px 0; color: #856404;';
?>
	<script>
		jQuery(document).ready(function($) {
			// Check if notice already exists
			if ($('.frameflow-restore-cart-notice').length === 0) {
				var notice = '<div class="frameflow-restore-cart-notice" style="<?php echo esc_js($js_notice_style); ?>">' +
					'<p style="<?php echo esc_js($js_title_style); ?>"><?php echo esc_js(esc_html($js_notice_title)); ?></p>' +
					'<p style="<?php echo esc_js($js_message_style); ?>"><?php echo esc_js(esc_html($js_notice_message)); ?></p>' +
					'<a href="<?php echo esc_js(esc_url($restore_url)); ?>" class="button"><?php echo esc_js(esc_html($js_button_text)); ?></a>' +
					'</div>';

				// Try to insert before order review
				if ($('.woocommerce-checkout-review-order').length > 0) {
					$('.woocommerce-checkout-review-order').before(notice);
				} else if ($('.woocommerce-checkout-review-order-table').length > 0) {
					$('.woocommerce-checkout-review-order-table').before(notice);
				} else if ($('form.checkout').length > 0) {
					$('form.checkout').prepend(notice);
				} else if ($('.woocommerce-checkout').length > 0) {
					$('.woocommerce-checkout').prepend(notice);
				} else {
					$('body').prepend(notice);
				}
			}
		});
	</script>
<?php
}



add_action('woocommerce_after_single_product_summary', 'frameflow_woocommerce_single_summer_end', 5);
function frameflow_woocommerce_single_summer_end()
{ ?>
	<?php echo '</div></div>'; ?>
<?php }

/* Checkout Page*/
add_action('woocommerce_checkout_before_order_review_heading', 'frameflow_checkout_before_order_review_heading_start', 5);
function frameflow_checkout_before_order_review_heading_start()
{ ?>
	<?php echo '<div class="pxl-order-review-right"><div class="pxl-order-review-inner">'; ?>
<?php }

add_action('woocommerce_checkout_after_order_review', 'frameflow_checkout_after_order_review_end', 5);
function frameflow_checkout_after_order_review_end()
{ ?>
	<?php echo '</div></div>'; ?>
	<?php }


add_action('woocommerce_single_product_summary', 'frameflow_woocommerce_sg_product_title', 9);
function frameflow_woocommerce_sg_product_title()
{
	global $product;
	$product_title = frameflow()->get_theme_opt('product_title', false);
	if ($product_title) : ?>
		<div class="woocommerce-sg-product-title">
			<?php woocommerce_template_single_title(); ?>
		</div>
	<?php endif;
}

add_action('woocommerce_single_product_summary', 'frameflow_woocommerce_sg_product_price', 11);
function frameflow_woocommerce_sg_product_price()
{ ?>
	<div class="woocommerce-sg-product-price">
		<?php woocommerce_template_single_price(); ?>
	</div>
<?php }

add_action('woocommerce_single_product_summary', 'frameflow_woocommerce_sg_product_rating', 8);
function frameflow_woocommerce_sg_product_rating()
{
	global $product; ?>
	<div class="woocommerce-sg-product-rating">
		<?php woocommerce_template_single_rating();
		if ($rating_count = $product->get_rating_count()) {
			echo ' <span class="review-count">(' . $rating_count . ' ' . esc_html__('Customer review', 'frameflow') . ')</span>';
		} ?>
	</div>
<?php }

add_action('woocommerce_single_product_summary', 'frameflow_woocommerce_sg_product_excerpt', 20);
function frameflow_woocommerce_sg_product_excerpt()
{ ?>
	<div class="woocommerce-sg-product-excerpt">
		<?php woocommerce_template_single_excerpt(); ?>
	</div>
	<?php }

add_action('woocommerce_single_product_summary', 'frameflow_woocommerce_sg_social_share', 34);
function frameflow_woocommerce_sg_social_share()
{
	$product_social_share = frameflow()->get_theme_opt('product_social_share', false);
	if ($product_social_share) : ?>
		<div class="woocommerce-social-share">
			<label class="pxl-mr-20"><span class="pxl-icon--share"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
						<path d="M6.9776 6.05828L9.08594 3.94995V12.75C9.08594 13.3 9.4526 13.6666 10.0026 13.6666C10.5526 13.6666 10.9193 13.3 10.9193 12.75V3.94995L13.0276 6.05828C13.3943 6.42495 13.9443 6.42495 14.3109 6.05828C14.6776 5.69162 14.6776 5.14162 14.3109 4.77495L10.6443 1.10828C10.5526 1.01662 10.4609 0.924951 10.3693 0.924951C10.1859 0.833285 9.91094 0.833285 9.63594 0.924951C9.54427 0.924951 9.4526 1.01662 9.36094 1.10828L5.69427 4.77495C5.3276 5.14162 5.3276 5.69162 5.69427 6.05828C6.06094 6.42495 6.61094 6.42495 6.9776 6.05828ZM18.2526 11.8333C17.7026 11.8333 17.3359 12.2 17.3359 12.75V16.4166C17.3359 16.9666 16.9693 17.3333 16.4193 17.3333H3.58594C3.03594 17.3333 2.66927 16.9666 2.66927 16.4166V12.75C2.66927 12.2 2.3026 11.8333 1.7526 11.8333C1.2026 11.8333 0.835938 12.2 0.835938 12.75V16.4166C0.835938 17.975 2.0276 19.1666 3.58594 19.1666H16.4193C17.9776 19.1666 19.1693 17.975 19.1693 16.4166V12.75C19.1693 12.2 18.8026 11.8333 18.2526 11.8333Z" fill="white" />
					</svg></span><?php echo esc_html__('Share:', 'frameflow'); ?></label>
			<a class="fb-social pxl-mr-10" target="_blank" href="http://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>"><i class="fab fa-facebook-f"></i></a>
			<a class="tw-social pxl-mr-10" target="_blank" href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>%20"><i class="fab fa-x-twitter"></i></a>
			<a class="pin-social pxl-mr-10" target="_blank" href="http://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&description=<?php the_title(); ?>%20"><i class="fab fa-pinterest-p"></i></a>
			<a class="lin-social pxl-mr-10" target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url=<?php the_permalink(); ?>&title=<?php the_title(); ?>%20"><i class="fab fa-linkedin"></i></a>
		</div>
	<?php endif;
}

add_filter('woocommerce_product_thumbnails_columns', 'bbloomer_change_gallery_columns');

function bbloomer_change_gallery_columns()
{
	return 1;
}

/* Product Single: Gallery */
add_action('woocommerce_before_single_product_summary', 'nexros_woocommerce_single_gallery_start', 0);
function nexros_woocommerce_single_gallery_start()
{ ?>
	<?php echo '<div class="woocommerce-gallery col-xl-5 col-lg-6 col-md-6"><div class="woocommerce-gallery-inner">'; ?>
<?php }
add_action('woocommerce_before_single_product_summary', 'nexros_woocommerce_single_gallery_end', 30);
function nexros_woocommerce_single_gallery_end()
{ ?>
	<?php echo '</div></div><div class="woocommerce-summary-inner col-xl-7 col-lg-6 col-md-6">'; ?>
<?php }

/* Ajax update cart item */
add_filter('woocommerce_add_to_cart_fragments', 'frameflow_woo_mini_cart_item_fragment');
function frameflow_woo_mini_cart_item_fragment($fragments)
{
	global $woocommerce;
	ob_start();
?>
	<div class="widget_shopping_cart">
		<div class="widget_shopping_head">
			<div class="pxl-item--close pxl-close pxl-cursor--cta"></div>
			<div class="widget_shopping_title">
				<?php echo esc_html__('Cart', 'frameflow'); ?> <span class="widget_cart_counter">(<?php echo sprintf(_n('%d item', '%d items', WC()->cart->cart_contents_count, 'frameflow'), WC()->cart->cart_contents_count); ?>)</span>
			</div>
		</div>
		<div class="widget_shopping_cart_content">
			<?php
			$cart_is_empty = sizeof($woocommerce->cart->get_cart()) <= 0;
			?>
			<ul class="cart_list product_list_widget">

				<?php if (! WC()->cart->is_empty()) : ?>

					<?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
						$_product     = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
						$product_id   = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

						if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {

							$product_name  = apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key);
							$thumbnail     = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
							$product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
					?>
							<li>
								<?php if (!empty($thumbnail)) : ?>
									<div class="cart-product-image">
										<a href="<?php echo esc_url($_product->get_permalink($cart_item)); ?>">
											<?php echo str_replace(array('http:', 'https:'), '', $thumbnail); ?>
										</a>
									</div>
								<?php endif; ?>
								<div class="cart-product-meta">
									<h3><a href="<?php echo esc_url($_product->get_permalink($cart_item)); ?>"><?php echo esc_html($product_name); ?></a></h3>
									<?php echo apply_filters('woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf('%s &times; %s', $cart_item['quantity'], $product_price) . '</span>', $cart_item, $cart_item_key); ?>
									<?php
									echo apply_filters('woocommerce_cart_item_remove_link', sprintf(
										'<a href="%s" class="remove_from_cart_button pxl-close" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"></a>',
										esc_url(wc_get_cart_remove_url($cart_item_key)),
										esc_attr__('Remove this item', 'frameflow'),
										esc_attr($product_id),
										esc_attr($cart_item_key),
										esc_attr($_product->get_sku())
									), $cart_item_key);
									?>
								</div>
							</li>
					<?php
						}
					}
					?>

				<?php else : ?>

					<li class="empty">
						<i class="bootstrap-icons bi-cart3"></i>
						<span><?php esc_html_e('Your cart is empty', 'frameflow'); ?></span>
						<a class="btn btn-shop" href="<?php echo get_permalink(wc_get_page_id('shop')); ?>"><?php echo esc_html__('Browse Shop', 'frameflow'); ?></a>
					</li>

				<?php endif; ?>

			</ul><!-- end product list -->
		</div>
		<?php if (! WC()->cart->is_empty()) : ?>
			<div class="widget_shopping_cart_footer">
				<p class="total"><strong><?php esc_html_e('Subtotal', 'frameflow'); ?>:</strong> <?php echo WC()->cart->get_cart_subtotal(); ?></p>

				<?php do_action('woocommerce_widget_shopping_cart_before_buttons'); ?>

	               <p class="buttons">
                                <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="btn btn-shop wc-forward btn-2-icons"><?php esc_html_e('View Cart', 'frameflow'); ?><span class="btn-icon-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none"><path d="M9.6 12.7999C9.39526 12.7999 9.19053 12.7219 9.03432 12.5657C8.7219 12.2532 8.7219 11.7467 9.03432 11.4343L13.2686 7.19999H0.800009C0.358159 7.19999 0 6.8418 0 6.39998C0 5.95813 0.358159 5.59997 0.800009 5.59997H13.2686L9.03432 1.36567C8.7219 1.05326 8.7219 0.546725 9.03432 0.234311C9.3467 -0.0781035 9.8533 -0.0781035 10.1657 0.234311L15.7657 5.8343L15.7674 5.83604C15.7677 5.83632 15.768 5.83667 15.7683 5.83695C15.7686 5.83723 15.7688 5.83751 15.7691 5.83778C15.7695 5.8382 15.7699 5.83862 15.7703 5.83904C15.7705 5.83918 15.7706 5.83932 15.7708 5.83949C15.7713 5.84005 15.7718 5.84057 15.7724 5.84109L15.7724 5.84116C15.8444 5.91483 15.8992 5.9989 15.937 6.08847C15.9371 6.08872 15.9372 6.089 15.9373 6.08924C15.9374 6.08952 15.9376 6.08983 15.9377 6.09011C15.9778 6.18543 15.9999 6.29015 15.9999 6.40002C15.9999 6.50989 15.9778 6.61461 15.9377 6.70993C15.9376 6.71017 15.9374 6.71052 15.9373 6.7108C15.9372 6.71104 15.9371 6.71128 15.937 6.71153C15.8992 6.80114 15.8444 6.88521 15.7724 6.95888L15.7724 6.95891C15.7718 6.95947 15.7713 6.95999 15.7708 6.96051C15.7707 6.96065 15.7705 6.96079 15.7703 6.96096C15.7699 6.96142 15.7695 6.9618 15.7691 6.96225C15.7688 6.9625 15.7686 6.96281 15.7683 6.96305C15.768 6.96333 15.7677 6.96368 15.7674 6.96396C15.7668 6.96455 15.7662 6.96514 15.7657 6.9657L10.1657 12.5657C10.0095 12.7219 9.80474 12.7999 9.6 12.7999Z" fill="#1A1A1A"></path></svg>            </span></a>
                                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn checkout wc-forward btn-2-icons"><?php esc_html_e('Checkout', 'frameflow'); ?><span class="btn-icon-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none"><path d="M9.6 12.7999C9.39526 12.7999 9.19053 12.7219 9.03432 12.5657C8.7219 12.2532 8.7219 11.7467 9.03432 11.4343L13.2686 7.19999H0.800009C0.358159 7.19999 0 6.8418 0 6.39998C0 5.95813 0.358159 5.59997 0.800009 5.59997H13.2686L9.03432 1.36567C8.7219 1.05326 8.7219 0.546725 9.03432 0.234311C9.3467 -0.0781035 9.8533 -0.0781035 10.1657 0.234311L15.7657 5.8343L15.7674 5.83604C15.7677 5.83632 15.768 5.83667 15.7683 5.83695C15.7686 5.83723 15.7688 5.83751 15.7691 5.83778C15.7695 5.8382 15.7699 5.83862 15.7703 5.83904C15.7705 5.83918 15.7706 5.83932 15.7708 5.83949C15.7713 5.84005 15.7718 5.84057 15.7724 5.84109L15.7724 5.84116C15.8444 5.91483 15.8992 5.9989 15.937 6.08847C15.9371 6.08872 15.9372 6.089 15.9373 6.08924C15.9374 6.08952 15.9376 6.08983 15.9377 6.09011C15.9778 6.18543 15.9999 6.29015 15.9999 6.40002C15.9999 6.50989 15.9778 6.61461 15.9377 6.70993C15.9376 6.71017 15.9374 6.71052 15.9373 6.7108C15.9372 6.71104 15.9371 6.71128 15.937 6.71153C15.8992 6.80114 15.8444 6.88521 15.7724 6.95888L15.7724 6.95891C15.7718 6.95947 15.7713 6.95999 15.7708 6.96051C15.7707 6.96065 15.7705 6.96079 15.7703 6.96096C15.7699 6.96142 15.7695 6.9618 15.7691 6.96225C15.7688 6.9625 15.7686 6.96281 15.7683 6.96305C15.768 6.96333 15.7677 6.96368 15.7674 6.96396C15.7668 6.96455 15.7662 6.96514 15.7657 6.9657L10.1657 12.5657C10.0095 12.7219 9.80474 12.7999 9.6 12.7999Z" fill="#1A1A1A"></path></svg>            </span></a>
					</p>
			</div>
		<?php endif; ?>
	</div>
<?php
	$fragments['div.widget_shopping_cart'] = ob_get_clean();
	return $fragments;
}

add_filter('woocommerce_add_to_cart_fragments', 'frameflow_woocommerce_sidebar_cart_count_number');
function frameflow_woocommerce_sidebar_cart_count_number($fragments)
{
	ob_start();
?>
	<span class="widget_cart_counter">(<?php echo sprintf(_n('%d', '%d', WC()->cart->cart_contents_count, 'frameflow'), WC()->cart->cart_contents_count); ?>)</span>
<?php

	$fragments['span.widget_cart_counter'] = ob_get_clean();

	return $fragments;
}

/* Pagination Args */
function frameflow_filter_woocommerce_pagination_args($array)
{
	$array['end_size'] = 1;
	$array['mid_size'] = 1;
	return $array;
};
add_filter('woocommerce_pagination_args', 'frameflow_filter_woocommerce_pagination_args', 10, 1);

/* Flex Slider Arrow */
add_filter('woocommerce_single_product_carousel_options', 'frameflow_update_woo_flexslider_options');
function frameflow_update_woo_flexslider_options($options)
{
	$options['directionNav'] = true;
	return $options;
}

/* Single Thumbnail Size */
$single_img_size = frameflow()->get_theme_opt('single_img_size');
if (!empty($single_img_size['width']) && !empty($single_img_size['height'])) {
	add_filter('woocommerce_get_image_size_single', function ($size) {
		$single_img_size = frameflow()->get_theme_opt('single_img_size');
		$single_img_size_width = preg_replace('/[^0-9]/', '', $single_img_size['width']);
		$single_img_size_height = preg_replace('/[^0-9]/', '', $single_img_size['height']);
		$size['width'] = $single_img_size_width;
		$size['height'] = $single_img_size_height;
		$size['crop'] = 0;
		return $size;
	});
}
add_filter('woocommerce_get_image_size_gallery_thumbnail', function ($size) {
	$size['width'] = 600;
	$size['height'] = 600;
	$size['crop'] = 0;
	return $size;
});

add_filter('woocommerce_get_image_size_thumbnail', function ($size) {
	$size['width'] = 767;
	$size['height'] = 778;
	$size['crop'] = 0;
	return $size;
});

/* Custom Text Add to cart - Single product */
add_filter('woocommerce_product_single_add_to_cart_text', 'frameflow_add_to_cart_button_text_single');
function frameflow_add_to_cart_button_text_single()
{
	echo '<span class="btn-icon-left"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none"><path d="M9.6 12.7999C9.39526 12.7999 9.19053 12.7219 9.03432 12.5657C8.7219 12.2532 8.7219 11.7467 9.03432 11.4343L13.2686 7.19999H0.800009C0.358159 7.19999 0 6.8418 0 6.39998C0 5.95813 0.358159 5.59997 0.800009 5.59997H13.2686L9.03432 1.36567C8.7219 1.05326 8.7219 0.546725 9.03432 0.234311C9.3467 -0.0781035 9.8533 -0.0781035 10.1657 0.234311L15.7657 5.8343L15.7674 5.83604C15.7677 5.83632 15.768 5.83667 15.7683 5.83695C15.7686 5.83723 15.7688 5.83751 15.7691 5.83778C15.7695 5.8382 15.7699 5.83862 15.7703 5.83904C15.7705 5.83918 15.7706 5.83932 15.7708 5.83949C15.7713 5.84005 15.7718 5.84057 15.7724 5.84109L15.7724 5.84116C15.8444 5.91483 15.8992 5.9989 15.937 6.08847C15.9371 6.08872 15.9372 6.089 15.9373 6.08924C15.9374 6.08952 15.9376 6.08983 15.9377 6.09011C15.9778 6.18543 15.9999 6.29015 15.9999 6.40002C15.9999 6.50989 15.9778 6.61461 15.9377 6.70993C15.9376 6.71017 15.9374 6.71052 15.9373 6.7108C15.9372 6.71104 15.9371 6.71128 15.937 6.71153C15.8992 6.80114 15.8444 6.88521 15.7724 6.95888L15.7724 6.95891C15.7718 6.95947 15.7713 6.95999 15.7708 6.96051C15.7707 6.96065 15.7705 6.96079 15.7703 6.96096C15.7699 6.96142 15.7695 6.9618 15.7691 6.96225C15.7688 6.9625 15.7686 6.96281 15.7683 6.96305C15.768 6.96333 15.7677 6.96368 15.7674 6.96396C15.7668 6.96455 15.7662 6.96514 15.7657 6.9657L10.1657 12.5657C10.0095 12.7219 9.80474 12.7999 9.6 12.7999Z" fill="#1A1A1A"></path></svg></span><span class="add-to-cart-content">' . esc_html__('Add to Cart', 'frameflow') . '</span>';
}

/**
 * Set default image for ticket products in cart.
 */
add_filter('woocommerce_cart_item_thumbnail', 'frameflow_default_ticket_thumbnail', 10, 3);
function frameflow_default_ticket_thumbnail($thumbnail, $cart_item, $cart_item_key)
{
	$_product = $cart_item['data'];

	// Check if product has no image
	if (!$_product->get_image_id()) {
		// Check if it's a ticket product (based on meta set by frameflow plugin)
		$product_id = $_product->get_id();
		$event_id = get_post_meta($product_id, '_me_event_id', true);

		if ($event_id) {
			$default_image_id = get_option('me_default_ticket_image');
			$default_image_url = '';

			if ($default_image_id) {
				$default_image_url = wp_get_attachment_url($default_image_id);
			}

			if (!$default_image_url) {
				$default_image_url = get_template_directory_uri() . '/assets/images/default-ticket.png';
			}

			$thumbnail = '<img src="' . esc_url($default_image_url) . '" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="' . esc_attr($_product->get_name()) . '" />';
		}
	}

	return $thumbnail;
}
