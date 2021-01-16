<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 
/*
if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} 
*/
?>

<?php get_header('nomenu'); ?>

<div class="rb-section-container">
    <div class="rb-container">
        <!--<h1 class="rb-center">Welcome!</h1>-->
        <p class="rb-center">Welcome to the Red Ballon billing platform. To view your reports and invoices, please log in with your private link.</p>
        <p class="rb-center">Need a new link? Get in touch and we'll email you a new one straight away.</p>
    </div>
</div>

<?php get_footer(); ?>