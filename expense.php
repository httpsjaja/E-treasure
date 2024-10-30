<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "etreasure";

$conn = new mysqli($servername, $username, $password, $dbname);

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    do {
        $day = $_POST['day'];
        $month = $_POST['month'];
        $year = $_POST['year'];
        $edate = "$day-$month-$year";

        $edescription = $_POST['edescription'];
        $eamounts = $_POST['eamount'];

        if (empty($edate) || in_array('', $edescription) || in_array('', $eamounts)) {
            echo "<script>alert('All fields are required.');</script>";
            break;
        }

        for ($i = 0; $i < count($edescription); $i++) {
            $edesc = $edescription[$i];
            $eamount = $eamounts[$i];

            if (!is_numeric($eamount)) {
                echo "<script>alert('Amount must be a numeric value.');</script>";
                break;
            }

            $eamount = -$eamount;

            $sql = "INSERT INTO expense (edate, edescription, eamount) VALUES (?, ?, ?)";

            $result = $conn->prepare($sql);

            if (!$result) {
                $errorMessage = "Invalid query: " . $conn->error;
                break;
            }

            $result->bind_param("sss", $edate, $edesc, $eamount);

            $result->execute();

            $successMessage = "New transaction added";
        }

        header("location: /finaladms/dashboard.php");
        exit();

    } while (false);
}
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
    <title>Add Expense</title>
</head>

<body>
    <div class="container my-5">
        <h2>ADD EXPENSE TRANSACTION</h2>
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


        <form method="post" onsubmit="return validateForm()">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label"> Transaction Date </label>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col">
                            <label for="day" class="form-label"> Day: </label>
                            <select name="day" id="day" class="form-select">
                                <?php
                                for ($day = 1; $day <= 31; $day++) {
                                    echo "<option value='$day'>$day</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col">
                            <label for="month" class="form-label"> Month: </label>
                            <select name="month" id="month" class="form-select">
                                <?php
                                for ($month = 1; $month <= 12; $month++) {
                                    echo "<option value='$month'>$month</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col">
                            <label for="year" class="form-label"> Year: </label>
                            <select name="year" id="year" class="form-select">
                                <?php
                                for ($year = 2023; $year <= 2030; $year++) {
                                    echo "<option value='$year'>$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="additionalTransactions">
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Description </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="edescription[]" value="">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Amount </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="eamount[]" value="">
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-2 d-grid">
                    <button type="button" class="btn btn-primary" onclick="addAnotherTransaction()"> Add Another </button>
                </div>
                <div class="col-sm-2 d-grid">
                    <button type="submit" class="btn btn-primary"> Add </button>
                </div>
                <div class="col-sm-2 d-grid">
                    <a class="btn btn-outline-primary" href="/finaladms/dashboard.php" role="button"> Back </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        function addAnotherTransaction() {
            var container = document.getElementById('additionalTransactions');
            var transactionHTML = `
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Description </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="edescription[]" value="">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Amount </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="eamount[]" value="">
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', transactionHTML);
        }

        function validateForm() {
            var edescription = document.getElementsByName('edescription[]');
            var eamount = document.getElementsByName('eamount[]');

            for (var i = 0; i < edescription.length; i++) {
                if (edescription[i].value === '' || eamount[i].value === '') {
                    alert('All fields are required.');
                    return false;
                }
            }

            for (var i = 0; i < eamount.length; i++) {
                if (isNaN(eamount[i].value)) {
                    alert('Amount must be a numeric value.');
                    return false;
                }
            }

            return true;
        }
    </script>

</body>

</html>