<?php
/**
 * WooCommerce Pricing Form Handler
 */

// 1. AJAX Handler for Adding to Cart with Custom Data
add_action('wp_ajax_pxl_pricing_add_to_cart', 'pxl_pricing_add_to_cart_handler');
add_action('wp_ajax_nopriv_pxl_pricing_add_to_cart', 'pxl_pricing_add_to_cart_handler');

function pxl_pricing_add_to_cart_handler() {
    error_log("Frameflow Buy Now: AJAX Handler started. POST: " . json_encode($_POST));
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
    $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';

    if (!$product_id) {
        wp_send_json_error(['message' => esc_html__('Invalid ProductID', 'frameflow')]);
    }

    // Reset Buy Now session flags to ensure the new product is correctly picked up
    if (WC()->session) {
        WC()->session->__unset('frameflow_buy_now_processed');
        WC()->session->__unset('frameflow_buy_now_product_id');
        WC()->session->__unset('frameflow_buy_now_variation_id');
        WC()->session->__unset('frameflow_buy_now');
        WC()->session->save_data();
    }

    $cart_item_data = array();
    if (!empty($first_name)) $cart_item_data['pxl_first_name'] = $first_name;
    if (!empty($last_name)) $cart_item_data['pxl_last_name'] = $last_name;
    if (!empty($email)) $cart_item_data['pxl_email'] = $email;
    if (!empty($phone)) $cart_item_data['pxl_phone'] = $phone;

    // Add to cart
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    
    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, 0, array(), $cart_item_data)) {
        wp_send_json_success(['redirect_url' => wc_get_checkout_url()]);
    } else {
        wp_send_json_error(['message' => esc_html__('Could not add to cart', 'frameflow')]);
    }
}

// 2. Display custom data in Cart and Checkout
add_filter('woocommerce_get_item_data', 'pxl_pricing_display_cart_data', 20, 2);
function pxl_pricing_display_cart_data($item_data, $cart_item) {
    $custom_fields = array(
        'pxl_first_name' => esc_html__('First Name', 'frameflow'),
        'pxl_last_name'  => esc_html__('Last Name', 'frameflow'),
        'pxl_email'      => esc_html__('Email', 'frameflow'),
        'pxl_phone'      => esc_html__('Phone', 'frameflow'),
    );

    foreach ($custom_fields as $key => $label) {
        if (isset($cart_item[$key])) {
            $item_data[] = array(
                'name'  => $label,
                'value' => $cart_item[$key],
            );
        }
    }
    return $item_data;
}

// 3. Save custom data to Order Items
add_action('woocommerce_checkout_create_order_line_item', 'pxl_pricing_save_order_item_data', 10, 4);
function pxl_pricing_save_order_item_data($item, $cart_item_key, $values, $order) {
    if (isset($values['pxl_first_name'])) {
        $item->add_meta_data(esc_html__('First Name', 'frameflow'), $values['pxl_first_name']);
    }
    if (isset($values['pxl_last_name'])) {
        $item->add_meta_data(esc_html__('Last Name', 'frameflow'), $values['pxl_last_name']);
    }
    if (isset($values['pxl_email'])) {
        $item->add_meta_data(esc_html__('Email', 'frameflow'), $values['pxl_email']);
    }
    if (isset($values['pxl_phone'])) {
        $item->add_meta_data(esc_html__('Phone', 'frameflow'), $values['pxl_phone']);
    }
}

// 4. Pre-fill Billing details from Cart Item Data to avoid double entry
add_filter('woocommerce_checkout_get_value', 'pxl_pricing_prefill_checkout_fields', 10, 2);
function pxl_pricing_prefill_checkout_fields($value, $input) {
    if (isset(WC()->cart) && !WC()->cart->is_empty()) {
        foreach (WC()->cart->get_cart() as $cart_item) {
            if ($input === 'billing_first_name' && isset($cart_item['pxl_first_name']) && empty($value)) {
                return $cart_item['pxl_first_name'];
            }
            if ($input === 'billing_last_name' && isset($cart_item['pxl_last_name']) && empty($value)) {
                return $cart_item['pxl_last_name'];
            }
            if ($input === 'billing_email' && isset($cart_item['pxl_email']) && empty($value)) {
                return $cart_item['pxl_email'];
            }
            if ($input === 'billing_phone' && isset($cart_item['pxl_phone']) && empty($value)) {
                return $cart_item['pxl_phone'];
            }
        }
    }
    return $value;
}

// 5. AJAX Handler for Adding Multiple Items to Cart (Layout 4)
add_action('wp_ajax_pxl_pricing_add_multiple_to_cart', 'pxl_pricing_add_multiple_to_cart_handler');
add_action('wp_ajax_nopriv_pxl_pricing_add_multiple_to_cart', 'pxl_pricing_add_multiple_to_cart_handler');

function pxl_pricing_add_multiple_to_cart_handler() {
    
    $items = isset($_POST['items']) ? $_POST['items'] : [];
    
    if (empty($items) || !is_array($items)) {
        wp_send_json_error(['message' => esc_html__('No items selected', 'frameflow')]);
    }

    // Reset Buy Now session flags to ensure the new product is correctly picked up
    if (WC()->session) {
        WC()->session->__unset('frameflow_buy_now_processed');
        WC()->session->__unset('frameflow_buy_now_product_id');
        WC()->session->__unset('frameflow_buy_now_variation_id');
        WC()->session->__unset('frameflow_buy_now');
        WC()->session->save_data();
    }

    $added_any = false;
    $buy_now_ids = []; // Initialize array

    // 1. Backup & Clear Logic (Ensure this runs)
    if (function_exists('frameflow_save_cart_backup')) {
        frameflow_save_cart_backup();
    }
    
    // Clear Cart
    $all_cart_items = WC()->cart->get_cart();
    foreach ($all_cart_items as $key => $item) {
        WC()->cart->remove_cart_item($key);
    }
    WC()->cart->empty_cart(false);

    foreach ($items as $item) {
        $product_id = isset($item['product_id']) ? absint($item['product_id']) : 0;
        $quantity = isset($item['quantity']) ? absint($item['quantity']) : 0;

        if ($product_id > 0 && $quantity > 0) {
            $buy_now_ids[] = $product_id; // Capture ID
            
            $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
            if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity)) {
                $added_any = true;
            }
        }
    }

    if ($added_any) {
        // Set flags so checkout knows this is a Buy Now flow
        WC()->session->set('frameflow_buy_now', true);
        WC()->session->set('frameflow_buy_now_processed', true);
        WC()->session->__unset('frameflow_buy_now_redirected');
        
        // Store IDs for verification
        if (count($buy_now_ids) === 1) {
            WC()->session->set('frameflow_buy_now_product_id', $buy_now_ids[0]);
        } else {
            // Store first one as fallback/primary
            WC()->session->set('frameflow_buy_now_product_id', $buy_now_ids[0]);
            // Store all IDs for multi-product verification
            WC()->session->set('frameflow_buy_now_product_ids', $buy_now_ids);
        }
        WC()->session->set('frameflow_buy_now_variation_id', 0);
        WC()->session->save_data();

        wp_send_json_success(['redirect_url' => wc_get_checkout_url()]);
    } else {
        wp_send_json_error(['message' => esc_html__('Could not add items to cart', 'frameflow')]);
    }
}
