<?php get_header('client'); 

// Validate user and create client object
$client = new Client(do_client_security()); ?>

<!-- page header -->
<div class="rb-section-container pageheader">
    <div class="rb-container">

        <!-- title and buttons -->
        <h1 style="text-align: center" class="mobile-marbot"><?php echo $client->client_name; ?></h1>
        <h2 style="text-align: center">Invoices</h2>
    
    </div>
</div>

<div class="rb-section-container client-invoice-items-invoice-items-table desktop">
    <div class="rb-container">
        <table class="primary-table">
            <!-- header row -->
            <thead>
                <tr>
                    <td class="rb-table-left">Invoice (click to view)</td>
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
                ); 
                $i_query = new WP_Query($query_args);

                // loop to create other rows
                while ($i_query->have_posts()) {
                    $i_query->the_post();
                    $invoice = new Invoice(get_the_id()); ?>
                    <tr>
                        <td class="rb-table-left"><a href="<?php echo get_site_url() . '/client-single-invoice?id=' . $invoice->id; ?>"><?php echo $invoice->reference; ?></a></td>
                        <td><?php echo return_human_date($invoice->date); ?></td>
                        <td>£<?php echo $invoice->i_value; ?></td>
                        <td><?php echo $invoice->display_invoice_status(); ?></td>
                    </tr><?php
                } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="rb-section-container client-invoices-list-mobile mobile-list mobile">
    <div class="rb-container"><?php

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
            <h6><a href="<?php echo get_site_url(); ?>/client-single-invoice?id=<?php echo $invoice->id; ?>"><?php echo $invoice->reference; ?></a></h6>
            <p><?php echo return_human_date($invoice->date); ?></p>
            <p>£<?php echo $invoice->i_value; ?></p>
            <p><?php echo $invoice->display_invoice_status(); ?></p><?php
            
        }?>
    </div>
</div>

<?php get_footer(); ?>