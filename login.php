<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "etreasure";

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM usere WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;

        header("location: dashboard.php");
        exit;
    } else {
        $errorMessage = "Enter correct username and password.";
    }

    $conn->close();
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
        html,
        body {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: none;
        }

        h2 {
            text-align: center;
        }

        .login-form button {
            display: block;
            margin: 0 auto;
        }
    </style>
    <title>Login - E-TREASURE</title>
</head>

<body>
    <div class="container my-5">
        <h2>LOGIN</h2>

        <div class="login-form">
            <form method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" placeholder="Enter your username" class="form-control" id="username" name="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" placeholder="Enter your password" class="form-control" id="password" name="password">
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.onload = function() {
            <?php if (!empty($errorMessage)) { ?>
                alert("<?php echo $errorMessage; ?>");
            <?php } ?>
        };
    </script>
</body>

</html>
