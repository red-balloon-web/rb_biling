<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} ?>

<?php get_header(); 
if ( have_posts() ) : while ( have_posts() ) : the_post(); // start of the loop
$invoice = new Invoice(get_the_ID()); 
$client = new Client($invoice->client_id); ?>

<!-- page header -->
<div class="page_header rb-section-container">
    <div class="rb-container">

        <!-- breadcrumb -->
        <p class="breadcrumb"><a href="<?php echo get_site_url(); ?>/clients">Clients</a><span class="chevron">></span><a href="<?php echo get_site_url() . '/client/' . $client->id; ?>"><?php echo $client->client_name; ?></a><span class="chevron">></span><a href="<?php echo get_site_url() . '/invoices?client_id=' . $client->id; ?>">Invoices</a><span class="chevron">></span>View Invoice</p>

        <!-- title and buttons -->
        <h1><?php echo $client->client_name; ?><span class="headerbuttons"><a href="<?php echo get_site_url(); ?>/invoice-pdf?id=<?php echo $invoice->id; ?>" class="button" target="_blank">Create PDF</a></span></h1>

        <!-- subtitle -->
        <h2 class="subtitle">View Invoice</h2>

        <!-- title details --> 
        <div class="title_details">
            <p>Ref: <?php echo $invoice->reference; ?></p>
            <p>Date: <?php echo return_human_date($invoice->date); ?></p>
            <p>Status: <?php echo $invoice->status; ?></p>
        </div>
    </div>
</div>

<hr>

<!-- invoice proper -->
<div class="rb-section-container invoice-report">
    <div class="rb-container">
        <!-- header -->
        <div class="logo-address">
            <div class="logo"><span class="red">Red</span> Balloon<span class="url">www.redballoonweb.com</span></div>
            <div class="address">Red Balloon Web Limited<br>
                Unit B<br>
                Office 36<br>
                Hatton Garden<br>
                London EC1</div>
        </div>
        <div class="invoice-header">
            <h2>INVOICE</h2>
        </div>

        <!-- summary -->
        <div class="report-summary">
            <p><?php echo $client->client_name; ?><br><?php 
            echo return_human_date($invoice->date); ?><br>
            Ref: <?php echo $invoice->reference; ?></p>
        </div><?php
        
        // List invoice items
        $query_args = array(
            'post__in' => explode( ',', $invoice->invoice_items),
            'post_type'=> 'invoice_item',
            'meta_query' => array(
                'ii_date' => array(
                    'key' => 'date'
                )
            ),
            'orderby' => 'ii_date',
            'order' => 'ASC'
        );
        $query = new WP_Query($query_args);
        while ($query->have_posts()) { // loop
            $query->the_post(); 
            $invoice_item = new InvoiceItem(get_the_ID()); ?>
            <div class="invoice-item"><?php

                // if it's a fixed rate item
                if ($invoice_item->ii_type == 'fixed_rate') { ?>
                    <p><?php echo return_human_date($invoice_item->date); ?></p>
                    <div class="priceline">
                        <p><?php echo $invoice_item->ii_title; ?></p>
                        <div class="underline"></div>
                        <p>£<?php echo $invoice_item->value; ?></p>
                    </div><?php

                // if it's an hourly item
                } else if ($invoice_item->ii_type == 'hourly') { ?>
                    <p><?php echo return_human_date($invoice_item->date); ?><br><?php echo $invoice_item->ii_title; ?></p>
                    <div class="priceline">
                        <p><?php echo $invoice_item->hours; ?>h @ <?php echo $invoice_item->hourly_rate_numeric; ?>/h</p>
                        <div class="underline"></div>
                        <p>£<?php echo $invoice_item->value; ?></p>
                    </div><?php
                } ?>
            </div>
            <?php
        } ?> 
        
        <!-- total and footer -->
        <div class="invoice-item total">
            <div class="priceline">
                <p>Total</p>
                <p>£<?php echo $invoice->i_value; ?></p>
            </div>
        </div>
        <div class="invoice-terms">
            <p>Terms: <?php echo $invoice->terms; ?> days</p>
            <p>BACS Payments: 40-07-30 81568205</p>
        </div>
        <div class="invoice-thankyou">
            <p>Thank you for being a valued client. If you have any questions or queries please contact me at chris@redballoonweb or on 07786 562 022</p>
        </div>
    </div>
</div>

<?php endwhile; endif; // end of the loop ?>

<?php get_footer(); ?>