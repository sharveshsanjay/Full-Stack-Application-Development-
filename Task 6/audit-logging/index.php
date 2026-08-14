<?php
require_once "config/db.php";

$message = "";
$messageType = "";

// Handle Form Submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Add Employee (INSERT)
    if (isset($_POST["add_employee"])) {
        $name = trim($_POST["name"]);
        $department = trim($_POST["department"]);
        $salary = floatval($_POST["salary"]);

        if (!empty($name) && !empty($department) && $salary > 0) {
            $stmt = $conn->prepare("INSERT INTO employees (name, department, salary) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $name, $department, $salary);
            if ($stmt->execute()) {
                $message = "Employee added successfully! Audit trigger logged the INSERT.";
                $messageType = "success";
            } else {
                $message = "Error adding employee: " . $conn->error;
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Please fill in all fields correctly.";
            $messageType = "error";
        }
    }

    // 2. Update Employee Salary (UPDATE)
    if (isset($_POST["update_salary"])) {
        $emp_id = intval($_POST["emp_id"]);
        $new_salary = floatval($_POST["new_salary"]);

        if ($emp_id > 0 && $new_salary > 0) {
            $stmt = $conn->prepare("UPDATE employees SET salary = ? WHERE id = ?");
            $stmt->bind_param("di", $new_salary, $emp_id);
            if ($stmt->execute()) {
                $message = "Employee salary updated! Audit trigger logged the UPDATE.";
                $messageType = "success";
            } else {
                $message = "Error updating salary: " . $conn->error;
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Please provide valid ID and salary.";
            $messageType = "error";
        }
    }
}

// Fetch Employees
$employees = $conn->query("SELECT * FROM employees ORDER BY id DESC");

// Fetch Audit Logs (Populated by MySQL Triggers)
$audit_logs = $conn->query("SELECT * FROM audit_logs ORDER BY id DESC");

// Fetch Daily Activity Report (From MySQL View)
$reports = $conn->query("SELECT * FROM daily_activity_report");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task 6 — Automated Logging (Triggers & Views)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Task 6 — Automated Logging System</h1>
            <p class="subtitle">Demonstrating MySQL Triggers (INSERT/UPDATE Audit Logs) & Database Views</p>
        </header>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="grid-2">
            <!-- Form 1: Add Employee -->
            <div class="card">
                <h2>Add New Employee</h2>
                <form action="index.php" method="POST">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required placeholder="e.g. Alex Morgan">
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <input type="text" id="department" name="department" required placeholder="e.g. IT Support">
                    </div>
                    <div class="form-group">
                        <label for="salary">Salary ($)</label>
                        <input type="number" step="0.01" id="salary" name="salary" required placeholder="e.g. 65000">
                    </div>
                    <button type="submit" name="add_employee" class="btn btn-primary">Add Employee</button>
                </form>
            </div>

            <!-- Form 2: Update Employee Salary -->
            <div class="card">
                <h2>Update Employee Salary</h2>
                <form action="index.php" method="POST">
                    <div class="form-group">
                        <label for="emp_id">Select Employee</label>
                        <select id="emp_id" name="emp_id" required>
                            <option value="">-- Choose Employee --</option>
                            <?php 
                            $emp_options = $conn->query("SELECT id, name, salary FROM employees ORDER BY name ASC");
                            while ($row = $emp_options->fetch_assoc()): 
                            ?>
                                <option value="<?= $row['id'] ?>">
                                    <?= htmlspecialchars($row['name']) ?> (Current: $<?= number_format($row['salary'], 2) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="new_salary">New Salary ($)</label>
                        <input type="number" step="0.01" id="new_salary" name="new_salary" required placeholder="e.g. 72000">
                    </div>
                    <button type="submit" name="update_salary" class="btn btn-warning">Update Salary</button>
                </form>
            </div>
        </div>

        <!-- Section 1: Employee Directory -->
        <div class="card mt-4">
            <h2>Employee Directory</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Salary</th>
                            <th>Created At</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($employees->num_rows > 0): ?>
                            <?php while ($emp = $employees->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $emp['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($emp['name']) ?></strong></td>
                                    <td><span class="badge badge-dept"><?= htmlspecialchars($emp['department']) ?></span></td>
                                    <td>$<?= number_format($emp['salary'], 2) ?></td>
                                    <td><?= $emp['created_at'] ?></td>
                                    <td><?= $emp['updated_at'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No employees found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: Automated Audit Logs (Triggered automatically by MySQL) -->
        <div class="card mt-4">
            <h2>Automated Audit Logs (Generated by MySQL Triggers)</h2>
            <p class="section-desc">Every time an employee is added or updated, MySQL triggers automatically insert a log entry into <code>audit_logs</code> table.</p>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Log ID</th>
                            <th>Table</th>
                            <th>Action</th>
                            <th>Record ID</th>
                            <th>Details</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($audit_logs->num_rows > 0): ?>
                            <?php while ($log = $audit_logs->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $log['id'] ?></td>
                                    <td><code><?= htmlspecialchars($log['table_name']) ?></code></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($log['action_type']) ?>">
                                            <?= $log['action_type'] ?>
                                        </span>
                                    </td>
                                    <td><?= $log['record_id'] ?></td>
                                    <td><?= htmlspecialchars($log['details']) ?></td>
                                    <td><?= $log['performed_at'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No audit logs available.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 3: Daily Activity Report (From Database View) -->
        <div class="card mt-4">
            <h2>Daily Activity Report (Generated by SQL View: <code>daily_activity_report</code>)</h2>
            <p class="section-desc">A dynamic view summarizing activity counts grouped by date and action type.</p>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Report Date</th>
                            <th>Action Type</th>
                            <th>Total Actions Executed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reports->num_rows > 0): ?>
                            <?php while ($rep = $reports->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= $rep['report_date'] ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($rep['action_type']) ?>">
                                            <?= $rep['action_type'] ?>
                                        </span>
                                    </td>
                                    <td><strong><?= $rep['total_actions'] ?></strong></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center">No daily activity data available.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
