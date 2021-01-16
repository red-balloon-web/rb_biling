<?php 
/*ini_set('display_startup_errors',1);
ini_set('display_errors',1);*/
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 
if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
}
header('Content-Type: application/pdf');


$invoice_item = new InvoiceItem($_GET['id']);

$client = new Client($invoice_item->client_id);

require('fpdf182/fpdf.php');

// include('fpdf182/makefont/makefont.php');


$pdf = new FPDF();
$pdf->AddPage();
$pdf->AddFont('Montserrat', '', 'Montserrat-Regular.php');
$pdf->AddFont('Montserrat', 'B', 'Montserrat-Bold.php');
$pdf->AddFont('Montserrat-SemiBold', '', 'Montserrat-SemiBold.php');
$pdf->AddFont('Montserrat-Light', '', 'Montserrat-Light.php');
$pdf->AddFont('Montserrat-ExtraLight', '', 'Montserrat-ExtraLight.php');
$pdf->SetFont('Montserrat','', 10);

// Address block
$pdf->SetY(14);
$pdf->SetTextColor(150);
$pdf->Cell(0,5,"Red Balloon Web Limited", 0, 1, 'R');
$pdf->Cell(0,5,"34 Windmill House", 0, 1, 'R');
$pdf->Cell(0,5,"Waterloo", 0, 1, 'R');
$pdf->Cell(0,5,"SE1 8LX", 0, 1, 'R');

// Logo
$pdf->SetY(19);
$pdf->SetFontSize(25);
$pdf->SetTextColor(150,0,0);
$pdf->Cell(19,0,'Red', 0, 0);
$pdf->SetTextColor(150);
$pdf->SetFont('Montserrat-ExtraLight');
$pdf->Cell(40,0,'Balloon', 0, 1);
$pdf->Cell(0,8,'',0,1);
$pdf->SetFontSize(11);
$pdf->SetTextColor(150);
$pdf->SetFont('Montserrat-Light');
$pdf->Cell(1.5, 0, '', 0, 0);
$pdf->Cell(0, 0, 'www.redballoonweb.com', 0, 1);

// Heading
$pdf->SetFont('Montserrat');
$pdf->SetFontSize(16);
$pdf->SetTextColor(50);
$pdf->Cell(0, 20, '', 0, 1);
if ($invoice_item->ii_type == 'hourly') {
    $heading = 'Hourly Work Report';
} else if ($invoice_item->ii_type == 'fixed_rate') {
    $heading = 'Work Item';
} else if ($invoice_item->ii_type == 'report') {
    $heading = 'Work Report';
}
$pdf->Cell(0, 8, $heading, 0, 1, 'C');
$pdf->SetTextColor(150);
$pdf->SetFontSize(10);
//$pdf->Cell(0, 6, 'This is not an invoice', 0, 1, 'C');

// Spacer
$pdf->Cell(0, 15, '', 0, 1);


// Reference Block
$pdf->SetFont("Montserrat-SemiBold");
$pdf->SetFontSize(10);
$pdf->SetTextColor(100);
$pdf->Cell(0, 6, $client->client_name, 0, 1);
$pdf->Cell(0, 6, return_human_date($invoice_item->date), 0, 1);
// $pdf->Cell(0, 6, 'Ref: ' . $invoice->reference, 0, 1);
if ($invoice_item->ii_type != 'report') {
    $pdf->Cell(0, 6, '', 0, 1);
    $pdf->SetFont('Montserrat');
    if ($invoice_item->ii_type == 'hourly') {
        $pdf->Cell(0, 6, 'Billable hours: ' . $invoice_item->hours, 0, 1);
        $pdf->Cell(0, 6, utf8_decode('Hourly rate: £' . $invoice_item->hourly_rate_numeric . '/h'), 0, 1); 
    } else {
        $pdf->Cell(0, 6, 'Type: ' . $invoice_item->display_ii_type(), 0, 1);
    }
    $pdf->SetFont("Montserrat-SemiBold");
    $pdf->Cell(0, 6, utf8_decode('To invoice: £' . $invoice_item->value), 0, 1);
}

// Spacer
$pdf->Cell(0, 15, '', 0, 1);

// II Title and Project
$pdf->SetFont('Montserrat', 'B', 12);
$pdf->Cell(0, 6, $invoice_item->ii_title, 0, 1);
$pdf->SetFont('Montserrat', '', 10);
$pdf->SetTextColor(100);
$pdf->Cell(0, 6, $invoice_item->ii_project, 0, 1);

// Spacer
$pdf->Cell(0, 10, '', 0, 1);

// Client Notes
$pdf->SetTextColor(50);
$pdf->SetFont('Montserrat-SemiBold', '');
$pdf->Cell(0, 6, 'Client Notes', 0, 1);
$pdf->SetFont('Montserrat');
if ($invoice_item->client_notes) {
    $pdf->MultiCell(0, 6, $invoice_item->client_notes, 0, 1);
} else {
    $pdf->Cell(0, 6, 'n/a', 0, 1);
}

// Spacer
$pdf->Cell(0, 10, '', 0, 1);

// Tech Notes
$pdf->SetFont('Montserrat-SemiBold', '');
$pdf->Cell(0, 6, 'Technical Notes', 0, 1);
$pdf->SetFont('Montserrat');
if ($invoice_item->technical_notes) {
    $pdf->MultiCell(0, 6, $invoice_item->technical_notes, 0, 1);
} else {
    $pdf->Cell(0, 6, 'n/a', 0, 1);
}

// Do we need a new page for the footer notes
if ($pdf->GetY() > 252 + (297 * ($pdf->PageNo() - 1))) {
    $pdf->AddPage();
}


// Footer Note
$pdf->SetY(262);
$pdf->SetFontSize(8);
if ($invoice_item->ii_type != 'report') {
    $pdf->Cell(0, 4, 'This item will be invoiced separately. If you are an account client you will recieve an invoice according to your billing schedule.', 0, 1, 'C');
} else {
    $pdf->Cell(0, 4, '', 0, 1);
}
$pdf->Cell(0, 4, 'Thank you for being a valued client. If you have any questions or queries please contact us on 0800 048 7608.', 0, 1, 'C');
$pdf->Cell(0, 4, 'Registered Office 129 Downhall Park Way, Rayleigh, Essex SS6 9TP', 0, 1, 'C');

// Output
$pdf->Output('I',  $invoice_item->ii_title . ' ' . return_human_date($invoice_item->date) . '.pdf');

?>