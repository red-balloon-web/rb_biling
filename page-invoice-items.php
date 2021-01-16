<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} ?>

<?php 
get_header(); 
$client = new Client($_GET['client_id']); ?>

<!-- page header -->
<div class="rb-section-container page_header">
    <div class="rb-container">

        <!-- breadcrumb -->
        <p class="breadcrumb"><a href="<?php echo get_site_url(); ?>/clients">Clients</a><span class="chevron">></span><a href="<?php echo get_site_url() . '/client/' . $client->id; ?>"><?php echo $client->client_name; ?></a><span class="chevron">></span>Invoice Items</p>

        <!-- title and buttons -->
        <h1><?php echo $client->client_name; ?><span class="headerbuttons"><a href="<?php echo get_site_url() . '/edit-invoice-item?client_id=' . $client->id . '&action=create'; ?>" class="button">Create Invoice Item</a></span></h1>

        <!-- subtitle -->
        <h2 class="subtitle">Invoice Items</h2>
    
    </div>
</div>

<form action="<?php echo get_site_url() . '/invoice-items?client_id=' . $client->id; ?>" method="post">

<!-- table of invoice items -->
<div class="rb-section-container ii_table">
    <div class="rb-container">
        <table class="primary-table">
            <!-- header row -->
            <thead>
                <tr>
                    <td class="rb-table-left">Item</td>
                    <td>Date</td>
                    <td>Value</td>
                    <td>Status</td>
                    <td>Add to Invoice</td>
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
                        <td><?php echo $invoice_item->display_ii_status(); ?></td>
                        <td><?php
                            if ($invoice_item->invoice_item_status == 'unbilled') { ?>
                                <input type="checkbox" name="invoice_items[]" value=<?php echo $invoice_item->id; ?>><?php
                            } ?>
                        </td>
                    </tr><?php
                } ?>
                
            </tbody>
        </table>
    </div>
</div>

<!-- raise invoice box -->
<div class="rb-section-container raise-invoice-box">
    <div class="rb-container">
        <div class="submit">
            <input type="submit" value="Create Invoice">
        </div>
        <div class="days">
            <p>days</p>
        </div>
        <div class="terms">
            <p class="header-label">Terms</p>
            <input type="text" name="terms" value="30" class="tiny">
        </div>
        <div class="date">
            <p class="header-label">Date</p>
            <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>">
        </div>
    </div>
</div>

<input type="hidden" name="action" value="create_invoice">
<input type="hidden" name="client_id" value="<?php echo $client->id; ?>">
</form>

<?php get_footer(); ?>