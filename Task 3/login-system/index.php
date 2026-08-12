<?php

session_start();

if (isset($_SESSION["username"])) {
    header("Location: dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login System</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="login-container">

    <div class="login-card">

        <h1>Login</h1>

        <p class="subtitle">
            Student Application Login System
        </p>

        <?php if (isset($_GET["error"])): ?>

            <div class="server-error">
                <?php echo htmlspecialchars($_GET["error"]); ?>
            </div>

        <?php endif; ?>

        <form
            id="loginForm"
            action="php/login.php"
            method="POST"
            novalidate
        >

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter username"
                >

                <small id="usernameError"></small>

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                >

                <small id="passwordError"></small>

            </div>

            <button type="submit">
                Login
            </button>

        </form>

        <div class="demo-info">

            <strong>Demo Credentials</strong>

            <p>Username: admin</p>
            <p>Password: admin123</p>

        </div>

    </div>

</div>

<script src="js/validation.js"></script>

</body>

</html>