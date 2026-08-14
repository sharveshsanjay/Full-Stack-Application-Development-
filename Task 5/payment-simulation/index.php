<?php

require_once "config/db.php";

/* Get users */

$users = $conn->query(
    "SELECT user_id, name, balance
     FROM users
     ORDER BY name"
);

/* Get merchants */

$merchants = $conn->query(
    "SELECT merchant_id, merchant_name, balance
     FROM merchants
     ORDER BY merchant_name"
);

/* Get transactions */

$transactions = $conn->query(
    "SELECT
        t.transaction_id,
        u.name AS user_name,
        m.merchant_name,
        t.amount,
        t.status,
        t.transaction_date
     FROM transactions t
     JOIN users u
        ON t.user_id = u.user_id
     JOIN merchants m
        ON t.merchant_id = m.merchant_id
     ORDER BY t.transaction_date DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Payment Transaction Simulation</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <header class="header">

        <h1>Payment Transaction Simulation</h1>

        <p>
            Transaction-based payment system using
            COMMIT and ROLLBACK
        </p>

    </header>


    <!-- PAYMENT FORM -->

    <section class="payment-card">

        <h2>Make Payment</h2>

        <form action="process_payment.php"
              method="POST">

            <div class="form-group">

                <label for="user_id">
                    Select User
                </label>

                <select name="user_id"
                        id="user_id"
                        required>

                    <option value="">
                        Select User
                    </option>

                    <?php while ($user = $users->fetch_assoc()) { ?>

                        <option value="<?php echo $user['user_id']; ?>">

                            <?php
                            echo htmlspecialchars($user['name']);
                            ?>

                            -
                            ₹<?php
                            echo number_format(
                                $user['balance'],
                                2
                            );
                            ?>

                        </option>

                    <?php } ?>

                </select>

            </div>


            <div class="form-group">

                <label for="merchant_id">
                    Select Merchant
                </label>

                <select name="merchant_id"
                        id="merchant_id"
                        required>

                    <option value="">
                        Select Merchant
                    </option>

                    <?php while (
                        $merchant = $merchants->fetch_assoc()
                    ) { ?>

                        <option
                            value="<?php
                            echo $merchant['merchant_id'];
                            ?>">

                            <?php
                            echo htmlspecialchars(
                                $merchant['merchant_name']
                            );
                            ?>

                        </option>

                    <?php } ?>

                </select>

            </div>


            <div class="form-group">

                <label for="amount">
                    Payment Amount
                </label>

                <input
                    type="number"
                    name="amount"
                    id="amount"
                    min="1"
                    step="0.01"
                    placeholder="Enter amount"
                    required
                >

            </div>


            <div class="form-group failure-option">

                <label>

                    <input
                        type="checkbox"
                        name="simulate_failure"
                        value="1"
                    >

                    Simulate Payment Failure

                </label>

            </div>


            <button type="submit">
                Process Payment
            </button>

        </form>

    </section>


    <!-- ACCOUNT BALANCES -->

    <section class="section">

        <h2>Current User Balances</h2>

        <div class="balance-grid">

            <?php

            $balanceUsers = $conn->query(
                "SELECT name, balance
                 FROM users
                 ORDER BY name"
            );

            while (
                $user = $balanceUsers->fetch_assoc()
            ) {

            ?>

                <div class="balance-card">

                    <h3>
                        <?php
                        echo htmlspecialchars($user['name']);
                        ?>
                    </h3>

                    <p>
                        ₹<?php
                        echo number_format(
                            $user['balance'],
                            2
                        );
                        ?>
                    </p>

                </div>

            <?php } ?>

        </div>

    </section>


    <!-- MERCHANT BALANCES -->

    <section class="section">

        <h2>Merchant Balances</h2>

        <div class="balance-grid">

            <?php

            $balanceMerchants = $conn->query(
                "SELECT merchant_name, balance
                 FROM merchants
                 ORDER BY merchant_name"
            );

            while (
                $merchant =
                $balanceMerchants->fetch_assoc()
            ) {

            ?>

                <div class="balance-card">

                    <h3>
                        <?php
                        echo htmlspecialchars(
                            $merchant['merchant_name']
                        );
                        ?>
                    </h3>

                    <p>
                        ₹<?php
                        echo number_format(
                            $merchant['balance'],
                            2
                        );
                        ?>
                    </p>

                </div>

            <?php } ?>

        </div>

    </section>


    <!-- TRANSACTION HISTORY -->

    <section class="section">

        <h2>Transaction History</h2>

        <div class="table-container">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Merchant</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>

                </thead>

                <tbody>

                <?php

                if ($transactions->num_rows > 0) {

                    while (
                        $transaction =
                        $transactions->fetch_assoc()
                    ) {

                ?>

                    <tr>

                        <td>
                            #<?php
                            echo $transaction[
                                'transaction_id'
                            ];
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $transaction['user_name']
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $transaction['merchant_name']
                            );
                            ?>
                        </td>

                        <td>
                            ₹<?php
                            echo number_format(
                                $transaction['amount'],
                                2
                            );
                            ?>
                        </td>

                        <td>

                            <span class="status
                                <?php
                                echo strtolower(
                                    $transaction['status']
                                );
                                ?>">

                                <?php
                                echo $transaction['status'];
                                ?>

                            </span>

                        </td>

                        <td>
                            <?php
                            echo $transaction[
                                'transaction_date'
                            ];
                            ?>
                        </td>

                    </tr>

                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td colspan="6"
                            class="empty">

                            No transactions yet.

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </section>


    <footer>

        <p>
            Task 5 — Transaction-Based Payment Simulation
        </p>

        <p>
            Full Stack Application Development
        </p>

    </footer>

</div>

</body>

</html>