<?php
require_once "config/db.php";

$message = "";
$messageType = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $rating = intval($_POST["rating"]);
    $comments = trim($_POST["comments"]);

    if (!empty($full_name) && !empty($email) && $rating >= 1 && $rating <= 5 && !empty($comments)) {
        $stmt = $conn->prepare("INSERT INTO feedbacks (full_name, email, rating, comments) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $full_name, $email, $rating, $comments);

        if ($stmt->execute()) {
            $message = "Thank you! Your feedback has been submitted successfully.";
            $messageType = "success";
        } else {
            $message = "Error submitting feedback: " . $conn->error;
            $messageType = "error";
        }
        $stmt->close();
    } else {
        $message = "Please ensure all fields are filled out properly.";
        $messageType = "error";
    }
}

// Fetch Existing Feedbacks
$feedbacks = $conn->query("SELECT * FROM feedbacks ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task 7 — Interactive Feedback Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Task 7 — Interactive Feedback System</h1>
            <p class="subtitle">Demonstrating JavaScript Event Handling (Keypress Validation, Mouse Hover, Double-Click Submit)</p>
        </header>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Feedback Form Card -->
        <div class="card">
            <h2>Submit Your Feedback</h2>
            <form id="feedbackForm" action="index.php" method="POST">
                
                <!-- Full Name Field -->
                <div class="form-group">
                    <label for="full_name">Full Name <span class="required">*</span></label>
                    <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Enter your full name">
                    <span id="nameError" class="error-msg"></span>
                </div>

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="e.g. user@example.com">
                    <span id="emailError" class="error-msg"></span>
                </div>

                <!-- Rating Dropdown -->
                <div class="form-group">
                    <label for="rating">Rating <span class="required">*</span></label>
                    <select id="rating" name="rating" class="form-control" required>
                        <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
                        <option value="4">⭐⭐⭐⭐ 4 - Good</option>
                        <option value="3">⭐⭐⭐ 3 - Average</option>
                        <option value="2">⭐⭐ 2 - Poor</option>
                        <option value="1">⭐ 1 - Very Bad</option>
                    </select>
                </div>

                <!-- Comments Field -->
                <div class="form-group">
                    <label for="comments">Feedback Comments <span class="required">*</span></label>
                    <textarea id="comments" name="comments" class="form-control" rows="4" placeholder="Write your detailed feedback here (min 10 chars)..."></textarea>
                    <span id="commentsError" class="error-msg"></span>
                </div>

                <!-- Submit Button with Double-Click Requirement -->
                <div class="form-actions">
                    <button type="button" id="submit_btn" class="btn btn-submit">
                        Submit Feedback (Double Click)
                    </button>
                    <p id="clickStatus" class="click-status hint">Tip: Double-click button to submit.</p>
                </div>
            </form>
        </div>

        <!-- Recent Feedbacks Table -->
        <div class="card mt-4">
            <h2>Customer Feedbacks List</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Rating</th>
                            <th>Comments</th>
                            <th>Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($feedbacks->num_rows > 0): ?>
                            <?php while ($row = $feedbacks->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $row['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($row['full_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td>
                                        <span class="star-rating">
                                            <?= str_repeat("⭐", $row['rating']) ?> (<?= $row['rating'] ?>/5)
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['comments']) ?></td>
                                    <td><?= $row['submitted_at'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No feedback submitted yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
