<?php

session_start();

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit();
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {

    header("Location: ../index.php?error=Please enter username and password.");
    exit();
}

$stmt = $conn->prepare(
    "SELECT id, username, password FROM users WHERE username = ?"
);

$stmt->bind_param("s", $username);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $user = $result->fetch_assoc();

    if (password_verify($password, $user["password"])) {

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];

        header("Location: ../dashboard.php");
        exit();

    } else {

        header("Location: ../index.php?error=Invalid username or password.");
        exit();
    }

} else {

    header("Location: ../index.php?error=Invalid username or password.");
    exit();
}

$stmt->close();
$conn->close();

?>