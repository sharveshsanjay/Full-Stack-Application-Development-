<?php

require_once "db.php";

$sql = "SELECT * FROM students";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Records</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 40px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #222;
            color: white;
        }

        tr:nth-child(even) {
            background: #f5f5f5;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #222;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>

</head>

<body>

    <div class="container">

        <h1>Student Records</h1>

        <table>

            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>DOB</th>
                <th>Department</th>
                <th>Phone</th>
            </tr>

            <?php

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {

                    echo "<tr>";

                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["dob"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["department"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["phone"]) . "</td>";

                    echo "</tr>";
                }

            } else {

                echo "<tr>";
                echo "<td colspan='6'>No student records found.</td>";
                echo "</tr>";

            }

            $conn->close();

            ?>

        </table>

        <a class="back" href="../index.html">
            Register New Student
        </a>

    </div>

</body>

</html>