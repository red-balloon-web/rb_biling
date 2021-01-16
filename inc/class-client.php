<?php

class Client {

    // Define properties

    // Constructor function
    function __construct($id = null) {

        // if there is an id
        if ($id) {
            $this->id = $id;
            // set form fields from database
            foreach ($this->properties as $property => $values) {
                $this->$property = get_post_meta($this->id, $property, true);
            }
            $this->login_key = get_post_meta($this->id, 'login_key', true);
        }

    }

    // Array of form fields
    public $properties = array(
        'client_name' => array(
            'label' => 'Name',
            'type' => 'text',
            'required' => true,
            'validate' => '',
            'default' => ''
        ),
        'contact_person' => array(
            'label' => 'Contact Person',
            'type' => 'text',
        ),
        'phone' => array(
            'label' => 'Phone',
            'type' => 'text',
            'validate' => 'phone'
        ),
        'address' => array(
            'label' => 'Address',
            'type' => 'textarea'
        ),
        'website' => array(
            'label' => 'Website',
            'type' => 'text'
        ),
        'email' => array(
            'label' => 'Billing Email',
            'type' => 'text',
            'validate' => 'email'
        ),
        'prefix' => array(
            'label' => 'Invoice Prefix',
            'type' => 'text',
            'required' => true
        ),
        'uninvoiced_limit' => array(
            'label' => 'Uninvoiced Limit',
            'type' => 'text',
            'required' => true,
            'validate' => 'number'
        )
    );

    // Create
    public function create() {
        $new_client_args = array(
            'post_type' => 'client',
            'post_status' => 'publish',
            'supports' => array(
                'custom-fields',
                'excerpt'
            )
        );
        $this->id = wp_insert_post($new_client_args);
        update_post_meta($this->id, 'client_status', 'current');
        update_post_meta($this->id, 'most_recent', 1);
    }

    // Function to display field
    public function do_input_field($field) {
        do_input_field($field, $this->properties[$field], $this->$field);
    }

    // Update from form
    public function update_from_form() {
        foreach ($this->properties as $property => $values) {

            if (isset($_POST[$property])) {
                $this->{$property} = $_POST[$property];
            } else {
                $this->{$property} = null;
            }

        }
    }

    // Save
    public function save() {
        foreach ($this->properties as $property => $values) {
            update_post_meta($this->id, $property, $this->{$property});
        }
        update_post_meta($this->id, 'login_key', $this->login_key);
    }

    // Calculate Unbilled
    // returns value of all unbilled invoice items
    public function calculate_unbilled() {

        // query database for this client's work items
        $query_args = array(
            'post_type' => 'invoice_item',
            'meta_query' => array(
                array(
                    'key' => 'client_id',
                    'value' => $this->id
                ),
                array(
                    'key' => 'invoice_item_status',
                    'value' => 'unbilled'
                )
            )
        );
        $query = new WP_Query($query_args);

        // initiate total variable and loop through query object
        $total = 0;
        while($query->have_posts()) {
            $query->the_post();
            $invoice_item = new InvoiceItem(get_the_id());
            $total += $invoice_item->value;
        }
        update_post_meta($this->id, 'total_unbilled', $total);
        return $total;
    }

    // Return Most Recent
    // return array of data for most recent invoice item
    public function return_most_recent() {

        // query database to return just the most recent work item
        $query_args = array(
            'post_type' => 'invoice_item',
            'meta_query' => array(
                array(
                    'key' => 'client_id',
                    'value' => $this->id
                ),
                'ii_date' => array(
                    'key' => 'date',
                )
            ),
            'orderby' => 'ii_date',
            'order' => 'DSC',
            'posts_per_page' => 1
        );
        $query = new WP_Query($query_args);

        // populate return array and return
        while ($query->have_posts()) {
            $query->the_post();
            $invoice_item = new InvoiceItem(get_the_id());
            update_post_meta($this->id, 'most_recent', strtotime($invoice_item->date));
            $return = array(
                'id' => get_the_id(),
                'permalink' => get_the_permalink(),
                'value' => $invoice_item->value,
                'title' => $invoice_item->ii_title,
                'date' => $invoice_item->date,
            );
            return $return;
        }

    }

    // Calculate total due
    // Return total of all invoices marked due
    public function get_total_due() {
        
        // query database to retrieve invoices marked due
        $query_args = array(
            'post_type' => 'invoice',
            'meta_query' => array(
                array(
                    'key' => 'client_id',
                    'value' => $this->id
                ),
                array(
                    'key' => 'status',
                    'value' => 'due'
                )
            ),
            'posts_per_page' => -1
        );
        $query = new WP_Query($query_args);

        // calculate total due invoices value
        $running_total = 0;
        while ($query->have_posts()) {
            $query->the_post();
            $running_total += get_post_meta(get_the_id(), 'i_value', true);
        }

        update_post_meta($this->id, 'total_due', $running_total);

        return $running_total;
    }


}