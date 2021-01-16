<?php

// enqueue stylesheet
function enqueue_stylesheet() {
    wp_enqueue_style('style', get_stylesheet_uri() );
}      
add_action( 'wp_enqueue_scripts', 'enqueue_stylesheet' ); 

// theme support
add_theme_support( 'post-thumbnails' ); 

//custom excerpt length
add_filter( 'excerpt_length', function($length) {
    return 20;
} );

// register menus
function register_menus() {
    register_nav_menus(
        array(
            'rb-main-menu' => 'RB Main Menu',
            'rb-client-menu' => 'RB Client Menu',
            'rb-handheld-menu' => 'RB Handheld Menu'
        )
    );
}
add_action( 'init', 'register_menus' );

// register post types
function register_posttypes() {
    register_post_type( 'client',
        array(
            'labels' => array(
                'name' => 'Clients',
                'singular_name' => 'Client'
            ),
            'public' => true,
            'has_archive' => false,
            'rewrite' => array('slug' => 'client'),
        )
    );

    register_post_type( 'invoice_item',
        array(
            'labels' => array(
                'name' => 'Invoice Items',
                'singular_name' => 'Invoice Item'
            ),
            'public' => true,
            'has_archive' => false,
            'rewrite' => array('slug' => 'invoice-item')
        )
    );

    register_post_type( 'invoice',
        array(
            'labels' => array(
                'name' => 'Invoices',
                'singular_name' => 'Invoice'
            ),
            'public' => true,
            'has_archive' => false,
            'rewrite' => array('slug' => 'invoice')
        )
    );
}
add_action( 'init', 'register_posttypes' );


// register classes
include "inc/class-client.php";
include "inc/class-invoice-item.php";
include "inc/class-invoice.php";

// Update all client totals/due/etc (for now) this will update all the clients' values in the database - each function saves its result
$args = array(
    'post_type' => 'client'
);
$query = new WP_Query($args);
while ($query->have_posts()) {
    $query->the_post();
    $client = new Client(get_the_id());
    $client->calculate_unbilled();
    $client->return_most_recent();
    $client->get_total_due();
}

/**
 * Process and Redirect
 * Init hook
 * 
 * Validates forms sent by POST requests. On unsuccessful validation the request continues to the original address (the original page with ?resp=f_val) and on success the user is redirected to the success page
 */
function process_and_redirect() {

    // Archive / Delete Client (clients screen)
    if ($_POST['client_arcdel_action']) {
        $action = $_POST['client_arcdel_action'];
        $ids = $_POST['client_archive_delete'];

        foreach ($ids as $id) {
            echo $id;
            update_post_meta($id, 'client_status', $action);
        }
    }

    // Create Client (edit client screen)
    if ($_POST['action'] == 'create_client') {
        
        $validate_form = true;
        $client = new Client;

        foreach($client->properties as $property => $values) {
            if (validate_field($_POST[$property], $values['required'], $values['validate'])) {
                $validate_form = false;
            }
        }

        if ($validate_form) {
            $client->create();
            $client->update_from_form();
            $client->login_key = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 40)), 0, 40);
            $client->save();
            header('Location: ' . get_site_url() . '/client/' . $client->id . '?resp=client-created');
            exit;
        }

    }

    // Edit Client (edit client screen)
    if ($_POST['action'] == 'edit_client') {

        $validate_form = true;
        $client = new Client($_POST['id']);

        foreach($client->properties as $property => $values) {
            if (validate_field($_POST[$property], $values['required'], $values['validate'])) {
                $validate_form = false;
            }
        }

        if ($validate_form) {
            $client->update_from_form();
            $client->save();
            header('Location: ' . get_site_url() . '/client/' . $client->id . '?resp=client-updated');
            exit;
        }
    }

    // Send Login Email to Client (single client screen)
    if ($_GET['sendlogin']) {
        $client = new Client($_GET['edit']);
        $email_message = "You can log in to our billing platform at any time to view and download your previous reports and invoices. Use the following secure link to log in to the platform as " . $client->client_name . ".\r\n\r\nbilling.redballoonweb.com/client-welcome?key=" . $client->login_key . "\r\n\r\nIf you need a new link at any time, please drop us a line and we'll send you another one straight away.\r\n\r\nRed Balloon Web";
        wp_mail($client->email, 'Red Balloon Billing: login link for ' . $client->client_name, $email_message, 'From: Red Balloon Online Billing <accounts@redballoonweb.com');
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Location: ' . get_site_url() . '/client/' . $_GET['edit'] . '?resp=login-sent', TRUE, 307);
        exit;
    }

    // Create Invoice Item (edit invoice item screen)
    if ($_POST['action'] == 'create_invoice_item') {

        $validate_form = true;
        $invoice_item = new InvoiceItem();
        $invoice_item->client_id = $_POST['client_id'];

        foreach($invoice_item->properties as $property => $values) {
            if (validate_field($_POST[$property], $values['required'], $values['validate'])) {
                $validate_form = false;
            }
        }
        
        if ($validate_form) {
            $invoice_item->create();
            $invoice_item->update_from_form();
            $invoice_item->save();
            header('Location: ' . get_site_url() . '/invoice-item/' . $invoice_item->id . '?resp=invoice-item-created');
            exit;
        }
    }

    // Update Invoice Item (edit invoice item screen)
    if ($_POST['action'] == 'update_invoice_item') {
        
        $validate_form = true;
        $invoice_item = new InvoiceItem($_POST['ii_id']);

        foreach($invoice_item->properties as $property => $values) {
            if (validate_field($_POST[$property], $values['required'], $values['validate'])) {
                $validate_form = false;
            }
        }

        if ($validate_form) {
            $invoice_item->update_from_form();
            $invoice_item->save();
            header('Location: ' . get_site_url() . '/invoice-item/' . $invoice_item->id . '?resp=invoice-item-updated');
            exit;
        }
    }

    // Delete invoice item (invoice item screen)
    if ($_GET['resp'] == 'delete_ii') {
        wp_delete_post($_GET['ii']);
    }

    // Create invoice (invoice items screen)
    if ($_POST['action'] == 'create_invoice') {
        $invoice = new Invoice();
        $invoice->create($_POST['client_id'], $_POST['date'], $_POST['terms'], $_POST['invoice_items']);

        header('Location: ' . get_site_url() . '/invoice/' . $invoice->id . '?resp=invoice-created');
            exit;
    }

    // Invoice Functions (invoices screen)
    if ($_POST['action'] == 'invoice_functions') {

        // cancel (delete) invoices
        if ($_POST['invoice_option'] == 'cancel') {
            foreach($_POST['invoices'] as $invoice_id) {
                $reinstate_ii = explode(',', get_post_meta($invoice_id, 'invoice_items', true));
                foreach ($reinstate_ii as $reinstate) {
                    update_post_meta($reinstate, 'invoice_item_status', 'unbilled');
                }
                wp_delete_post($invoice_id);
            }
        }

        if ($_POST['invoice_option'] == 'mark_paid') {
            foreach($_POST['invoices'] as $invoice_id) {
                update_post_meta($invoice_id, 'status', 'paid');
            }
        }
    }

    // Add cookie if user is logging in
    if ($_GET['key']) {
        setcookie('rb-login-key', $_GET['key'], time() + 3600);
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Location: ' . get_site_url() . '/client-welcome', TRUE, 307);
        exit;
    }

    if ($_GET['logout']) {
        setcookie("rb-login-key", "", time() - 3600); 
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Location: ' . get_site_url() . '/client-welcome', TRUE, 307);
        exit;
    }

}
add_action( 'init', 'process_and_redirect');


/**
 * Validate Field
 * 
 * Validates a single field. Returns error message if validation fails or false if validation successful.
 * 
 * @param   $input      string      The input to validate
 * @param   $required   boolean     Whether the field is required
 * @param   $validate   string      The validation type
 */

function validate_field($input, $required, $validate) {

    // check for blank required field
    if (!$input && $required) {
        return "Required Field";
    }

    // otherwise if there is input and a validation type
    else if ($input && $validate) {

        // phone
        if ($validate == 'phone') {
            if (!preg_match('/^[0-9+\s]*$/', $input)) {
                return "Numbers, Spaces and + Only";
            }
        }

        // email
        if ($validate == 'email') {
            if (!preg_match('/^\S+@\S+\.\S+$/', $input)) {
                return "Not a Valid Email Address";
            }
        }

        // number
        if ($validate == 'number') {
            if (!preg_match('/^[0-9]*$/', $input)) {
                return "Numbers Only";
            }
        }
    }

    return false;
}

/**
 * Do Input Field
 * 
 * Displays a single input field with label, and message if field has just failed validation
 * 
 * @param   $field              string      The name of the field (not the label)
 * @param   $field_properties   array       Array of field properties
 * @param   $value              string      The value to populate field with
 * @param   $input_class        string      Class name to add to the input field
 * @param   $input_id           string      id to add to the input field
 */

function do_input_field($field, $field_properties, $value, $input_class = '', $input_id = '') {
    
    // have we returned to form because validation failed
    if ($_GET['resp'] == 'f_val') {

        // check this field for validation errors and get message
        $failed_validation_message = validate_field($value, $field_properties['required'], $field_properties['validate']);
    }

    // dispay label and failed validation message if applicable
    echo '<p class="label">' . $field_properties['label'];
    if ($field_properties['required']) {
        echo'<span class="required_field">*</span>';
    }
    if ($failed_validation_message) {
        $fval_class = 'f_val';
        echo '<span class="field_val_fail">' . $failed_validation_message . '</span>';
    }
    echo '</p>';

    // display input element
    if ($field_properties['type'] == 'text') {
        echo '<input type="text" class="' . $fval_class . ' ' . $input_class . '" value="' . $value . '" name="' . $field . '" id="' . $input_id . '">';
    }
    if ($field_properties['type'] == 'textarea') {
        echo '<textarea name="' . $field . '" class="' . $fval_class . ' ' . $input_class . '" id="' . $input_id . '">' . stripslashes($value) . '</textarea>';
    }
    if ($field_properties['type'] == 'date') {
        echo '<input type="date" class="' . $fval_class . ' ' . $input_class . '" value="' . $value . '" name="' . $field . '" id="' . $input_id . '">';
    }
    if ($field_properties['type'] == 'select') {
        echo '<select name="' . $field . '" id="' . $input_id . '">';
        foreach($field_properties['options'] as $option) {
            echo '<option value="' . $option['value'] . '" ';
            if ($value == $option['value']) {
                echo 'selected';
            }
            echo '>';
            
            echo $option['label'] . '</option>';
        }
        echo '</select>';
    }
}

// register jquery

// include custom jQuery
function shapeSpace_include_custom_jquery() {

	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', array(), null, false);

}
add_action('wp_enqueue_scripts', 'shapeSpace_include_custom_jquery');

// Return human readable date
function return_human_date($date) {
    return date("j M Y", strtotime($date));
}

/**
 * Get Client
 * Returns client ID when passed a login key
 */

 function get_client($key) {

    $args = array(
        'post_type' => 'client',
        'meta_query' => array(
            array(
                'key' => 'login_key',
                'value' => $key
            )
        )
    );
    $query = new WP_Query($args);

    while ($query->have_posts()) {
        $query->the_post();
        return get_the_id();
    }    
}

/**
 * Do Client Security
 * Returns client ID if login key saved as cookie or passed as GET
 * If client not logged in and no key, display message and exit
 */

 function do_client_security() {
    if ($_GET['key']) {
        $client_id = get_client($_GET['key']);
        return $client_id;
    } else if ($_COOKIE['rb-login-key']) {
        $client_id = get_client($_COOKIE['rb-login-key']);
        return $client_id;
    } else { ?>
        <div class="rb-section-container">
            <div class="rb-container">
                <p>Welcome to the online billing platform. Please use the link we emailed you to log in. If you have lost your link please contact us to request another one.</p>
            </div>
        </div>
    <?php
        exit;
    }
 }

/**
 * Do Unauthorised Access Message
 * Displays message and exits if user tries to look at someone else's invoice or item
 */

 function do_unauthorised_access_message() { ?>
    <div class="rb-section-container">
        <div class="rb-container">
            <p>You are not authorised to view this item. Please contact us to resolve this issue.</p>
        </div>
    </div> <?php
    exit;
 }