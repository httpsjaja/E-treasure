<?php
require('./fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',12);
        $this->Cell(0,10,'Bolbok Integrated National High School',0,'','C');
        $this->Ln(5);
        $this->SetFont('Arial','',12);
        $this->Cell(0,10,'IV-A CALABARZON',0,'','C');
        $this->Ln(5);
        $this->Cell(0,10,'Brgy. Bolbok, Lipa City, Batangas',0,'','C');
        $this->Ln(20);
    }
}

$pdf = new PDF('P', 'mm', array(210, 279));
$pdf->AddPage();

$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'FINANCIAL REPORT S.Y. ' . (date('Y') - 1) . '-' . date('Y'),0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'CASH-IN TRANSACTIONS',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,10,'Date (D/M/Y)',1,0,'C');
$pdf->Cell(120,10,'Description',1,0,'C');
$pdf->Cell(30,10,'Amount',1,1,'C');

$conn = new mysqli("localhost", "root", "", "etreasure");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT cdate, cdescription, camount FROM cash_in ORDER BY STR_TO_DATE(cdate, '%d-%m-%Y') ASC";
$result = $conn->query($sql);
$prevDate = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['cdate'] != $prevDate) {
            $pdf->Cell(40,10,$row['cdate'],1,0,'C');
        } else {
            $pdf->Cell(40,10,'','',0,'C');
        }
        $pdf->Cell(120,10,$row['cdescription'],1,0,'L');
        $pdf->Cell(30,10,($row['camount']),1,1,'R');
        $prevDate = $row['cdate'];
    }
}

$sql = "SELECT SUM(camount) AS total_cash_in FROM cash_in";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalCashIn = $row['total_cash_in'];
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(160,10,'Total Cash-ins: ',1,0,'R');
    $pdf->Cell(30,10, ($totalCashIn),1,1,'R');
}

$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'EXPENSE TRANSACTIONS',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,10,'Date (D/M/Y)',1,0,'C');
$pdf->Cell(120,10,'Description',1,0,'C');
$pdf->Cell(30,10,'Amount',1,1,'C');

$sql = "SELECT edate, edescription, eamount FROM expense ORDER BY STR_TO_DATE(edate, '%d-%m-%Y') ASC";
$result = $conn->query($sql);
$prevDate = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['edate'] != $prevDate) {
            $pdf->Cell(40,10,$row['edate'],1,0,'C');
        } else {
            $pdf->Cell(40,10,'','',0,'C');
        }
        $pdf->Cell(120,10,$row['edescription'],1,0,'L');
        $pdf->Cell(30,10,($row['eamount']),1,1,'R');
        $prevDate = $row['edate'];
    }
}

$sql = "SELECT SUM(eamount) AS total_expenses FROM expense";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalExpenses = $row['total_expenses'];
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(160,10,'Total Expenses: ',1,0,'R');
    $pdf->Cell(30,10,($totalExpenses),1,1,'R');
}

$conn->close();

$pdf->Ln(10);

$totalAmount = $totalCashIn + $totalExpenses;
$pdf->Cell(130,10,'Total Cash in hand as of ' . date("F d, Y") . ': ',1,'0','R');
$pdf->Cell(60,10,($totalAmount),1,1,'R');

$pdf->AddPage();
$pdf->Ln(30);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,0,'________________________',0,0,'C');
$pdf->Ln(1);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'ELEONOR F. LICAUAN',0,0,'C','');
$pdf->Ln(5);
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,10,'SPTA Treasurer',0,0,'C');

$pdf->Ln(25);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,0,'________________________',0,0,'C');
$pdf->Ln(1);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'MARK JOHN FETALCORIN',0,0,'C','');
$pdf->Ln(5);
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,10,'SPTA Auditor',0,0,'C');

$pdf->Ln(25);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,0,'________________________',0,0,'C');
$pdf->Ln(1);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'JAMES N. KEELER',0,0,'C','');
$pdf->Ln(5);
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,10,'SPTA President',0,0,'C');

$pdf->Ln(25);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,0,'________________________',0,0,'C');
$pdf->Ln(1);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'ALVIN J. SABIDO',0,0,'C','');
$pdf->Ln(5);
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,10,'Principal II',0,0,'C');

$pdf->Output();
?>
