<?php

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION["username"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="dashboard-container">

    <div class="dashboard-card">

        <h1>Login Successful</h1>

        <p>
            Welcome,
            <strong>
                <?php echo htmlspecialchars($username); ?>
            </strong>
        </p>

        <p>
            You have successfully authenticated using
            credentials stored in MySQL.
        </p>

        <a class="logout-btn" href="logout.php">
            Logout
        </a>

    </div>

</div>

</body>

</html>