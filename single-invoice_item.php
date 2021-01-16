<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} ?>

<?php 
get_header(); 
if ( have_posts() ) : while ( have_posts() ) : the_post(); // start of the loop

// set up objects
$invoice_item = new InvoiceItem(get_the_ID());
$client = new Client($invoice_item->client_id); ?>

<!-- page header -->
<div class="page_header rb-section-container">
    <div class="rb-container">

    <!-- breadcrumb -->
    <p class="breadcrumb"><a href="<?php echo get_site_url(); ?>/clients">Clients</a><span class="chevron">></span><a href="<?php echo get_site_url() . '/client/' . $client->id; ?>"><?php echo $client->client_name; ?></a><span class="chevron">></span><a href="<?php echo get_site_url() . '/invoice-items?client_id=' . $client->id; ?>">Invoice Items</a><span class="chevron">></span>View Invoice Item</p>

    <!-- title and buttons -->
    <h1><?php echo $client->client_name; ?><span class="headerbuttons"><a href="<?php echo get_site_url() . '/edit-invoice-item?edit=' . $invoice_item->id; ?>" class="button">Edit</a><a href="<?php echo get_site_url() . '/invoice-item-pdf?id=' . $invoice_item->id; ?>" class="button" target="_blank">Create PDF</a></span></h1>

    <!-- subtitle -->
    <h2 class="subtitle">View Invoice Item</h2>

    <!-- title details --> 
    <div class="title_details"><?php
        if ($invoice_item->invoice_item_status != 'report') { ?>
            <p>Invoice: <?php echo $invoice_item->display_ii_status(); ?></p><?php
        }

        if ($invoice_item->invoice_item_status == 'unbilled' || $invoice_item->ii_type == 'report') { ?>
            <p><a href="<?php echo get_site_url() . '/invoice-items?client_id=' . $client->id . '&resp=delete_ii&ii=' . $invoice_item->id; ?>">Delete</a></p><?php
        } ?>
    </div>

    </div>
</div>

<hr>

<!-- invoice item proper -->
<!-- logo and address -->
<div class="rb-section-container invoice-report">
    <div class="rb-container">
        <div class="logo-address">
            <div class="logo"><span class="red">Red</span> Balloon<span class="url">www.redballoonweb.com</span></div>
            <div class="address">Red Balloon Web Limited<br>
                Unit B<br>
                Office 36<br>
                Hatton Garden<br>
                London EC1</div>
        </div>
        <div class="invoice-header">
            <h2><?php
                // show the right header
                if ($invoice_item->ii_type == 'hourly') {
                    echo 'Hourly Work Report';
                } else if ($invoice_item->ii_type == 'fixed_rate') {
                    echo 'Work Item';
                } else if ($invoice_item->ii_type == 'report') {
                    echo 'Work Report'; 
                } ?>
            </h2><?php
            if ($invoice_item->ii_type != 'report') { ?>
                <p>This is not an invoice</p><?php
            } ?>
        </div>

        <!-- date and type -->
        <div class="report-summary">
            <p>Date: <strong><?php echo return_human_date($invoice_item->date); ?></strong><br><?php
            if ($invoice_item->ii_type != 'report') { ?>            
                Type: <strong><?php echo $invoice_item->display_ii_type(); ?> </strong><br><?php
                
                if ($invoice_item->ii_type == 'hourly') { ?>
                    Billable Hours: <strong><?php echo $invoice_item->hours; ?></strong><br>
                    Hourly Rate: <strong>£<?php echo $invoice_item->hourly_rate_numeric; ?></strong><br><?php 
                } ?>
                To Invoice: <strong>£<?php echo $invoice_item->value; ?></strong></p><?php
            } ?>
        </div>

        <!-- title, project and notes -->
        <div class="report-body">
            <div class="report-body__header">
                <h3 class="report-title"><?php echo $invoice_item->ii_title; ?></h3>
                <p class="project"><?php echo $invoice_item->ii_project; ?></p>
            </div><?php
            if ($invoice_item->client_notes) { ?>
                <div class="report-body__client-notes">
                    <p class="title">Client Notes</p>
                    <p><?php echo nl2br($invoice_item->client_notes); ?></p>
                </div> <?php
            } 
            if ($invoice_item->technical_notes) { ?>
                <div class="report-body__tech-notes">
                    <p class="title">Technical Notes</p>
                    <p><?php echo nl2br(stripslashes($invoice_item->technical_notes)); ?></p>
                </div><?php
            } ?>
        </div>
    </div>
</div>

<?php endwhile; endif; // end of the loop ?>

<?php get_footer(); ?>