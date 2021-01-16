<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} ?>

<?php get_header(); ?>
<div class="rb-nav-pusher"></div>

<div class="rb-section-container">
    <div class="rb-container">
        <?php echo do_shortcode( ' [rb-simple-blog page="single"] ' ); ?>
    </div>
</div>



<?php get_footer(); ?>