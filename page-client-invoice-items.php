<?php get_header('client'); 

// Validate user and create client object
$client = new Client(do_client_security()); ?>

<!-- page header -->
<div class="rb-section-container pageheader">
    <div class="rb-container">

        <!-- title and buttons -->
        <h1 style="text-align: center" class="mobile-marbot"><?php echo $client->client_name; ?></h1>
        <h2 style="text-align: center">Invoice Items</h2>
    
    </div>
</div>

<div class="rb-section-container client-invoice-items-invoice-items-table desktop">
    <div class="rb-container">
        <table class="primary-table">
            <!-- header row -->
            <thead>
                <tr>
                    <td class="rb-table-left">Item</td>
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
                    'posts_per_page' => -1
                );
                $ii_query = new WP_Query($query_args);
                
                // loop to create other rows
                while ($ii_query->have_posts()) {
                    $ii_query->the_post();
                    $invoice_item = new InvoiceItem(get_the_id()); ?>
                    <tr>
                        <td class="rb-table-left"><a href="<?php echo get_site_url() . '/client-single-invoice-item?id=' . $invoice_item->id; ?>"><?php echo $invoice_item->ii_title; ?></a></td>
                        <td><?php echo return_human_date($invoice_item->date); ?></td>
                        <td>£<?php echo $invoice_item->value; ?></td>
                        <td><?php echo $invoice_item->display_ii_status(); ?></td>
                    </tr><?php
                    
                }?>
            </tbody>
        </table>
    </div>
</div>

<div class="rb-section-container client-invoice-items-list-mobile mobile-list mobile">
    <div class="rb-container"><?php
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
            'posts_per_page' => -1
        );
        $ii_query = new WP_Query($query_args);

        // loop to create other rows
        while ($ii_query->have_posts()) {
            $ii_query->the_post();
            $invoice_item = new InvoiceItem(get_the_id()); ?>
            <h6><a href="<?php echo get_site_url(); ?>/client-single-invoice-item?id=<?php echo $invoice_item->id; ?>"><?php echo $invoice_item->ii_title; ?></a></h6>
            <p><?php echo return_human_date($invoice_item->date); ?></p>
            <p>£<?php echo $invoice_item->value; ?></p>
            <p><?php echo $invoice_item->display_ii_status(true); ?></p><?php
            
        }?>
    </div>
</div>

<?php get_footer(); ?>