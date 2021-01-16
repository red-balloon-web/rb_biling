<?php get_header('client'); 

// Validate user and create client object
$client = new Client(do_client_security());

?>



<!-- page header -->
<div class="rb-section-container pageheader">
    <div class="rb-container">

        <!-- title and buttons -->
        <h1 style="text-align: center"><?php echo $client->client_name; ?></h1>
    
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
            </table>
        </div>

        <!-- client finances table -->
        <div class="table">
            <table class="secondary-table">
                <tr><?php 
                        $most_recent = $client->return_most_recent(); ?>
                        <td>Most Recent: <a href="<?php echo get_site_url() . '/client-single-invoice-item?id=' . $most_recent['id']; ?>"><?php echo $most_recent['title']; ?></a></td>
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

<div class="rb-section-container client-home-invoice-items-table desktop">
    <div class="rb-container">
        <table class="primary-table">
            <!-- header row -->
            <thead>
                <tr>
                    <td class="rb-table-left"><h2><a href="<?php echo get_site_url() . '/client-invoice-items'; ?>" class="button tabletop">View All</a>Invoice Items</h2></td>
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
                        <td class="rb-table-left mobile-full"><a href="<?php echo get_site_url() . '/client-single-invoice-item?id=' . $invoice_item->id; ?>"><?php echo $invoice_item->ii_title; ?></a></td>
                        <td><?php echo return_human_date($invoice_item->date); ?></td>
                        <td>£<?php echo $invoice_item->value; ?></td>
                        <td><a href=""><?php echo $invoice_item->display_ii_status(); ?></a></td>
                    </tr><?php
                    
                }?>
            </tbody>
        </table>
    </div>
</div>

<div class="rb-section-container client-home-invoice-item-mobile-list mobile-list mobile">
    <div class="rb-container">
        <h2>Recent Invoice Items</h2>
        <p class="rb-center"><a href="<?php echo get_site_url(); ?>/client-invoice-items">View All</a></p><?php

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
            
            <h6><a href="<?php echo get_site_url(); ?>/client-single-invoice-item?id=<?php echo $invoice_item->id; ?>"><?php echo $invoice_item->ii_title; ?></a></h6>
            <p><?php echo return_human_date($invoice_item->date); ?></p>
            <p>£<?php echo $invoice_item->value; ?></p>
            <p><?php echo $invoice_item->display_ii_status(); ?></p>
            
            <?php
            
        }?>
    </div>
</div>

<div class="rb-section-container client-home-invoice-table desktop">
    <div class="rb-container">
        <table class="primary-table">
            <!-- header row -->
            <thead>
                <tr>
                    <td><h2><a href="<?php echo get_site_url() . '/client-invoices'; ?>" class="button tabletop">View All</a>Invoices</h2></td>
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

<div class="rb-section-container client-home-invoice-item-mobile-list mobile-list mobile">
    <div class="rb-container">
        <h2 style="margin-top: 3rem;">Recent Invoices</h2>
        <p class="rb-center"><a href="<?php echo get_site_url(); ?>/client-invoices">View All</a></p><?php

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
            
            <h6><a href="<?php echo get_site_url(); ?>/client-single-invoice?id=<?php echo $invoice->id; ?>"><?php echo $invoice->reference; ?></h6>
            <p><?php echo return_human_date($invoice->date); ?></p>
            <p>£<?php echo $invoice->i_value; ?></p>
            <p><?php echo $invoice->display_invoice_status(); ?></p>
            
            <?php
            
        }?>
    </div>
</div>

<?php get_footer(); ?>