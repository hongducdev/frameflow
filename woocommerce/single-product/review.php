<li <?php comment_class( 'wc-custom-review' ); ?> id="li-comment-<?php comment_ID(); ?>">

    <div class="wc-review-inner">

        <!-- TEXT -->
        <div class="wc-review-text">
            <?php comment_text(); ?>
        </div>
        <svg class="wc-review-bg" xmlns="http://www.w3.org/2000/svg" width="200" height="170" viewBox="0 0 200 170" fill="none">
            <g opacity="0.02">
                <path d="M0 85V170H85.7051V85H28.5685C28.5685 53.7547 54.2006 28.3335 85.7051 28.3335V0C38.4445 0 0 38.1282 0 85Z" fill="#1A1A1A" />
                <path d="M200.002 28.3335V0C152.741 0 114.297 38.1282 114.297 85V170H200.002V85H142.865C142.865 53.7547 168.497 28.3335 200.002 28.3335Z" fill="#1A1A1A" />
            </g>
        </svg>  
        <div class="wc-review-bottom">
            <!-- AVATAR + NAME -->
            <div class="wc-review-author-block">
                <div class="wc-review-avatar">
                    <?php echo get_avatar( $comment, 60 ); ?>
                </div>

                <div class="wc-review-author">
                    <?php echo get_comment_author(); ?>
                </div>
            </div>

            <!-- RATING -->
            <div class="wc-review-rating">
                <?php woocommerce_review_display_rating(); ?>
            </div>
        </div>
    </div>

</li>
