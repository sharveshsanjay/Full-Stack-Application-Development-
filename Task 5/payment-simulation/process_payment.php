<?php

require_once "config/db.php";


/* Check request */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");
    exit;

}


/* Get form data */

$user_id = intval($_POST["user_id"]);
$merchant_id = intval($_POST["merchant_id"]);
$amount = floatval($_POST["amount"]);

$simulate_failure =
    isset($_POST["simulate_failure"]);


/* Validate amount */

if ($amount <= 0) {

    die("Invalid payment amount.");

}


/*
====================================================
START TRANSACTION
====================================================
*/

$conn->begin_transaction();


try {

    /*
    ------------------------------------------------
    STEP 1
    Get user balance
    ------------------------------------------------
    */

    $stmt = $conn->prepare(
        "SELECT balance
         FROM users
         WHERE user_id = ?
         FOR UPDATE"
    );

    $stmt->bind_param("i", $user_id);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {

        throw new Exception(
            "User not found."
        );

    }

    $user = $result->fetch_assoc();

    $user_balance = floatval(
        $user["balance"]
    );


    /*
    ------------------------------------------------
    STEP 2
    Check sufficient balance
    ------------------------------------------------
    */

    if ($user_balance < $amount) {

        throw new Exception(
            "Insufficient user balance."
        );

    }


    /*
    ------------------------------------------------
    STEP 3
    Deduct money from user
    ------------------------------------------------
    */

    $stmt = $conn->prepare(
        "UPDATE users
         SET balance = balance - ?
         WHERE user_id = ?"
    );

    $stmt->bind_param(
        "di",
        $amount,
        $user_id
    );

    if (!$stmt->execute()) {

        throw new Exception(
            "Failed to deduct user balance."
        );

    }


    /*
    ------------------------------------------------
    STEP 4
    Add money to merchant
    ------------------------------------------------
    */

    $stmt = $conn->prepare(
        "UPDATE merchants
         SET balance = balance + ?
         WHERE merchant_id = ?"
    );

    $stmt->bind_param(
        "di",
        $amount,
        $merchant_id
    );

    if (!$stmt->execute()) {

        throw new Exception(
            "Failed to update merchant balance."
        );

    }


    /*
    ------------------------------------------------
    STEP 5
    Simulate failure if requested
    ------------------------------------------------
    */

    if ($simulate_failure) {

        throw new Exception(
            "Payment failure simulated."
        );

    }


    /*
    ------------------------------------------------
    STEP 6
    Record successful transaction
    ------------------------------------------------
    */

    $status = "SUCCESS";

    $stmt = $conn->prepare(
        "INSERT INTO transactions
        (user_id, merchant_id, amount, status)
        VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "iids",
        $user_id,
        $merchant_id,
        $amount,
        $status
    );

    if (!$stmt->execute()) {

        throw new Exception(
            "Failed to record transaction."
        );

    }


    /*
    =================================================
    COMMIT
    =================================================
    */

    $conn->commit();

    ?>

    <!DOCTYPE html>

    <html>

    <head>

        <title>Payment Successful</title>

        <link rel="stylesheet"
              href="style.css">

    </head>

    <body>

        <div class="result success-result">

            <h1>Payment Successful</h1>

            <p>
                Transaction completed successfully.
            </p>

            <div class="result-box">

                <p>
                    <strong>Amount:</strong>
                    ₹<?php
                    echo number_format(
                        $amount,
                        2
                    );
                    ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    COMMITTED
                </p>

                <p>
                    User balance was deducted.
                </p>

                <p>
                    Merchant balance was increased.
                </p>

            </div>

            <a href="index.php">
                Back to Dashboard
            </a>

        </div>

    </body>

    </html>

    <?php


} catch (Exception $e) {


    /*
    =================================================
    ROLLBACK
    =================================================
    */

    $conn->rollback();


    /*
    ------------------------------------------------
    Record FAILED transaction AFTER rollback
    ------------------------------------------------

    The failed transaction record is intentionally
    inserted after rollback so that the failure
    itself can still be displayed in history.
    ------------------------------------------------
    */

    $status = "FAILED";

    $stmt = $conn->prepare(
        "INSERT INTO transactions
        (user_id, merchant_id, amount, status)
        VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "iids",
        $user_id,
        $merchant_id,
        $amount,
        $status
    );

    $stmt->execute();

    ?>

    <!DOCTYPE html>

    <html>

    <head>

        <title>Payment Failed</title>

        <link rel="stylesheet"
              href="style.css">

    </head>

    <body>

        <div class="result failure-result">

            <h1>Payment Failed</h1>

            <p>
                The transaction was rolled back.
            </p>

            <div class="result-box">

                <p>
                    <strong>Amount:</strong>
                    ₹<?php
                    echo number_format(
                        $amount,
                        2
                    );
                    ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    ROLLBACK
                </p>

                <p>
                    User balance was restored.
                </p>

                <p>
                    Merchant balance was restored.
                </p>

                <p>
                    <strong>Reason:</strong>
                    <?php
                    echo htmlspecialchars(
                        $e->getMessage()
                    );
                    ?>
                </p>

            </div>

            <a href="index.php">
                Back to Dashboard
            </a>

        </div>

    </body>

    </html>

    <?php

}

?>