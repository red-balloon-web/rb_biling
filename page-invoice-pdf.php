<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 
if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
}
header('Content-Type: application/pdf');

$invoice = new Invoice($_GET['id']);
$client = new Client($invoice->client_id);

require('fpdf182/fpdf.php');

// include('fpdf182/makefont/makefont.php');


$pdf = new FPDF();
$pdf->AddPage();
$pdf->AddFont('Montserrat', '', 'Montserrat-Regular.php');
$pdf->AddFont('Montserrat', 'B', 'Montserrat-Bold.php');
$pdf->SetFont('Montserrat','', 10);

// Address block
$pdf->SetY(14);
$pdf->SetTextColor(150);
$pdf->Cell(0,5,"Red Balloon Web Limited", 0, 1, 'R');
$pdf->Cell(0,5,"34 Windmill House", 0, 1, 'R');
$pdf->Cell(0,5,"Waterloo", 0, 1, 'R');
$pdf->Cell(0,5,"SE1 8LX", 0, 1, 'R');

// Logo
$pdf->SetY(20);
$pdf->SetFontSize(25);
$pdf->SetTextColor(150,0,0);
$pdf->Cell(19,0,'Red', 0, 0);
$pdf->SetTextColor(150);
$pdf->Cell(40,0,'Balloon', 0, 1);
$pdf->Cell(0,8,'',0,1);
$pdf->SetFontSize(11);
$pdf->SetTextColor(150);
$pdf->Cell(1, 0, '', 0, 0);
$pdf->Cell(0, 0, 'www.redballoonweb.com', 0, 1);

// INVOICE
$pdf->SetFontSize(16);
$pdf->SetTextColor(50);
$pdf->Cell(0, 20, '', 0, 1);
$pdf->Cell(0, 10, 'INVOICE', 0, 1, 'C');
$pdf->Cell(0, 10, '', 0, 1);

// Reference Block
$pdf->SetFontSize(10);
$pdf->SetTextColor(100);
$pdf->Cell(0, 6, $client->client_name, 0, 1);
$pdf->Cell(0, 6, return_human_date($invoice->date), 0, 1);
$pdf->Cell(0, 6, 'Ref: ' . $invoice->reference, 0, 1);
$pdf->Cell(0, 10, '', 0, 1);

// Query for Invoice Items
$query_args = array(
    'post__in' => explode( ',', $invoice->invoice_items),
    'post_type'=> 'invoice_item',
    'meta_query' => array(
        'ii_date' => array(
            'key' => 'date'
        )
    ),
    'orderby' => 'ii_date',
    'order' => 'ASC'
);
$query = new WP_Query($query_args);

// Loop and output Invoice Items
while ($query->have_posts()) {
    $query->the_post();
    $invoice_item = new InvoiceItem(get_the_ID());
    $pdf->Cell(0, 6, return_human_date($invoice_item->date), 0, 1);

    // Get the right string for the final line
    if ($invoice_item->ii_type == 'hourly') {
        $pdf->Cell(0, 6, $invoice_item->ii_title, 0, 1);
        $string = $invoice_item->hours . utf8_decode('h @ ') . $invoice_item->hourly_rate_numeric . utf8_decode('/h');
    } else if ($invoice_item->ii_type == 'fixed_rate') {
        $string = $invoice_item->ii_title;
    }
    $value_string = utf8_decode('£') . number_format($invoice_item->value, 2);

    // Display item and line
    $pdf->Cell($pdf->GetStringWidth($string), 6, $string, 0, 0);
    $line = array(
        'startX' => $pdf->getX() + 2,
        'Y' => $pdf->getY() + 4.5,
        'endX' => 197 - $pdf->GetStringWidth($value_string)
    );
    $pdf->Line($line['startX'], $line['Y'], $line['endX'], $line['Y']);
    $pdf->SetX($line['endX']);
    $pdf->Cell(0, 6, $value_string, 0, 1);
    // Spacer
    $pdf->Cell(0, 10, '', 0, 1);
}

// Spacer
$pdf->Cell(0, 10, '', 0, 1);

// Do we need a new page for the total
if ($pdf->GetY() > 223 + (297 * ($pdf->PageNo() - 1))) {
    $pdf->AddPage();
}

// Total
$pdf->SetY(233);
$pdf->SetFont('Montserrat','B', 10);
$pdf->Cell(50 ,6, 'Total', 0, 0);
$pdf->SetX(197 - $pdf->GetStringWidth(utf8_decode('£') . number_format($invoice->i_value, 2)));
$pdf->Cell(0,6, utf8_decode('£') . number_format($invoice->i_value, 2), 0, 1);
$pdf->Line(11, $pdf->GetY(), 198, $pdf->GetY());

// Details
$pdf->Cell(0, 2, '', 0, 1);
$pdf->SetFont('Montserrat', '', 10);
$pdf->Cell(0, 6, 'Terms: ' . $invoice->terms . ' days', 0, 1);
$pdf->Cell(0, 6, 'BACS Payments: 40-07-30 81568205', 0, 1);

// Spacer
$pdf->Cell(0, 10, '', 0, 1);

// Footer note
$pdf->SetFontSize(8);
$pdf->Cell(0, 4, 'Registered Office 129 Downhall Park Way, Rayleigh, Essex SS6 9TP. This invoice is not liable for VAT.', 0, 1, 'C');
$pdf->Cell(0, 4, 'Thank you for being a valued client. If you have any questions or queries please contact us on 0800 048 7608', 0, 1, 'C');



// Output
$pdf->Output('I', 'Red Balloon Invoice ' . $invoice->reference . '.pdf');

?>