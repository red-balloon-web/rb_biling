<?php if (!defined('ABSPATH')) { exit; }

get_header('client'); 

// Validate user and create client object
$client = new Client(do_client_security()); 

// query
$query_args = array(
    'post_type' => 'invoice',
    'p' => $_GET['id']
);

$i_query = new WP_Query($query_args);

// Check the user is not messing around with the URL
if (!$i_query->have_posts()) {
    do_unauthorised_access_message();
}

if ( $i_query->have_posts() ) : while ( $i_query->have_posts() ) : $i_query->the_post(); // start of the loop

// set up objects
$invoice = new Invoice(get_the_ID());

// Check this item belongs to the user and they're not messing around with the URL
if ($invoice->client_id != $client->id) {
    do_unauthorised_access_message();
} ?>

<!-- page header -->
<div class="page_header rb-section-container">
    <div class="rb-container desktop">

        <!-- title and buttons -->
        <h1>View Invoice<span class="headerbuttons"><a href="<?php echo get_site_url(); ?>/client-ipdf?id=<?php echo $invoice->id; ?>" class="button" target="_blank">Create PDF</a></span></h1>

        <!-- subtitle -->
        <h2 class="subtitle"><?php echo return_human_date($invoice->date); ?></h2>

        <!-- title details --> 
        <div class="title_details">
            <p>Ref: <?php echo $invoice->reference; ?></p>
            <p>Date: <?php echo return_human_date($invoice->date); ?></p>
            <p>Status: <?php echo $invoice->display_invoice_status(); ?></p>
        </div>
    </div>
</div>

<hr class="desktop">

<!-- invoice proper -->
<div class="rb-section-container invoice-report">
    <div class="rb-container">
        <!-- header -->
        <div class="logo-address desktop">
            <div class="logo"><span class="red">Red</span> Balloon<span class="url">www.redballoonweb.com</span></div>
            <div class="address">Red Balloon Web Limited<br>
                34 Windmill House<br>
                Waterloo<br>
                London SE1 8LX</div>
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
        <div class="invoice-thankyou desktop">
            <p>Thank you for being a valued client. If you have any questions or queries please contact me at chris@redballoonweb or on 07786 562 022</p>
        </div>

        <div class="create-pdf mobile">
            <p class="rb-center" style="margin-bottom: 0; line-height: 1.5"><a href="<?php echo get_site_url(); ?>/client-ipdf?id=<?php echo $invoice->id; ?>">Download PDF</a></p>
        </div>
    </div>
</div>

<?php endwhile; endif; // end of the loop ?>

<?php get_footer(); ?>