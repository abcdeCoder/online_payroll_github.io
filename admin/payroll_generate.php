<?php
include 'includes/session.php';

function generateRow($from, $to, $conn, $deduction)
{
    $contents = '';
    $rsql = 'SELECT * FROM nOkcCY6dDe.position';
    $rquery = $conn->query($rsql);
    $rrow = $rquery->fetch_assoc();

    $sql = "SELECT SUM(num_hr) AS total_hr, attendance.employee_id AS empid , employees.firstname as firstname , employees.lastname as lastname FROM attendance LEFT JOIN employees ON employees.id=attendance.employee_id LEFT JOIN position ON position.id=employees.position_id WHERE date BETWEEN '$from' AND '$to' GROUP BY attendance.employee_id ORDER BY employees.firstname ASC, employees.lastname ASC";

    $query = $conn->query($sql);
    $total = 0;
    while ($row = $query->fetch_assoc()) {
        $empid = $row['empid'];

        $casql = "SELECT SUM(amount) AS cashamount FROM cashadvance WHERE employee_id='$empid' AND date_advance BETWEEN '$from' AND '$to'";

        $caquery = $conn->query($casql);
        $carow = $caquery->fetch_assoc();
        $cashadvance = $carow['cashamount'];

        $gross = $rrow['rate'] * $row['total_hr'];
        $total_deduction = $deduction + $cashadvance;
        $net = $gross - $total_deduction;

        $total += $net;
        $contents .=
            '
			<tr>
				<td>' .
            $row['firstname'] .
            ', ' .
            $row['lastname'] .
            '</td>
				<td>' .
            $row['empid'] .
            '</td>
				<td align="right">' .
            number_format($net, 2) .
            '</td>
			</tr>
			';
    }

    $contents .=
        '
			<tr>
				<td colspan="2" align="right"><b>Total</b></td>
				<td align="right"><b>' .
        number_format($total, 2) .
        '</b></td>
			</tr>
		';
    return $contents;
}

$range = $_POST['date_range'];
$ex = explode(' - ', $range);
$from = date('Y-m-d');
$to = date('Y-m-d');

$sql = 'SELECT SUM(amount) as total_amount FROM deductions';
$query = $conn->query($sql);
$drow = $query->fetch_assoc();
$deduction = $drow['total_amount'];

$from_title = date('M d, Y', strtotime($ex[0]));
$to_title = date('M d, Y', strtotime($ex[1]));

require_once '../tcpdf/tcpdf.php';
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Payroll: ' . $from_title . ' - ' . $to_title);
$pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
$pdf->SetDefaultMonospacedFont('helvetica');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(true, 10);
$pdf->SetFont('helvetica', '', 11);
$pdf->AddPage();
$content = '';
$content .=
    '
      	<h2 align="center">TechSoft IT Solutions</h2>
      	<h4 align="center">' .
    $from_title .
    ' - ' .
    $to_title .
    '</h4>
      	<table border="1" cellspacing="0" cellpadding="3">  
           <tr>  
           		<th width="40%" align="center"><b>Employee Name</b></th>
                <th width="30%" align="center"><b>Employee ID</b></th>
				<th width="30%" align="center"><b>Net Pay</b></th> 
           </tr>  
      ';
$content .= generateRow($from, $to, $conn, $deduction);
$content .= '</table>';
$pdf->writeHTML($content);
ob_end_clean();
$pdf->Output($_SERVER['DOCUMENT_ROOT'] . 'payroll.pdf', 'I');

?>