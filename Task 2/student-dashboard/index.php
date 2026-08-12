<?php

require_once "php/db.php";

$department = $_GET["department"] ?? "";
$sort = $_GET["sort"] ?? "name";
$order = $_GET["order"] ?? "ASC";

/*
|--------------------------------------------------------------------------
| Allowed sorting columns
|--------------------------------------------------------------------------
*/

$allowedSorts = [
    "name" => "name",
    "dob" => "dob"
];

$sortColumn = $allowedSorts[$sort] ?? "name";

$order = strtoupper($order);

if ($order !== "ASC" && $order !== "DESC") {
    $order = "ASC";
}

/*
|--------------------------------------------------------------------------
| Student records query
|--------------------------------------------------------------------------
*/

$sql = "SELECT * FROM students";

if ($department !== "") {
    $sql .= " WHERE department = ?";
}

$sql .= " ORDER BY $sortColumn $order";

if ($department !== "") {

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $department);
    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $result = $conn->query($sql);
}

/*
|--------------------------------------------------------------------------
| Department count
|--------------------------------------------------------------------------
*/

$countSql = "
    SELECT department, COUNT(*) AS student_count
    FROM students
    GROUP BY department
    ORDER BY department ASC
";

$countResult = $conn->query($countSql);

/*
|--------------------------------------------------------------------------
| Department dropdown
|--------------------------------------------------------------------------
*/

$departmentSql = "
    SELECT DISTINCT department
    FROM students
    ORDER BY department ASC
";

$departmentResult = $conn->query($departmentSql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Dashboard</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="container">

    <header>

        <h1>Student Dashboard</h1>

        <p>
            Student Data Retrieval, Sorting & Filtering
        </p>

    </header>


    <!-- Filters -->

    <section class="controls">

        <form method="GET">

            <div class="control">

                <label for="department">
                    Department
                </label>

                <select name="department" id="department">

                    <option value="">
                        All Departments
                    </option>

                    <?php while ($dept = $departmentResult->fetch_assoc()): ?>

                        <option
                            value="<?= htmlspecialchars($dept["department"]) ?>"
                            <?= $department === $dept["department"] ? "selected" : "" ?>
                        >
                            <?= htmlspecialchars($dept["department"]) ?>
                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <div class="control">

                <label for="sort">
                    Sort By
                </label>

                <select name="sort" id="sort">

                    <option value="name"
                        <?= $sort === "name" ? "selected" : "" ?>>
                        Name
                    </option>

                    <option value="dob"
                        <?= $sort === "dob" ? "selected" : "" ?>>
                        Date of Birth
                    </option>

                </select>

            </div>


            <div class="control">

                <label for="order">
                    Order
                </label>

                <select name="order" id="order">

                    <option value="ASC"
                        <?= $order === "ASC" ? "selected" : "" ?>>
                        Ascending
                    </option>

                    <option value="DESC"
                        <?= $order === "DESC" ? "selected" : "" ?>>
                        Descending
                    </option>

                </select>

            </div>


            <div class="control button-control">

                <button type="submit">
                    Apply
                </button>

            </div>

        </form>

    </section>


    <!-- Department Statistics -->

    <section class="statistics">

        <h2>Students Per Department</h2>

        <div class="stats-grid">

            <?php if ($countResult->num_rows > 0): ?>

                <?php while ($count = $countResult->fetch_assoc()): ?>

                    <div class="stat-card">

                        <h3>
                            <?= htmlspecialchars($count["department"]) ?>
                        </h3>

                        <strong>
                            <?= htmlspecialchars($count["student_count"]) ?>
                        </strong>

                        <span>
                            Students
                        </span>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <p>No department data available.</p>

            <?php endif; ?>

        </div>

    </section>


    <!-- Student Records -->

    <section class="records">

        <h2>Student Records</h2>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Email</th>

                        <th>DOB</th>

                        <th>Department</th>

                        <th>Phone</th>

                    </tr>

                </thead>

                <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php while ($student = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($student["id"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($student["name"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($student["email"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($student["dob"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($student["department"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($student["phone"]) ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="6">
                            No student records found.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>


    <footer>

        <a href="../../Task%201/student-registration/">
            Register New Student
        </a>

    </footer>

</div>

</body>

</html>

<?php

$conn->close();

?>