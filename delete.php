<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "etreasure";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$transaction_id = $_GET['id'];

$sql = "DELETE t, ci, e 
        FROM transaction t
        LEFT JOIN cash_in ci ON t.cashin_id = ci.cashin_id
        LEFT JOIN expense e ON t.expense_id = e.expense_id
        WHERE t.transaction_id = $transaction_id";

if ($conn->query($sql) === TRUE) {
    echo "Record deleted successfully";
} else {
    echo "Error deleting record: " . $conn->error;
}

header("location: /finaladms/dashboard.php");
exit();
?>
