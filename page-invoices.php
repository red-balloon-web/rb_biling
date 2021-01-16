<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} ?>

<?php get_header(); 
$client = new Client($_GET['client_id']);
?>

<!-- page header -->
<div class="rb-section-container page_header">
    <div class="rb-container">

        <!-- breadcrumb -->
        <p class="breadcrumb"><a href="<?php echo get_site_url(); ?>/clients">Clients</a><span class="chevron">></span><a href="<?php echo get_site_url() . '/client/' . $client->id; ?>"><?php echo $client->client_name; ?></a><span class="chevron">></span>Invoices</p>

        <!-- title and buttons -->
        <h1><?php echo $client->client_name; ?></h1>

        <!-- subtitle -->
        <h2 class="subtitle">Invoices</h2>
    
    </div>
</div>

<form action="<?php echo get_site_url() . '/invoices?client_id=' . $client->id . '&resp=invoice_functions'; ?>" method="post">
    <input type="hidden" name="action" value="invoice_functions">

<div class="rb-section-container">
    <div class="rb-container">
        <table class="primary-table">
            <thead>
                <tr>
                    <td class="rb-table-left">Item</td>
                    <td>Date</td>
                    <td>Value</td>
                    <td>Status</td>
                    <td>
                        <select name="invoice_option">
                            <option value="mark_paid">Mark as Paid</option>
                            <option value="cancel">Cancel</option>
                            <option value="write_off">Write Off</option>
                        </select>    
                        <input type="submit" value="Go" class="mini-submit">
                    </td>
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
                ); 
                $i_query = new WP_Query($query_args);

                // loop to create other rows
                while ($i_query->have_posts()) {
                    $i_query->the_post();
                    $invoice = new Invoice(get_the_id()); ?>
                    <tr>
                        <td class="rb-table-left"><a href="<?php echo get_site_url() . '/invoice/' . $invoice->id; ?>"><?php echo $invoice->reference; ?></a></td>
                        <td><?php echo $invoice->date; ?></td>
                        <td>£<?php echo $invoice->i_value; ?></td>
                        <td><?php echo $invoice->display_invoice_status(); ?></td>
                        <td><input type="checkbox" class="tablehack" name="invoices[]" value="<?php echo $invoice->id; ?>"></td>
                    </tr><?php
                } ?>
            </tbody>
        </table>
    </div>
</div>

</form>

<?php get_footer(); ?>