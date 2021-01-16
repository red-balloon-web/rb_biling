<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} ?>

<?php get_header(); ?>

<div class="rb-section-container pageheader rb-pb-0">
    <div class="rb-container">
        <h1>Dashboard<span class="headerbuttons"></span></h1>
    </div>
</div>

<div class="rb-section-container dashboard-top">
    <div class="rb-container">
        <div class="left">
            <table class="secondary-table">
                <tr>
                    <td colspan="2"><strong>Due and Unbilled</strong></td>
                </tr>
                <tr>
                    <td>Unbilled Work:</td>
                    <td class="rb-right"><strong>£360</strong></td>
                </tr>
                <tr>
                    <td>Invoices Due:</td>
                    <td class="rb-right"><strong>£1265</strong></td>
                </tr>
                <tr>
                    <td>Invoices Overdue:</td>
                    <td class="rb-right"><strong>£0</strong></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>3</strong> invoices due within 1 week</td>
                </tr>
            </table>
        </div>
        <div class="center">
            <table class="secondary-table">
                <tr>
                    <td colspan="2"><strong>Invoices Paid</strong></td>
                </tr>
                <tr>
                    <td>May 2021:</td>
                    <td class="rb-right"><strong>£2360</strong></td>
                </tr>
                <tr>
                    <td>April 2021:</td>
                    <td class="rb-right"><strong>£1450</strong></td>
                </tr>
                <tr>
                    <td>March 2021:</td>
                    <td class="rb-right"><strong>£2060</strong></td>
                </tr>
            </table>
        </div>
        <div class="right">
            <table class="secondary-table">
                <tr>
                    <td colspan="2" class="rb-right"><a href="">Clients</a></td>
                </tr>
                <tr>
                    <td colspan="2" class="rb-right"><a href="">Invoices</a></td>
                </tr>
                <tr>
                    <td colspan="2" class="rb-right"><a href="">Invoice Items</a></td>
                </tr>
                <tr>
                    <td colspan="2" class="rb-right"><a href="">Set Hourly Rates</a></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="rb-section-container dash-invoices">
    <div class="rb-container">
        <h2 class="rb-left">Invoices Due</h2>
        <table class="primary-table">
            <thead>
                <tr>
                    <td class="rb-table-left">Client</td>
                    <td>Invoice</td>
                    <td>Date Due</td>
                    <td>Value</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="rb-table-left"><a href="">Baba's Fisheries</a></td>
                    <td><a href="">BAB050621</a></td>
                    <td>5 July 2021</td>
                    <td>£160</td>
                </tr>
                <tr>
                    <td class="rb-table-left"><a href="">Learners Progress College</a></td>
                    <td><a href="">LPC050621</a></td>
                    <td>5 July 2021</td>
                    <td>£240</td>
                </tr>
                <tr>
                    <td class="rb-table-left"><a href="">St George the Martyr</a></td>
                    <td><a href="">STG050621</a></td>
                    <td>5 July 2021</td>
                    <td>£60</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="rb-section-container dash-unbilled">
    <div class="rb-container">
        <h2 class="rb-left">Unbilled Work</h2>
        <table class="primary-table">
            <thead>
                <tr>
                    <td class="rb-table-left">Client</td>
                    <td>Invoice Item</td>
                    <td>Date</td>
                    <td>Value</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="rb-table-left"><a href="">Baba's Fisheries</a></td>
                    <td><a href="">Server Migration</a></td>
                    <td>5 July 2021</td>
                    <td>£160</td>
                </tr>
                <tr>
                    <td class="rb-table-left"><a href="">Learners Progress College</a></td>
                    <td><a href="">Monthly Hosting Charge</a></td>
                    <td>5 July 2021</td>
                    <td>£240</td>
                </tr>
                <tr>
                    <td class="rb-table-left"><a href="">St George the Martyr</a></td>
                    <td><a href="">Website Update</a></td>
                    <td>5 July 2021</td>
                    <td>£60</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php get_footer(); ?>