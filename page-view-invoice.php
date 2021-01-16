<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} ?>

<?php get_header(); ?>

<div class="rb-section-container pageheader rb-pb-0">
    <div class="rb-container">
        <p class="back-to"><a href=""><< Back to Baba's Fisheries</a></p>
        <h1 class="subtitled rb-left">Baba's Fisheries<span class="headerbuttons"><a href="Create New" class="button">Generate PDF</a><a href="Create New" class="button">Email to Client</a></h1>
        <h2 class="subtitle">View Invoice</h2>
        <p>Ref: BABWR230621</p>
        <p>Sent: 23/06/21</p>
        <p>Status: Due</p>
    </div>
</div>

<hr>

<div class="rb-section-container invoice-report rb-pt-0 rb-pb-0">
    <div class="rb-container">
        <div class="logo-address">
            <div class="logo"><span class="red">Red</span> Balloon<span class="url">www.redballoonweb.com</span></div>
            <div class="address">Red Balloon Web Limited<br>
                Unit B<br>
                Office 36<br>
                Hatton Garden<br>
                London EC1</div>
        </div>
        <div class="invoice-header">
            <h2>INVOICE</h2>
        </div>
        <div class="report-summary">
            <p>Baba's Fisheries<br>
            26 June 2021<br>
            Ref: BAB26021</p>
        </div>
        <div class="invoice-item">
            <p>6 June 2021<br>
            Server migration</p>
            <div class="priceline">
                <p>3h @ £20/h</p>
                <div class="underline"></div>
                <p>£60</p>
            </div>
        </div>
        <div class="invoice-item">
            <p>10 June 2021</p>
            <div class="priceline">
                <p>Monthly Hosting Fee</p>
                <div class="underline"></div>
                <p>£40</p>
            </div>
        </div>
        <div class="invoice-item">
            <p>16 June 2021<br>
            Website Update</p>
            <div class="priceline">
                <p>2h @ £20/h</p>
                <div class="underline"></div>
                <p>£40</p>
            </div>
        </div>
        <div class="invoice-item total">
            <div class="priceline">
                <p>Total</p>
                <p>£140</p>
            </div>
        </div>
        <div class="invoice-terms">
            <p>Terms: 30 days</p>
            <p>BACS Payments: 40-07-30 81568205</p>
        </div>
        <div class="invoice-thankyou">
            <p>Thank you for being a valued client. If you have any questions or queries please contact me at chris@redballoonweb or on 07786 562 022</p>
        </div>
    </div>
</div>

<?php get_footer(); ?>