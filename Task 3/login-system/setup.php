<?php

require_once "php/db.php";

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";

if (!$conn->query($sql)) {
    die("Error creating users table: " . $conn->error);
}

$username = "admin";
$plainPassword = "admin123";

$check = $conn->prepare("SELECT id FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$result = $check->get_result();

if ($result->num_rows == 0) {

    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "INSERT INTO users (username, password) VALUES (?, ?)"
    );

    $stmt->bind_param("ss", $username, $hashedPassword);

    if ($stmt->execute()) {
        echo "<h2>Setup completed successfully!</h2>";
        echo "<p>Username: <strong>admin</strong></p>";
        echo "<p>Password: <strong>admin123</strong></p>";
        echo "<p><a href='index.php'>Go to Login</a></p>";
    } else {
        echo "Error inserting user: " . $conn->error;
    }

    $stmt->close();

} else {
    echo "<h2>Setup already completed.</h2>";
    echo "<p>The admin user already exists.</p>";
    echo "<p><a href='index.php'>Go to Login</a></p>";
}

$check->close();
$conn->close();

?>