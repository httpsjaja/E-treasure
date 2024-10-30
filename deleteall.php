<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "etreasure";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_delete_transactions = "DELETE FROM transaction";
if ($conn->query($sql_delete_transactions) === TRUE) {
    echo "All transactions deleted successfully.<br>";
} else {
    echo "Error deleting transactions: " . $conn->error;
}

$sql_delete_cashin = "DELETE FROM cash_in";
if ($conn->query($sql_delete_cashin) === TRUE) {
    echo "All cash in records deleted successfully.<br>";
} else {
    echo "Error deleting cash in records: " . $conn->error;
}

$sql_delete_expense = "DELETE FROM expense";
if ($conn->query($sql_delete_expense) === TRUE) {
    echo "All expense records deleted successfully.<br>";
} else {
    echo "Error deleting expense records: " . $conn->error;
}

$sql_reset_auto_increment = "ALTER TABLE transaction AUTO_INCREMENT = 1;
                             ALTER TABLE cash_in AUTO_INCREMENT = 1;
                             ALTER TABLE expense AUTO_INCREMENT = 1;";
if ($conn->multi_query($sql_reset_auto_increment) === TRUE) {
    echo "Auto increment reset successfully.<br>";
} else {
    echo "Error resetting auto increment: " . $conn->error;
}

$conn->close();

header("location: /finaladms/dashboard.php");
exit();
?>
