<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} ?>

<?php get_header(); ?>

<!-- page header -->
<div class="rb-section-container pageheader">
    <div class="rb-container">

        <!-- title and buttons -->
        <h1 style="text-align: center">Clients<span class="headerbuttons"><a href="<?php echo get_site_url(); ?>/edit-client?create=true"  class="button">Create New</a></span></h1>
    
    </div>
</div>

<!-- filter box -->
<div class="rb-section-container clients-filter-box rb-pb-0 rb-pt-0">
    <div class="rb-container">
        <form action="" formname="clients-filter-form" method="get">
            <div class="sort-by">
                <p class="header-label">Sort By</p>
                <select name="sort_by" formname="clients-filter-form">
                    <option value="alphabetical" <?php if ($_GET['sort_by'] == 'alphabetical') { echo ' selected'; } ?>>Alphabetical</option>
                    <option value="most_recent" <?php if ($_GET['sort_by'] == 'most_recent') { echo ' selected'; } ?>>Most Recent</option>
                    <option value="total_unbilled" <?php if ($_GET['sort_by'] == 'total_unbilled') { echo ' selected'; } ?>>Total Unbilled</option>
                    <option value="total_due" <?php if ($_GET['sort_by'] == 'total_due') { echo ' selected'; } ?>>Invoices Due</option>
                </select>
            </div>
            <div class="display">
                <p class="header-label">Display</p>
                <select name="display" formname="clients-filter-form">
                    <option value="current" <?php if ($_GET['display'] == 'current') { echo ' selected'; } ?>>Current</option>
                    <option value="archived" <?php if ($_GET['display'] == 'archived') { echo ' selected'; } ?>>Archived</option>
                </select>
            </div>
            <div class="submit">
                <input type="submit" value="Update">
            </div>
        </form>
    </div>
</div>

<!-- clients table -->
<div class="rb-section-container clients-table">
    <div class="rb-container">
        <table class="primary-table">
            <form action="<?php echo get_site_url() . '/clients?resp=achdel'; ?>" method="post">
                <thead>
                    <tr>
                        <td>
                            <select name="client_arcdel_action">
                                <option value="archived">Archive</option>
                                <option value="deleted">Delete</option>
                            </select>    
                            <input type="submit" value="Go" class="mini-submit">
                        </td>
                        <td style="text-align: left">Client</td>
                        <td>Last Work</td>
                        <td>Uninvoiced Work</td>
                        <td>Invoices Due</td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        // The Query
                        if ($_GET['display']) {
                            $client_status = $_GET['display'];
                        } else {
                            $client_status = 'current';
                        }

                        if ($_GET['sort_by']) {
                            if ($_GET['sort_by'] == 'alphabetical') {
                                $orderby = 'alphabet';
                                $order = 'ASC';
                            } else if ($_GET['sort_by'] == 'total_due') {
                                $meta_key = 'total_due';
                                $orderby = 'meta_value_num';
                                $order = 'DSC';
                            } else if ($_GET['sort_by'] == 'most_recent') {
                                $orderby = 'most_recent';
                                $order = 'DSC';
                            } else if ($_GET['sort_by'] == 'total_unbilled') {
                                $meta_key = 'total_unbilled';
                                $orderby = 'meta_value_num';
                                $order = 'DSC';
                            }
                        } else {
                            $orderby = 'alphabet';
                            $order = 'ASC';
                        }

                        $args = array(
                            'post_type' =>'client',
                            'meta_query' => array(
                                array(
                                    'key' => 'client_status',
                                    'value' => $client_status
                                ),
                                'alphabet' => array(
                                    'key' => 'client_name'
                                ),
                                'most_recent' => array(
                                    'key' => 'most_recent'
                                ),
                                'total_due' => array(
                                    'key' => 'total_due'
                                ),
                                'total_unbilled_sort' => array(
                                    'key' => 'total_unbilled'
                                )
                            ),
                            'meta_key' => $meta_key,
                            'orderby' => $orderby,
                            'order' => $order

                        );

                        $the_query = new WP_Query( $args );
                        //print_r($the_query);
                        
                        // The Loop
                        if ( $the_query->have_posts() ) {
                            while ( $the_query->have_posts() ) {
                                $the_query->the_post();
                                $client = new Client(get_the_id());
                                $most_recent_work = $client->return_most_recent();
                                
                                ?>
                                    <tr>
                                        <td><input type="checkbox" class="tablehack" name="client_archive_delete[]" value="<?php echo $client->id; ?>"></td>
                                        <td style="text-align: left"><a href="<?php echo get_the_permalink($client->id);  ?>"><?php echo $client->client_name; ?></a></td>
                                        <td><?php 
                                            if ($most_recent_work) {
                                                echo return_human_date($most_recent_work['date']);
                                            } ?>
                                        </td>
                                        <td>£<?php echo $client->calculate_unbilled(); ?></td>
                                        <td>£<?php echo $client->get_total_due(); ?></td>
                                    </tr>
                                <?php
                            }
                        } 
                        wp_reset_postdata();
                    ?>
                </tbody>
            </form>
        </table>
    </div>
</div>

<?php get_footer(); ?>