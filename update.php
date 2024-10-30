<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "etreasure";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$transaction_id = $_GET['id'] ?? '';

if (empty($transaction_id)) {
    die("Transaction ID is missing");
}

$sql_transaction_info = "SELECT ttype, cashin_id, expense_id FROM transaction WHERE transaction_id = ?";
$stmt = $conn->prepare($sql_transaction_info);
$stmt->bind_param("i", $transaction_id);
$stmt->execute();
$result_transaction_info = $stmt->get_result();

if ($result_transaction_info->num_rows > 0) {
    $row_transaction_info = $result_transaction_info->fetch_assoc();
    $transaction_type = $row_transaction_info['ttype'];
    $cashin_id = $row_transaction_info['cashin_id'];
    $expense_id = $row_transaction_info['expense_id'];
} else {
    die("Transaction not found");
}

$stmt->close();
$conn->close();

if ($transaction_type === 'CASHIN' && $cashin_id !== null) {
    header("Location: /finaladms/update_cashin.php?id=$cashin_id");
    exit();
} elseif ($transaction_type === 'EXPENSE' && $expense_id !== null) {
    header("Location: /finaladms/update_expense.php?id=$expense_id");
    exit();
} else {
    die("Invalid transaction type or associated ID not found");
}
?>
