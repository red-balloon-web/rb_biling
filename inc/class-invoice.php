<?php

class Invoice {

    // Constructor function
    function __construct($id = null) {
        
        // Get properties if there is an id
        if ($id) {
            $this->id = $id;
            $this->client_id = get_post_meta($this->id, 'client_id', true);
            $this->date = get_post_meta($this->id, 'date', true);
            $this->reference = get_post_meta($this->id, 'reference', true);
            $this->i_value = get_post_meta($this->id, 'i_value', true);
            $this->status = get_post_meta($this->id, 'status', true);
            $this->terms = get_post_meta($this->id, 'terms', true);
            $this->invoice_items = get_post_meta($this->id, 'invoice_items', true);
            $this->due_date = get_post_meta($this->id, 'due_date', true);
        }
    }

    /**
     * Create
     * Creates a new invoice
     * 
     * @param   $client_id      integer     Client ID
     * @param   $date           date        Date
     * @param   $terms          integer     Days for payment
     * @param   $invoice_items  array       Invoice Items to add to invoice
     */

    public function create($client_id, $date, $terms, $invoice_items) {

        // create new post
        $new_invoice_args = array(
            'post_type' => 'invoice',
            'post_status' => 'publish',
            'supports' => array(
                'custom-fields'
            )
        );
        $this->id = wp_insert_post($new_invoice_args);

        // set simple meta
        update_post_meta($this->id, 'client_id', $client_id);
        update_post_meta($this->id, 'date', $date);
        update_post_meta($this->id, 'status', 'due');
        update_post_meta($this->id, 'terms', $terms);
        update_post_meta($this->id, 'invoice_items', implode(',', $invoice_items));

        // create reference number
        $prefix = get_post_meta($client_id, 'prefix', true);
        $datestring = date('jmy', strtotime($date));
        $reference = $prefix . $datestring;
        update_post_meta($this->id, 'reference', $reference);

        // calculate value and save
        $i_value = 0;
        foreach($invoice_items as $invoice_item_id) {
            $i_value += get_post_meta($invoice_item_id, 'value', true);
        }
        update_post_meta($this->id, 'i_value', $i_value);

        // calculate due date and save
        $due_date = date('Y-m-d', strtotime($date . ' + ' . $terms . ' days'));
        update_post_meta($this->id, 'due_date', $due_date);

        // mark all invoice items as invoiced
        foreach($invoice_items as $invoice_item_id) {
            update_post_meta($invoice_item_id, 'invoice_item_status', $this->id);
        }
    }

    public function display_invoice_status() {
        if ($this->status == 'due') {
            return '<span class="red">Due</span>';
        } else if ($this->status == 'paid') {
            return 'Paid';
        } else {
            return 'unkown';
        }
    }

}