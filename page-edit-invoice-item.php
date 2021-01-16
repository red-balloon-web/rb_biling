<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} ?>

<?php get_header(); 

// Create objects
if ($_GET['action'] == 'create') {
    $invoice_item = new InvoiceItem;
    if ($_GET['resp'] == 'f_val') {
        $invoice_item->update_from_form();
    }
    $client = new Client($_GET['client_id']);
} else if ($_GET['edit']) {
    $invoice_item = new InvoiceItem($_GET['edit']);
    if ($_GET['resp'] == 'f_val') {
        $invoice_item->update_from_form();
    }
    $client = new Client($invoice_item->client_id);
}


?>

<!-- page header -->
<div class="page_header rb-section-container">
    <div class="rb-container">

        <!-- breadcrumb -->
        <p class="breadcrumb"><a href="<?php echo get_site_url(); ?>/clients">Clients</a><span class="chevron">></span><a href="<?php echo get_site_url() . '/client/' . $client->id; ?>"><?php echo $client->client_name; ?></a><span class="chevron">></span><a href="<?php echo get_site_url() . '/invoice-items?client_id=' . $client->id; ?>">Invoice Items</a><span class="chevron">></span><?php

            if ($_GET['action'] == 'create') { echo 'Create'; }
            else if ($_GET['edit']) { echo 'Edit'; }?>
        </p>

        <!-- title and buttons -->
        <h1><?php echo $client->client_name; ?><span class="headerbuttons"></span></h1>

        <!-- subtitle --><?php
        if ($_GET['action'] == 'create') { ?>
            <h2 class="subtitle">Create Invoice Item</h2><?php
        } else if ($_GET['edit']) { ?>
            <h2 class="subtitle">Edit Invoice Item</h2><?php
        } ?>

        <!-- title details --> 
        <!--<div class="title_details">
            <p>Here is the first detail</p>
            <p>Here is the second detail</p>
        </div>-->
    </div>
</div>

<div class="rb-section-container create-invoice-item-form">
    <div class="rb-container"><?php

        // Display the right form destination (create or edit)
        if ($_GET['action'] == 'create') { ?>
            <form action="<?php echo get_site_url(); ?>/edit-invoice-item?client_id=<?php echo $client->id; ?>&action=create&resp=f_val" method="post">
            <input type="hidden" name="client_id" value="<?php echo $client->id; ?>">
            <input type="hidden" name="action" value="create_invoice_item"><?php
        } else if ($_GET['edit']) { ?>
            <form action="<?php echo get_site_url(); ?>/edit-invoice-item?edit=<?php echo $invoice_item->id; ?>&resp=f_val" method="post">
            <input type="hidden" name="client_id" value="<?php echo $invoice_item->client_id; ?>">
            <input type="hidden" name="action" value="update_invoice_item">
            <input type="hidden" name="ii_id" value="<?php echo $invoice_item->id; ?>"><?php
        } ?>

            <!-- form proper -->
            <div class="formrow">
                <div class="date">
                    <?php $invoice_item->do_input_field('date', '', 'date'); ?>
                </div>
                <div class="reference">
                    <?php $invoice_item->do_input_field('ii_type', '', 'ii_type'); ?>
                </div>
            </div>

            <div class="formrow" id="hourly_fields" style="<?php 
                if ($invoice_item->ii_type == 'fixed_rate' || $invoice_item->ii_type == 'report') {
                    echo 'display: none';
                } ?>">
                <div class="hours">
                    <?php $invoice_item->do_input_field('hours', '', 'hours'); ?>
                </div>
                <div class="hourly-text">
                    <?php $invoice_item->do_input_field('hourly_rate_text'); ?>
                </div>
                <div class="date">
                <?php $invoice_item->do_input_field('hourly_rate_numeric', 'short', 'hourly_rate_numeric'); ?> per hour
                    <div class="pound-sign">£</div>
                </div>
            </div>

            <div class="formrow" id="ii_value" style="<?php 
                if ($invoice_item->ii_type == 'report') {
                    echo 'display: none';
                } ?>">
                <div class="value">
                    <?php $invoice_item->do_input_field('value', '', 'value'); ?>
                    <div class="pound-sign">£</div>
                </div>
            </div>

            <div class="formrow">
                <div class="value">
                    <?php $invoice_item->do_input_field('ii_title', 'long'); ?>
                </div>
            </div>

            <div class="formrow">
                <div class="value">
                <?php $invoice_item->do_input_field('ii_project', 'long'); ?>
                </div>
            </div>

            <div class="formrow formrow--fullwidth">
                <div class="value">
                <?php $invoice_item->do_input_field('client_notes'); ?>
                </div>
            </div>

            <div class="formrow formrow--fullwidth">
                <div class="value">
                    <?php $invoice_item->do_input_field('technical_notes'); ?>
                </div>
            </div>

            <div class="formrow formrow--fullwidth">
                <p class="rb-right">
                    <input type="submit" value="<?php
                        if ($_GET['action'] == 'create') { echo 'Create '; }
                        else if ($_GET['edit']) { echo 'Update '; } ?>Invoice Item">
                </p>
            </div>

        </form>
    </div>
</div>

<!-- jQuery functions unique to this page -->
<script>

    jQuery(document).ready(function() {

        // If we are creating a new item then default to today's date
        <?php if ($_GET['action'] == 'create') { ?>
            dateField = document.getElementById('date');
            date = new Date();
            dateField.value = date.getFullYear().toString() + '-' + (date.getMonth() + 1).toString().padStart(2, 0) + '-' + date.getDate().toString().padStart(2, 0);
        <?php } ?>

        
        // Fade hourly section in and out when ii_type field changed
        jQuery('#ii_type').change(function() {
            selected_option = jQuery('#ii_type').find(':selected').attr('value');
            if (selected_option == 'hourly') {
                jQuery('#hourly_fields').slideDown(200);
                jQuery('#ii_value').slideDown(200);
                update_values();
            } else if (selected_option == 'fixed_rate') {
                jQuery('#hourly_fields').slideUp(200);
                jQuery('#ii_value').slideDown(200);
                jQuery('#hours').val('0');
                jQuery('#hourly_rate_numeric').val('0');
            } else if (selected_option == 'report') {
                jQuery('#hourly_fields').slideUp(200);
                jQuery('#ii_value').slideUp(200);
                jQuery('#hours').val('0');
                jQuery('#hourly_rate_numeric').val('0');
                jQuery('#value').val('0');
            }
        });

        // Update value field with calculation when hourly rate or hours changed
        jQuery('#hours, #hourly_rate_numeric').on('input', function() {
            update_values();
        });

        // Function to update value field
        function update_values() {
            value = jQuery('#hours').val() * jQuery('#hourly_rate_numeric').val();
            jQuery('#value').val(value.toFixed(2));
        }
    });

</script>

<?php get_footer(); ?>