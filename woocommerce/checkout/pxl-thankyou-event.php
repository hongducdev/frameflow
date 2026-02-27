<?php

/**
 * Thank you page for Event Orders
 * 
 * This template is used when an order contains event tickets
 * 
 * @var WC_Order $order
 */

if (!defined('ABSPATH')) {
    exit;
}

use MyEventsWooCommerce\Core\Utils;

if ($order) :
    do_action('woocommerce_before_thankyou', $order->get_id());

    // Check if order contains event tickets
    $has_event_tickets = false;
    $event_items = array();

    foreach ($order->get_items() as $item_id => $item) {
        $product_id = $item->get_product_id();
        $event_id = get_post_meta($product_id, '_me_event_id', true);

        if ($event_id) {
            $has_event_tickets = true;
            $event = get_post($event_id);
            if ($event) {
                $event_items[] = array(
                    'event_id' => $event_id,
                    'event' => $event,
                    'item' => $item,
                    'product_id' => $product_id,
                    'quantity' => $item->get_quantity(),
                    'ticket_type' => get_post_meta($product_id, '_me_ticket_type', true),
                );
            }
        }
    }

    if (!$has_event_tickets) {
        return; // Not an event order, use default template
    }

    // Get tickets for this order
    $tickets = array();
    $ticket_query = new WP_Query(array(
        'post_type' => 'event_ticket',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_me_order_id',
                'value' => $order->get_id(),
            ),
        ),
    ));

    if ($ticket_query->have_posts()) {
        while ($ticket_query->have_posts()) {
            $ticket_query->the_post();
            $ticket_id = get_the_ID();
            $ticket_event_id = Utils::get_ticket_meta($ticket_id, 'event_id', 0);
            $tickets[$ticket_event_id][] = $ticket_id;
        }
        wp_reset_postdata();
    }
?>

    <?php wc_get_template('checkout/order-received.php', array('order' => $order)); ?>

    <div class="thankyou-page thankyou-page--event">
        <div class="thankyou-page--content">
            <!-- Order Overview -->
            <div class="order-overview">
                <div class="order-detail d-flex align-items-center justify-content-center">
                    <p class="d-flex align-items-center"><strong><?php esc_html_e('Order number:', 'frameflow'); ?></strong> #<?php echo esc_html($order->get_order_number()); ?></p>
                    <p class="d-flex align-items-center"><strong><?php esc_html_e('Order date:', 'frameflow'); ?></strong> <?php echo wc_format_datetime($order->get_date_created()); ?></p>
                    <p class="d-flex align-items-center"><strong><?php esc_html_e('Order total:', 'frameflow'); ?></strong> <?php echo wp_kses_post($order->get_formatted_order_total()); ?></p>
                    <p class="d-flex align-items-center"><strong><?php esc_html_e('Payment method:', 'frameflow'); ?></strong> <?php echo esc_html($order->get_payment_method_title()); ?></p>
                </div>

                <!-- Order Status -->
                <div class="order-status d-flex align-items-baseline justify-content-center">
                    <span class="status confirmed d-flex align-items-center">
                        <span class="status-icon d-flex align-items-center justify-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="30" viewBox="0 0 28 30" fill="none">
                                <path d="M19.1998 11.2587C18.7448 10.8031 18.0052 10.8031 17.5502 11.2587L12.5417 16.2666L10.4498 14.1753C9.99483 13.7198 9.25517 13.7198 8.80017 14.1753C8.34458 14.6309 8.34458 15.3694 8.80017 15.825L11.7168 18.7417C11.9443 18.9698 12.243 19.0835 12.5417 19.0835C12.8403 19.0835 13.139 18.9698 13.3665 18.7417L19.1998 12.9083C19.6554 12.4528 19.6554 11.7143 19.1998 11.2587Z" fill="black" />
                                <path d="M26.8333 13.8266C26.1893 13.8266 25.6667 14.3524 25.6667 15.0003C25.6667 21.4715 20.433 26.7363 14 26.7363C7.567 26.7363 2.33333 21.4715 2.33333 15.0003C2.33333 8.52899 7.567 3.2642 14 3.2642C17.1319 3.2642 20.0719 4.49531 22.2792 6.73103C22.7325 7.19167 23.4716 7.19402 23.9289 6.7369C24.3862 6.28037 24.3886 5.53747 23.9347 5.07742C21.2864 2.39456 17.7578 0.916992 14 0.916992C6.28017 0.916992 0 7.23451 0 15.0003C0 22.766 6.28017 29.0835 14 29.0835C21.7198 29.0835 28 22.766 28 15.0003C28 14.3524 27.4773 13.8266 26.8333 13.8266Z" fill="black" />
                            </svg>
                        </span>
                        <?php esc_html_e('Confirmed', 'frameflow'); ?>
                        <?php
                        $confirmed_date = $order->get_date_created();
                        if ($confirmed_date) {
                            echo '<small>' . esc_html($confirmed_date->date('j M Y')) . '</small>';
                        }
                        ?>
                    </span>
                </div>
            </div>

            <!-- Event Information -->
            <?php foreach ($event_items as $event_item) :
                $event_id = $event_item['event_id'];
                $event = $event_item['event'];
                $start_date = Utils::get_event_meta($event_id, 'start', '');
                $end_date = Utils::get_event_meta($event_id, 'end', '');
                $location = Utils::get_event_meta($event_id, 'location', '');
                $venue = Utils::get_event_meta($event_id, 'venue', '');
            ?>
                <div class="event-info">
                    <div class="event-header">
                        <?php if (has_post_thumbnail($event_id)) : ?>
                            <div class="event-image">
                                <?php echo get_the_post_thumbnail($event_id, 'large'); ?>
                            </div>
                        <?php endif; ?>
                        <h2><?php echo esc_html($event->post_title); ?></h2>
                    </div>

                    <div class="event-details">
                        <?php if ($start_date) : ?>
                            <div class="event-detail-item">
                                <strong><?php esc_html_e('Event Date:', 'frameflow'); ?></strong>
                                <span><?php echo esc_html(Utils::format_date($start_date)); ?></span>
                                <?php if ($end_date && $end_date !== $start_date) : ?>
                                    <span> - <?php echo esc_html(Utils::format_date($end_date)); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($location) : ?>
                            <div class="event-detail-item">
                                <strong><?php esc_html_e('Location:', 'frameflow'); ?></strong>
                                <span><?php echo esc_html($location); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($venue) : ?>
                            <div class="event-detail-item">
                                <strong><?php esc_html_e('Venue:', 'frameflow'); ?></strong>
                                <span><?php echo esc_html($venue); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="event-detail-item">
                            <strong><?php esc_html_e('Tickets Purchased:', 'frameflow'); ?></strong>
                            <span><?php echo esc_html($event_item['quantity']); ?>x <?php echo esc_html(ucfirst($event_item['ticket_type'])); ?></span>
                        </div>
                    </div>

                    <!-- Tickets List -->
                    <?php if (isset($tickets[$event_id]) && !empty($tickets[$event_id])) : ?>
                        <div class="tickets-section">
                            <h3><?php esc_html_e('Your Tickets', 'frameflow'); ?></h3>
                            <div class="tickets-list">
                                <?php foreach ($tickets[$event_id] as $ticket_id) :
                                    $ticket_status = Utils::get_ticket_meta($ticket_id, 'status', 'valid');
                                    $ticket_code = get_post_meta($ticket_id, '_me_code', true);
                                ?>
                                    <div class="ticket-item">
                                        <div class="ticket-info">
                                            <span class="ticket-number"><?php esc_html_e('Ticket #', 'frameflow'); ?><?php echo esc_html($ticket_id); ?></span>
                                            <?php if ($ticket_code) : ?>
                                                <span class="ticket-code"><?php echo esc_html($ticket_code); ?></span>
                                            <?php endif; ?>
                                            <span class="ticket-status status-<?php echo esc_attr($ticket_status); ?>">
                                                <?php echo esc_html(ucfirst($ticket_status)); ?>
                                            </span>
                                        </div>
                                        <div class="ticket-actions">
                                            <?php 
                                                $qr_token = Utils::get_ticket_meta($ticket_id, 'qr_token', '');
                                                $view_url = add_query_arg('me_view_ticket', $qr_token, home_url('/'));
                                            ?>
                                            <a href="<?php echo esc_url($view_url); ?>" class="button view-ticket">
                                                <?php esc_html_e('View Ticket', 'frameflow'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="tickets-pending">
                            <p><?php esc_html_e('Your tickets will be issued shortly. You will receive an email confirmation once your tickets are ready.', 'frameflow'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <!-- Billing Address -->
            <div class="address-section">
                <div class="billing-address">
                    <h3><?php esc_html_e('Billing address', 'frameflow'); ?></h3>
                    <p><?php echo wp_kses_post($order->get_formatted_billing_address()); ?></p>
                </div>
            </div>
        </div>

        <div class="thankyou-page--sidebar">
            <!-- Order Items -->
            <div class="order-items">
                <h3><?php esc_html_e('Order Details', 'frameflow'); ?></h3>
                <ul>
                    <?php foreach ($order->get_items() as $item_id => $item) : ?>
                        <li>
                            <span class="order-items--img">
                                <?php
                                $product = $item->get_product();
                                if ($product) {
                                    $image_id = $product->get_image_id();
                                    if ($image_id) {
                                        echo wp_get_attachment_image($image_id, 'thumbnail');
                                    } else {
                                        // Placeholder when no image
                                        echo '<span class="product-image-placeholder"><i class="bi bi-ticket"></i></span>';
                                    }
                                } else {
                                    // Fallback placeholder
                                    echo '<span class="product-image-placeholder"><i class="bi bi-ticket"></i></span>';
                                }
                                ?>
                                <span class="product-quantity"><?php echo esc_html($item->get_quantity()); ?></span>
                            </span>
                            <span class="product-name"><?php echo esc_html($item->get_name()); ?></span>
                            <span class="product-total"><?php echo wp_kses_post(wc_price($item->get_total(), array('currency' => $order->get_currency()))); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="order-summary">
                    <p><strong><?php esc_html_e('Subtotal:', 'frameflow'); ?></strong>
                        <?php echo wp_kses_post(wc_price($order->get_subtotal(), array('currency' => $order->get_currency()))); ?>
                    </p>
                    <p><strong><?php esc_html_e('Discount:', 'frameflow'); ?></strong>
                        <?php echo wp_kses_post(wc_price($order->get_discount_total(), array('currency' => $order->get_currency()))); ?>
                    </p>
                    <p><strong><?php esc_html_e('Tax:', 'frameflow'); ?></strong>
                        <?php echo wp_kses_post(wc_price($order->get_total_tax(), array('currency' => $order->get_currency()))); ?>
                    </p>
                    <p><strong><?php esc_html_e('Total:', 'frameflow'); ?></strong>
                        <?php echo wp_kses_post($order->get_formatted_order_total()); ?>
                    </p>
                </div>
            </div>

            <!-- My Tickets Link -->
            <?php if (is_user_logged_in()) :
                $my_tickets_url = wc_get_page_permalink('myaccount') . 'my-tickets/';
            ?>
                <div class="my-tickets-link">
                    <a href="<?php echo esc_url($my_tickets_url); ?>" class="button">
                        <?php esc_html_e('View All My Tickets', 'frameflow'); ?>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Social Share -->
            <div class="social-links">
                <h3><?php esc_html_e('Share the event', 'frameflow'); ?></h3>
                <?php if (!empty($event_items)) :
                    $first_event = $event_items[0];
                    $event_url = get_permalink($first_event['event_id']);
                ?>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url($event_url); ?>" target="_blank" id="facebook-share-event">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo esc_url($event_url); ?>&text=<?php echo esc_attr(urlencode($first_event['event']->post_title)); ?>" target="_blank" id="twitter-share-event">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo esc_url($event_url); ?>" target="_blank" id="linkedin-share-event">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>