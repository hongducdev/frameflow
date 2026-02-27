<?php
/**
 * @package Case-Themes
 */

if ( post_password_required() ) {
    return;
    } ?>

    <div id="comments" class="comments-area">

        <?php
        if ( have_comments() ) : ?>
            <div class="comment-list-wrap">

                <h2 class="comments-title">
                    <?php
                        $comment_count = get_comments_number();
                        if ( 1 === intval($comment_count) ) {
                            echo esc_html__( '1 Comment', 'frameflow' );
                        } else {
                            echo esc_attr( $comment_count ).' '.esc_html__('Comments', 'frameflow');
                        }
                    ?>
                </h2>

                <?php the_comments_navigation(); ?>

                <ul class="comment-list">
                    <?php
                        wp_list_comments( array(
                            'style'      => 'ul',
                            'short_ping' => true,
                            'callback'   => 'frameflow_comment_list',
                            'max_depth'  => 3
                        ) );
                    ?>
                </ul>

                <?php the_comments_navigation(); ?>
            </div>
            <?php if ( ! comments_open() ) : ?>
                <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'frameflow' ); ?></p>
            <?php
            endif;

        endif;

    $args = array(
            'id_form'           => 'commentform',
            'id_submit'         => 'submit',
            'class_submit'         => 'btn-submit btn btn-2-icons',
            'title_reply'       => esc_attr__( 'Leave A Comment', 'frameflow'),
            'title_reply_to'    => esc_attr__( 'Leave A Comment To ', 'frameflow') . '%s',
            'cancel_reply_link' => esc_attr__( 'Cancel Comment', 'frameflow'),
            'submit_button'     => '<button name="%1$s" type="submit" id="%2$s" class="%3$s" />%4$s</button>',
            'comment_notes_before' => '',
            'fields' => apply_filters( 'comment_form_default_fields', array(

                    'author' =>
                    '<div class="row"><div class="col-12 notice-f">'. esc_attr( 'Your email address will not be published. Required fields are marked *', 'frameflow' ) .'</div><div class="comment-form-author col-lg-6 col-md-6 col-sm-12"><input id="author" name="author" type="text" placeholder="'.esc_attr__('Your name*', 'frameflow').'" value="' . esc_attr( $commenter['comment_author'] ) .
                    '" size="30"/></div>',

                    'email' =>
                    '<div class="comment-form-email col-lg-6 col-md-6 col-sm-12"><input id="email" name="email" type="text" placeholder="'.esc_attr__('Email address*', 'frameflow').'" value="' . esc_attr(  $commenter['comment_author_email'] ) .
                    '" size="30"/></div>',
            )
            ),
            'comment_field' =>  '<div class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="8" placeholder="'.esc_attr__('Write comment...', 'frameflow').'" aria-required="true">' .
            '</textarea></div><div class="col-12 notice-f">'. esc_attr( 'Please note, your email won’t be published.', 'frameflow' ) .'</div>',
    );
    comment_form($args); ?>
</div>