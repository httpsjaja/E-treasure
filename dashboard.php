<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .container {
            width: 85%;
            max-width: none;
        }
    </style>
    <title>E-TREASURE</title>
</head>
<body>
    <div class="container my-5">
        <h2>E-TREASURE</h2>
        <div class="row">
            <div class="col-sm-7">
                <a class="btn btn-primary" href="cashin.php" role="button">ADD CASH IN</a>
                <a class="btn btn-primary" href="expense.php" role="button">ADD EXPENSES</a>
                <a class="btn btn-primary" href="report.html" target="_blank" role="button">GENERATE REPORT</a>
                <a class="btn btn-primary btn-danger" href="#" onclick="confirmDelete()">DELETE ALL</a>
            </div>
            <div class="col-md-3">
                <h4>Total Amount: ₱<?php echo calculateTotalAmount(); ?></h4>
            </div>
            <br>
            <br>
            <div class="col-sm-6">
                <a class="btn btn-primary" href="dashboard.php" role="button">REFRESH</a>
                <a class="btn btn-primary" href="#" role="button" onclick="showAbout()">ABOUT</a>
                <a class="btn btn-primary btn-danger" href="#" role="button" onclick="confirmLogout()">LOGOUT</a>
            </div>
            <div id="aboutPopup" class="modal fade" tabindex="-1" aria-labelledby="aboutPopupLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="aboutPopupLabel">About E-TREASURE</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Developed by IT students from BatStateU-TNEU Lipa, Lawrence Licauan, Jericho Banawa, and Janna Angeles, E-TREASURE redefines financial management for the digital age.</p>
                            <p>With a focus on Sustainable Development Goals (SDGs) 9 and 12, E-TREASURE merges innovation and sustainability. Through features like automated expense management and cash flow tracking, it simplifies financial operations, ensuring efficiency and precision.</p>
                            <p>By transitioning from traditional to electronic processes, E-TREASURE minimizes environmental impact while maximizing convenience and accuracy. Join us in shaping the future of financial management with E-TREASURE.</p>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function confirmDelete() {
                    if (confirm("Are you sure you want to delete all records?")) {
                        document.getElementById('deleteAllForm').submit();
                    }
                }
                function showAbout() {
                    var aboutPopup = new bootstrap.Modal(document.getElementById('aboutPopup'));
                    aboutPopup.show();
                }
                function confirmLogout() {
                    if (confirm("Are you sure you want to logout?")) {
                        window.location.href = "login.php";
                    }
                }
            </script>
            <form id="deleteAllForm" method="post" action="deleteall.php" style="display: none;">
                <input type="hidden" name="confirmDelete" value="true">
            </form>
            <br>
            <br>
            <div>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Enter desired date (dd-mm-yyyy)" name="desired_date">
                        <button class="btn btn-primary" type="submit">Filter</button>
                    </div>
                </form>
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "etreasure";
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $desired_date = $_POST["desired_date"];
                    $sql_cash_in = "SELECT * FROM cash_in WHERE cdate = ?";
                    $stmt_cash_in = $conn->prepare($sql_cash_in);
                    $stmt_cash_in->bind_param("s", $desired_date);
                    $stmt_cash_in->execute();

                    $result_cash_in = $stmt_cash_in->get_result();
                    $sql_expense = "SELECT * FROM expense WHERE edate = ?";
                    $stmt_expense = $conn->prepare($sql_expense);
                    $stmt_expense->bind_param("s", $desired_date);
                    $stmt_expense->execute();

                    $result_expense = $stmt_expense->get_result();
                    
                    echo "<div class='transaction-box' id='transactionBox'>";
                    echo "<h5>CASH INS (Filtered)</h5>";
                    if ($result_cash_in->num_rows > 0) {
                        echo "<table class='table table-bordered'>";
                        echo "<thead><tr><th>Date (D/M/Y)</th><th>Description</th><th>Amount</th><th>Action</th></tr></thead>";
                        echo "<tbody>";
                        while ($row_cash_in = $result_cash_in->fetch_assoc()) {
                            echo "<tr><td>{$row_cash_in['cdate']}</td><td>{$row_cash_in['cdescription']}</td><td>{$row_cash_in['camount']}</td><td><a class='btn btn-primary btn-sm' href='/finaladms/update_cashin.php?id={$row_cash_in['cashin_id']}'>Update</a> <a class='btn btn-primary btn-sm btn-danger' href='/finaladms/delete_cashin.php?id={$row_cash_in['cashin_id']}' onclick='return confirm(\"Are you sure you want to delete this cash-in transaction?\")'>Delete</a></td></tr>";
                        }
                        echo "</tbody>";
                        echo "</table>";
                    } else {
                        echo "<p>No cash in records found for the specified date.</p>";
                    }
                    echo "<h5>EXPENSES (Filtered)</h5>";
                    if ($result_expense->num_rows > 0) {
                        echo "<table class='table table-bordered'>";
                        echo "<thead><tr><th>Date (D/M/Y)</th><th>Description</th><th>Amount</th><th>Action</th></tr></thead>";
                        echo "<tbody>";
                        while ($row_expense = $result_expense->fetch_assoc()) {
                            echo "<tr><td>{$row_expense['edate']}</td><td>{$row_expense['edescription']}</td><td>{$row_expense['eamount']}</td><td><a class='btn btn-primary btn-sm' href='/finaladms/update_expense.php?id={$row_expense['expense_id']}'>Update</a> <a class='btn btn-primary btn-sm btn-danger' href='/finaladms/delete_expense.php?id={$row_expense['expense_id']}' onclick='return confirm(\"Are you sure you want to delete this expense transaction?\")'>Delete</a></td></tr>";
                        }
                        echo "</tbody>";
                        echo "</table>";
                    } else {
                        echo "<p>No expense records found for the specified date.</p>";
                    }
                    echo "<button class='btn btn-secondary' onclick='hideTransactionBoxAndButton()'>Hide</button>";
                    echo "</div>";
                }
                ?>
                <script>
                    function hideTransactionBoxAndButton() {
                        var transactionBox = document.getElementById('transactionBox');
                        var hideButton = document.querySelector('button.btn-secondary');
                        transactionBox.style.display = "none";
                        hideButton.style.display = "none";
                    }
                </script>
                <style>
                    .transaction-box {
                        background-color: #f8f9fa;
                        border: 1px solid #ced4da;
                        border-radius: 8px;
                        padding: 20px;
                        margin-bottom: 10px;
                    }
                    .transaction-box table {
                        width: 100%;
                    }
                </style>
            </div>
        </div>
        <br>
        <table class="table">
            <h4>TRANSACTIONS</h4>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Date (D/M/Y)</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "etreasure";
                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $sql = "SELECT t.transaction_id, t.ttype, 
                       CASE 
                            WHEN t.ttype = 'CASHIN' THEN ci.cdate
                            WHEN t.ttype = 'EXPENSE' THEN e.edate
                            ELSE ''
                        END AS date,
                       CASE 
                            WHEN t.ttype = 'CASHIN' THEN ci.cdescription
                            WHEN t.ttype = 'EXPENSE' THEN e.edescription
                            ELSE ''
                        END AS description,
                       CASE 
                            WHEN t.ttype = 'CASHIN' THEN ci.camount
                            WHEN t.ttype = 'EXPENSE' THEN e.eamount
                            ELSE ''
                        END AS tamount
                FROM transaction t
                LEFT JOIN cash_in ci ON t.cashin_id = ci.cashin_id
                LEFT JOIN expense e ON t.expense_id = e.expense_id
                ";
                $result = $conn->query($sql);
                if (!$result) {
                    die("Invalid query for transactions table: " . $conn->error);
                }
                while ($row = $result->fetch_assoc()) {
                    echo "
                <tr>
                    <td>{$row['ttype']}</td>
                    <td>{$row['date']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['tamount']}</td>
                    <td>
                        <a class='btn btn-primary btn-sm' href='/finaladms/update.php?id={$row['transaction_id']}'>Update</a>
                        <a class='btn btn-primary btn-sm btn-danger' href='/finaladms/delete.php?id={$row['transaction_id']}' onclick='return confirm(\"Are you sure you want to delete this transaction?\")'>Delete</a>
                    </td>
                </tr>
            ";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
function calculateTotalAmount()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "etreasure";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT SUM(tamount) AS total_amount FROM transaction";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["total_amount"];
    } else {
        return 0;
    }
}
?>
