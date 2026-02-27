<?php
/**
 * Custom widget layout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

if ( ! is_a( $product, 'WC_Product' ) ) {
    return;
}

$event_id = get_post_meta( $product->get_id(), '_me_event_id', true );
if ( ! empty( $event_id ) ) {
    return;
}

?>
<li class="pxl-widget-product">
    <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="pxl-wp-item">

        <span class="pxl-thumb">
            <?php echo wp_kses_post( $product->get_image() ); ?>
        </span>

        <span class="pxl-info">
            <span class="product-title">
                <?php echo wp_kses_post( $product->get_name() ); ?>
            </span>

            <span class="price">
                <?php echo wp_kses_post( $product->get_price_html() ); ?>
            </span>
        </span>

    </a>
</li>
