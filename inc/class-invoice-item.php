<?php

class InvoiceItem {

    // Define properties

    // Constructor function
    function __construct($id = null) {

        // if there is an id
        if ($id) {
            $this->id = $id;
            // set form fields from database
            foreach ($this->properties as $property => $values) {
                $this->{$property} = get_post_meta($this->id, $property, true);
            }
        }

        $this->invoice_item_status = get_post_meta($this->id, 'invoice_item_status', true);

    }

    // Array of form fields
    public $properties = array(
        'client_id' => array(
            'type' => 'hidden'
        ),
        'date' => array(
            'label' => 'Date',
            'type' => 'date',
            'required' => true
        ),
        'ii_type' => array(
            'label' => 'Type',
            'type' => 'select',
            'options' => array(
                array(
                    'value' => 'hourly',
                    'label' => 'Hourly'
                ),
                array(
                    'value' => 'fixed_rate',
                    'label' => 'Fixed Rate'
                ),
                array(
                    'value' => 'report',
                    'label' => 'Report'
                )
            )
        ),
        'hours' => array(
            'label' => 'Hours',
            'type' => 'text'
        ),
        'hourly_rate_text' => array(
            'label' => 'Hourly Rate',
            'type' => 'select',
            'options' => array(
                array(
                    'value' => 'standard',
                    'label' => 'Standard'
                ),
                array(
                    'value' => 'free',
                    'label' => 'Free'
                )
            )
        ),
        'hourly_rate_numeric' => array(
            'label' => '&nbsp;',
            'type' => 'text'
        ),
        'value' => array(
            'label' => 'Value',
            'type' => 'text',
            // 'required' => true
        ),
        'ii_title' => array(
            'label' => 'Title',
            'type' => 'text',
            'required' => true
        ),
        'ii_project' => array(
            'label' => 'Project / Website',
            'type' => 'text'
        ),
        'client_notes' => array(
            'label' => 'Client Notes',
            'type' => 'textarea'
        ),
        'technical_notes' => array(
            'label' => 'Technical Notes',
            'type' => 'textarea'
        )
    );

    // Create
    public function create() {
        $new_invoice_item_args = array(
            'post_type' => 'invoice_item',
            'post_status' => 'publish',
            'supports' => array(
                'custom-fields',
                'excerpt'
            )
        );
        $this->id = wp_insert_post($new_invoice_item_args);
        if ($_POST['ii_type'] != 'report') {
            update_post_meta($this->id, 'invoice_item_status', 'unbilled');
        } else {
            update_post_meta($this->id, 'invoice_item_status', 'report');
        }
    }

    // Function to display field
    public function do_input_field($field, $input_class = '', $input_id = '') {
        do_input_field($field, $this->properties[$field], $this->$field, $input_class, $input_id);
    }

    // Update from form
    public function update_from_form() {
        foreach ($this->properties as $property => $values) {

            if (isset($_POST[$property])) {
                $this->$property = $_POST[$property];
            } else {
                $this->$property = null;
            }

        }
    }

    // Save
    public function save() {
        foreach ($this->properties as $property => $values) {
            update_post_meta($this->id, $property, $this->{$property});
        }
    }

    // Display ii type
    public function display_ii_type() {
        if ($this->ii_type == 'hourly') {
            return 'Hourly';
        }
        if ($this->ii_type == 'fixed_rate') {
            return 'Fixed Rate';
        }
        if ($this->ii_type == 'report') {
            return 'Report';
        }
    }

    // Return ii status
    // Returns 'Unbilled' in red or invoice ID with link
    public function display_ii_status($client = false) {
        if ($this->invoice_item_status == 'unbilled') {
            return '<span class="red">Unbilled</span>';
        } else if ($this->invoice_item_status == 'report') {
            return 'Report';
        } else {
            if ($client = false) {
                return '<a href="' . get_site_url() . '/invoice/' . $this->invoice_item_status . '">' . get_post_meta($this->invoice_item_status, 'reference', true) . '</a>';
            } else {
                return '<a href="' . get_site_url() . '/client-single-invoice?id=' . $this->invoice_item_status . '">' . get_post_meta($this->invoice_item_status, 'reference', true) . '</a>';
            }
        }
    }

}