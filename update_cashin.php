<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "etreasure";

$conn = new mysqli($servername, $username, $password, $dbname);

$cashin_id = $_GET['id'] ?? '';
$cdate = "";
$cdescription = "";
$camount = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $day = $_POST['day'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $cdate = "$day-$month-$year";
    $cdescription = $conn->real_escape_string($_POST["cdescription"]);
    $camount = $_POST["camount"];

    do {
        if (empty($cdate) || empty($cdescription) || empty($camount)) {
            $errorMessage = "All the fields are required.";
            break;
        }

        $sql = "UPDATE cash_in SET cdate='$cdate', cdescription='$cdescription', camount='$camount' WHERE cashin_id='$cashin_id'";

        $result = $conn->query($sql);

        if (!$result) {
            $errorMessage = "Error updating record: " . $conn->error;
            break;
        }

        $sql_transaction = "UPDATE transaction SET cashin_id='$cashin_id', tamount='$camount' WHERE cashin_id='$cashin_id'";
        $result_transaction = $conn->query($sql_transaction);

        if (!$result_transaction) {
            $errorMessage = "Error updating related transaction records: " . $conn->error;
            break;
        }

        $successMessage = "Transaction updated successfully";
        header("location: /finaladms/dashboard.php");
        exit();
    } while (false);
}

// Fetch cash_in details for the given cashin_id
$sql_fetch = "SELECT * FROM cash_in WHERE cashin_id='$cashin_id'";
$result_fetch = $conn->query($sql_fetch);

if ($result_fetch->num_rows > 0) {
    $row = $result_fetch->fetch_assoc();
    $cdate = $row['cdate'];
    $cdescription = $row['cdescription'];
    $camount = abs($row['camount']);
} else {
    $errorMessage = "Cash In record not found";
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
    <title>Update Cash-in</title>
</head>

<body>
    <div class="container my-5">
        <h2>UPDATE CASH-IN TRANSACTION</h2>
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
                                    $selected = ($day == date('d', strtotime($cdate))) ? 'selected' : '';
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
                                    $selected = ($month == date('m', strtotime($cdate))) ? 'selected' : '';
                                    echo "<option value='$month' $selected>$month</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col">
                            <label for="year" class="form-label"> Year: </label>
                            <select name="year" id="year" class="form-select">
                                <?php
                                for ($year = 2023; $year <= 2030; $year++) {
                                    $selected = ($year == date('Y', strtotime($cdate))) ? 'selected' : '';
                                    echo "<option value='$year' $selected>$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label for="cdescription" class="col-sm-3 col-form-label"> Description </label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="cdescription" id="cdescription" value="<?php echo $cdescription ?>" oninput="validateDescription(this)">
                    <div id="descriptionError" class="invalid-feedback"></div>
                </div>
            </div>

            <div class="row mb-3">
                <label for="camount" class="col-sm-3 col-form-label"> Amount </label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="camount" id="camount" value="<?php echo $camount ?>" oninput="validateAmount(this)">
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
            const amountInput = document.getElementById('camount');
            const descriptionInput = document.getElementById('cdescription');
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
                event.preventDefault(); // Prevent form submission if validation fails
            } else {
                descriptionError.textContent = '';
                descriptionInput.classList.remove('is-invalid');
            }
        });
    </script>
</body>

</html>
