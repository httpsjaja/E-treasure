<?php
$servername = "localhost";
$username = "root";
$password = ""; // Replace 'your_password' with your actual password
$dbname = "etreasure";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];

$sql_delete_transactions = "DELETE FROM transaction WHERE expense_id = $id";
$conn->query($sql_delete_transactions);

$sql_delete_expense = "DELETE FROM expense WHERE expense_id = $id";

if ($conn->query($sql_delete_expense) === TRUE) {
    echo "Record deleted successfully";
} else {
    echo "Error deleting record: " . $conn->error;
}
header("location: /finaladms/dashboard.php");
exit;
?>
