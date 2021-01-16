<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} ?>

<?php get_header(); ?>

<div class="rb-section-container page_header">
    <div class="rb-container"><?php

        // display header if creating new client
        if ($_GET['create']) { 
            $client = new Client; 
            if ($_GET['resp'] == 'f_val') {
                $client->update_from_form();
            } ?>

            <!-- breadcrumb -->
            <p class="breadcrumb"><a href="<?php echo get_site_url(); ?>/clients">Clients</a><span class="chevron">></span>Create New</p>

            <!-- title and buttons -->
            <h1>Create Client <span class="headerbuttons"></span></h1><?php

        // display header if updating client
        } else if ($_GET['edit']) { 
            $client = new Client($_GET['edit']); ?>
            
            
            <!-- breadcrumb -->
            <p class="breadcrumb"><a href="<?php echo get_site_url(); ?>/clients">Clients</a><span class="chevron">></span><a href="<?php echo get_site_url() . '/client/' . $client->id; ?> "><?php echo $client->client_name; ?></a><span class="chevron">></span>Edit</p>
            <h1><?php echo $client->client_name; ?> <span class="headerbuttons"></span></h1>
            <h2 class="subtitle">Edit Details</h2><?php

            // if we have returned because validation has failed then load submitted values (must come after breadcrumb)
            if ($_GET['resp'] == 'f_val') {
                $client->update_from_form();
            }
            
        // exit if we are doing neither
        } else {
            exit;
        } ?>

    </div>
</div>

<div class="rb-section-container edit-client-form">
    <div class="rb-container">
        <form class="primary-form" <?php 

            // print appropriate form destination (back to self with f_val)
            if ($_GET['create']) {
                echo 'action="' . get_site_url() . '/edit-client?create=true&resp=f_val"';
            } else {
                echo 'action="' . get_site_url() . '/edit-client?edit=' . $client->id . '&resp=f_val"';
            } ?> method="post"><?php
        
            // display appropriate hidden fields
            if ($_GET['create']) { ?>
                <input type="hidden" name="action" value="create_client"><?php
            } else { ?>
                <input type="hidden" name="action" value="edit_client">
                <input type="hidden" name="id" value="<?php echo $client->id; ?>"><?php
            }
            
            // display fields ?>          
            <div class="fields">
                <div class="col-1"><?php
                    $client->do_input_field('client_name');
                    $client->do_input_field('contact_person');
                    $client->do_input_field('phone');
                    $client->do_input_field('website');
                    $client->do_input_field('address'); ?>
                </div>
                <div class="col-2"><?php
                    $client->do_input_field('email');
                    $client->do_input_field('prefix');
                    $client->do_input_field('uninvoiced_limit'); ?>
                </div>
            </div>

            <p><?php
                if ($_GET['create']) { ?>
                    <input type="submit" value="Create Client"><?php
                } else { ?>
                    <input type="submit" value="Update"><?php
                }?>
            </p>
        </form>
    </div>
</div>

<?php get_footer(); ?>