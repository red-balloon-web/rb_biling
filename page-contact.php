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
        <h2>Contact Form</h2>
        <?php echo do_shortcode( ' [contact-form-7 id="39" title="Contact form 1"] ' ); ?>
    </div>
</div>


<?php get_footer(); ?>