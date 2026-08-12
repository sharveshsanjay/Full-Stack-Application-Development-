<?php

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $dob = $_POST["dob"];
    $department = $_POST["department"];
    $phone = $_POST["phone"];

    $sql = "INSERT INTO students 
            (name, email, dob, department, phone)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssss",
        $name,
        $email,
        $dob,
        $department,
        $phone
    );

    if ($stmt->execute()) {

        echo "<h2>Student registered successfully!</h2>";
        echo "<p>Name: " . htmlspecialchars($name) . "</p>";
        echo "<p>Email: " . htmlspecialchars($email) . "</p>";
        echo "<p><a href='../index.html'>Register another student</a></p>";
        echo "<p><a href='students.php'>View all students</a></p>";

    } else {

        echo "Error: " . $stmt->error;

    }

    $stmt->close();
    $conn->close();

} else {

    echo "Invalid request.";

}

?>