<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} 

?>

<?php get_header(); 
if ( have_posts() ) : while ( have_posts() ) : the_post(); // start of the loop
$client = new Client(get_the_id()); // set up client object
/*
$email_message = "Welcome to the Red Balloon Billing Platform!\r\nYour unique code is " . $client->login_key;

wp_mail($client->email, 'Welcome to the Red Balloon Billing Platform!', $email_message, 'From: Red Balloon <noreply@billing.redballoonweb.com');
*/
?>

<!-- page header -->
<div class="page_header rb-section-container">
    <div class="rb-container">

        <!-- breadcrumb -->
        <p class="breadcrumb"><a href="<?php echo get_site_url(); ?>/clients">Clients</a><span class="chevron">></span><?php echo $client->client_name; ?></p>

        <!-- title and buttons -->
        <h1><?php echo $client->client_name; ?><span class="headerbuttons"><!--<a href="<?php echo get_site_url() . '/invoice-items?client_id=' . $client->id; ?>" class="button">Invoice Items</a>--><a href="<?php echo get_site_url() . '/edit-invoice-item?client_id=' . $client->id . '&action=create'; ?>"  class="button">Create Invoice Item</a></span></h1>

        <!-- subtitle -->
        <!--<h2 class="subtitle">This is the subtitle</h2>-->

        <!-- title details --> 
        <!--<div class="title_details">
            <p>Here is the first detail</p>
            <p>Here is the second detail</p>
        </div>-->
    </div>
</div>
        
<div class="rb-section-container client-info">
    <div class="rb-container">

        <!-- client details section -->
        <div class="details">
            <table><?php
                if ($client->address) { ?>
                    <tr>
                        <td valign="top"><i class="fas fa-home"></i></td>
                        <td>
                            <?php echo nl2br($client->address); ?>
                        </td>
                    </tr><?php
                }
                if ($client->phone) { ?>
                    <tr>
                        <td valign="top"><i class="fas fa-phone"></i></td>
                        <td><?php echo $client->phone; ?></td>
                    </tr><?php
                }
                if ($client->email) { ?>
                    <tr>
                        <td valign="top"><i class="fas fa-envelope"></i></td>
                        <td><?php echo $client->email; ?></td>
                    </tr><?php
                }
                if ($client->website) { ?>
                    <tr>
                        <td valign="top"><i class="fas fa-globe"></i></td>
                        <td><?php echo $client->website; ?></td>
                    </tr><?php
                } ?>
                <!--<tr>
                    <td colspan="2">Uninvoiced Limit: <strong>£<?php echo $client->uninvoiced_limit; ?></strong></td>
                </tr>-->
                <!--<tr>
                    <td colspan="2">Login Key: <?php echo $client->login_key; ?></td>
                </tr>-->
                <tr>
                    <td colspan="2"><a href="<?php echo get_site_url() . '/edit-client?edit=' . get_the_id(); ?>">Edit Details</a><br><a href="<?php echo get_site_url() . '/email-client-login?edit=' . get_the_id() . '&sendlogin=true'; ?>">Send Login Email</a></td>
                </tr>
            </table>
        </div>

        <!-- client finances table -->
        <div class="table">
            <table class="secondary-table">
                <tr><?php 
                        $most_recent = $client->return_most_recent(); ?>
                        <td>Most Recent: <a href="<?php echo $most_recent['permalink']; ?>"><?php echo $most_recent['title']; ?></a></td>
                    <td class="rb-right">£<?php echo $most_recent['value']; ?></td>
                </tr>
                <tr>
                    <td>Unbilled:</td>
                    <td class="rb-right">£<?php echo $client->calculate_unbilled(); ?></td>
                </tr>
                <tr>
                    <td>Due:</td>
                    <td class="rb-right">£<?php echo $client->get_total_due(); ?></td>
                </tr>
                <tr>
                    <td>Overdue</td>
                    <td class="rb-right">£0</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?php endwhile; endif; // end of the loop ?>

<!-- invoice items table -->
<div class="rb-section-container client_ii_table">
    <div class="rb-container">
        <table class="primary-table">
            <!-- header row -->
            <thead>
                <tr>
                    <td class="rb-table-left"><h2><a href="<?php echo get_site_url() . '/invoice-items?client_id=' . $client->id; ?>" class="button tabletop">View All</a>Invoice Items</h2></td>
                    <td>Date</td>
                    <td>Value</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody><?php

                // query
                $query_args = array(
                    'post_type' => 'invoice_item',
                    'meta_query' => array(
                        array(
                            'key' => 'client_id',
                            'value' => $client->id
                        ),
                        'ii_date' => array(
                            'key' => 'date'
                        )
                    ),
                    'orderby' => 'ii_date',
                    'order' => 'DSC',
                    'posts_per_page' => 3
                );
                $ii_query = new WP_Query($query_args);
                
                // loop to create other rows
                while ($ii_query->have_posts()) {
                    $ii_query->the_post();
                    $invoice_item = new InvoiceItem(get_the_id()); ?>
                    <tr>
                        <td class="rb-table-left"><a href="<?php echo get_the_permalink(); ?>"><?php echo $invoice_item->ii_title; ?></a></td>
                        <td><?php echo return_human_date($invoice_item->date); ?></td>
                        <td>£<?php echo $invoice_item->value; ?></td>
                        <td><a href=""><?php echo $invoice_item->display_ii_status(); ?></a></td>
                    </tr><?php
                    
                }?>
            </tbody>
        </table>
    </div>
</div>

<!-- invoices table -->
<div class="rb-section-container client-invoice-table">
    <div class="rb-container">
        <table class="primary-table">
            <!-- header row -->
            <thead>
                <tr>
                    <td><h2><a href="<?php echo get_site_url() . '/invoices?client_id=' . $client->id; ?>" class="button tabletop">View All</a>Invoices</h2></td>
                    <td>Date</td>
                    <td>Value</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody><?php
                
                // query
                $query_args = array(
                    'post_type' => 'invoice',
                    'meta_query' => array(
                        array(
                            'key' => 'client_id',
                            'value' => $client->id
                        ),
                        'i_date' => array(
                            'key' => 'date'
                        )
                    ),
                    'orderby' => 'i_date',
                    'order' => 'DSC',
                    'posts_per_page' => 3
                );
                $i_query = new WP_Query($query_args);

                // loop to create other rows
                while ($i_query->have_posts()) {
                    $i_query->the_post();
                    $invoice = new Invoice(get_the_id()); ?>
                    <tr>
                        <td class="rb-table-left"><a href="<?php echo get_the_permalink(); ?>"><?php echo $invoice->reference; ?></a></td>
                        <td><?php echo return_human_date($invoice->date); ?></td>
                        <td>£<?php echo $invoice->i_value; ?></td>
                        <td><?php echo $invoice->display_invoice_status(); ?></td>
                    </tr><?php
                } ?>
            </tbody>
        </table>
    </div>
</div>

<?php get_footer(); ?>