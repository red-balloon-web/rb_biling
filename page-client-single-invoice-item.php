<?php if (!defined('ABSPATH')) { exit; }

get_header('client'); 

// Validate user and create client object
$client = new Client(do_client_security());

// query
$query_args = array(
    'post_type' => 'invoice_item',
    'p' => $_GET['id']
);

$ii_query = new WP_Query($query_args);

// Check the user is not messing around with the URL
if (!$ii_query->have_posts()) {
    do_unauthorised_access_message();
}


if ( $ii_query->have_posts() ) : while ( $ii_query->have_posts() ) : $ii_query->the_post(); // start of the loop


// set up objects
$invoice_item = new InvoiceItem(get_the_ID());

// Check this item belongs to the user and they're not messing around with the URL
if ($invoice_item->client_id != $client->id) {
    do_unauthorised_access_message();
} ?>

<!-- page header -->
<div class="page_header rb-section-container">
    <div class="rb-container desktop">
        <!-- title and buttons -->
        <h1>View Invoice Item<span class="headerbuttons"><a href="<?php echo get_site_url() . '/client-iipdf?id=' . $invoice_item->id; ?>" class="button" target="_blank">Create PDF</a></span></h1>

        <!-- subtitle -->
        <h2 class="subtitle"><?php echo $invoice_item->ii_title; ?></h2>

        <!-- title details --> 
        <div class="title_details"><?php
            if ($invoice_item->invoice_item_status != 'report') { ?>
                <p>Invoice: <?php echo $invoice_item->display_ii_status(); ?></p><?php
            } ?>
        </div>
    </div>
    
    <div class="rb-container mobile">
        <h1 class="rb-center" style="margin-bottom: 0px;"><?php echo $invoice_item->ii_title; ?></h1>
        <p class="rb-center" style="margin-bottom: 0; line-height: 1.5"><?php echo return_human_date($invoice_item->date); ?></p>
        <p class="rb-center" style="margin-bottom: 0; line-height: 1.5">Invoice: <?php echo $invoice_item->display_ii_status(true); ?></p>
        
    </div>
</div>

<hr>

<!-- invoice item proper -->
<!-- logo and address -->
<div class="rb-section-container invoice-report">
    <div class="rb-container">
        <div class="logo-address desktop">
            <div class="logo"><span class="red">Red</span> Balloon<span class="url">www.redballoonweb.com</span></div>
            <div class="address">Red Balloon Web Limited<br>
                34 Windmill House<br>
                Waterloo<br>
                London SE1 8LX</div>
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

        <!-- mobile download pdf button --> 
        <div class="create-pdf mobile">
            <hr>
            <p class="rb-center" style="margin-bottom: 0; line-height: 1.5"><a href="<?php echo get_site_url() . '/client-iipdf?id=' . $invoice_item->id; ?>">Download PDF</a></p>
        </div>
    </div>
</div>

<?php endwhile; endif; // end of the loop ?>

<?php get_footer(); ?>