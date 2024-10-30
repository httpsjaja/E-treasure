<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "etreasure";

$conn = new mysqli($servername, $username, $password, $dbname);

$expense_id = $_GET['id'] ?? '';
$edate = "";
$edescription = "";
$eamount = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $day = $_POST['day'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $edate = "$day-$month-$year";
    $edescription = $_POST["edescription"];
    $eamount = -$_POST["eamount"];

    do {
        if (empty($edate) || empty($edescription) || empty($eamount)) {
            $errorMessage = "All the fields are required.";
            break;
        }

        $sql = "UPDATE expense SET edate=?, edescription=?, eamount=? WHERE expense_id=?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $errorMessage = "Invalid query: " . $conn->error;
            break;
        }

        $stmt->bind_param("ssdi", $edate, $edescription, $eamount, $expense_id);

        $stmt->execute();

        $sql_transaction = "UPDATE transaction SET expense_id='$expense_id', tamount='$eamount' WHERE expense_id='$expense_id'";
        $result_transaction = $conn->query($sql_transaction);

        if (!$result_transaction) {
            $errorMessage = "Error updating related transaction records: " . $conn->error;
            break;
        }

        $successMessage = "Expense updated successfully";

        header("location: /finaladms/dashboard.php");
        exit();

    } while (false);
}

$sql_fetch = "SELECT * FROM expense WHERE expense_id=?";
$stmt_fetch = $conn->prepare($sql_fetch);

$stmt_fetch->bind_param("i", $expense_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $edate = $row['edate'];
    $edescription = $row['edescription'];
    $eamount = abs($row['eamount']);
} else {
    $errorMessage = "Expense record not found";
}

$conn->close();
?>

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
    <title>Update Expense</title>
</head>

<body>
    <div class="container my-5">
        <h2>UPDATE EXPENSE TRANSACTION</h2>
        <br>

        <?php
        if (!empty($errorMessage)) {
            echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>$errorMessage</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            ";
        }
        ?>

        <form method="post" id="updateForm">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label"> Transaction Date </label>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col">
                            <label for="day" class="form-label"> Day: </label>
                            <select name="day" id="day" class="form-select">
                                <?php
                                for ($day = 1; $day <= 31; $day++) {
                                    $selected = ($day == date('d', strtotime($edate))) ? 'selected' : '';
                                    echo "<option value='$day' $selected>$day</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col">
                            <label for="month" class="form-label"> Month: </label>
                            <select name="month" id="month" class="form-select">
                                <?php
                                for ($month = 1; $month <= 12; $month++) {
                                    $selected = ($month == date('m', strtotime($edate))) ? 'selected' : '';
                                    echo "<option value='$month' $selected>$month</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col">
                            <label for="year" class="form-label"> Year:</label>
                            <select name="year" id="year" class="form-select">
                                <?php
                                for ($year = 2023; $year <= 2030; $year++) {
                                    $selected = ($year == date('Y', strtotime($edate))) ? 'selected' : '';
                                    echo "<option value='$year' $selected>$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row mb-3">
                <label for="edescription" class="col-sm-3 col-form-label"> Description </label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="edescription" id="edescription" value="<?php echo $edescription ?>" oninput="validateDescription(this)">
                    <div id="descriptionError" class="invalid-feedback"></div>
                </div>
            </div>

            <div class="row mb-3">
                <label for="eamount" class="col-sm-3 col-form-label"> Amount </label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="eamount" id="eamount" value="<?php echo $eamount ?>" oninput="validateAmount(this)">
                    <div id="amountError" class="invalid-feedback"></div>
                </div>
            </div>

            <?php
            if (!empty($successMessage)) {
                echo "
                <div class='row mb-3'>
                    <div class='offset-sm-3 col-sm-6'>
                        <div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <strong>$successMessage</strong>
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>
                    </div>
                </div>    
                ";
            }
            ?>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary"> Update </button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="/finaladms/dashboard.php" role="button"> Back </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        function validateAmount(input) {
            const amountError = document.getElementById('amountError');
            if (input.value.trim() === '') {
                amountError.textContent = 'Amount is required';
                input.classList.add('is-invalid');
            } else if (isNaN(input.value)) {
                amountError.textContent = 'Amount must be numeric';
                input.classList.add('is-invalid');
            } else {
                amountError.textContent = '';
                input.classList.remove('is-invalid');
            }   
        }

        function validateDescription(input) {
            const descriptionError = document.getElementById('descriptionError');
            if (input.value.trim() === '') {
                descriptionError.textContent = 'Description is required';
                input.classList.add('is-invalid');
            } else {
                descriptionError.textContent = '';
                input.classList.remove('is-invalid');
            }
        }

        document.getElementById('updateForm').addEventListener('submit', function(event) {
            const amountInput = document.getElementById('eamount');
            const descriptionInput = document.getElementById('edescription');
            const amountError = document.getElementById('amountError');
            const descriptionError = document.getElementById('descriptionError');

            if (amountInput.value.trim() === '') {
                amountError.textContent = 'Amount is required';
                amountInput.classList.add('is-invalid');
                event.preventDefault(); // Prevent form submission if validation fails
            } else if (isNaN(amountInput.value)) {
                amountError.textContent = 'Amount must be numeric';
                amountInput.classList.add('is-invalid');
                event.preventDefault(); // Prevent form submission if validation fails
            } else {
                amountError.textContent = '';
                amountInput.classList.remove('is-invalid');
            }

            if (descriptionInput.value.trim() === '') {
                descriptionError.textContent = 'Description is required';
                descriptionInput.classList.add('is-invalid');
                event.preventDefault();
            } else {
                descriptionError.textContent = '';
                descriptionInput.classList.remove('is-invalid');
            }
        });
    </script>

</body>

</html>