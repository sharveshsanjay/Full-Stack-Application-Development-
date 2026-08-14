<?php

require_once "config/db.php";

/* =========================
   SUMMARY COUNTS
========================= */

$customerCountResult = $conn->query(
    "SELECT COUNT(*) AS total_customers FROM customers"
);

$customerCount = $customerCountResult->fetch_assoc()['total_customers'];


$productCountResult = $conn->query(
    "SELECT COUNT(*) AS total_products FROM products"
);

$productCount = $productCountResult->fetch_assoc()['total_products'];


$orderCountResult = $conn->query(
    "SELECT COUNT(*) AS total_orders FROM orders"
);

$orderCount = $orderCountResult->fetch_assoc()['total_orders'];


/* =========================
   CUSTOMER ORDER HISTORY
========================= */

$orderSql = "SELECT
                c.name AS customer_name,
                c.email,
                o.order_id,
                p.product_name,
                o.quantity,
                p.price,
                (o.quantity * p.price) AS total,
                o.order_date
             FROM orders o
             JOIN customers c
                 ON o.customer_id = c.customer_id
             JOIN products p
                 ON o.product_id = p.product_id
             ORDER BY o.order_date DESC";

$orderResult = $conn->query($orderSql);


/* =========================
   HIGHEST VALUE ORDER
========================= */

$highestSql = "SELECT
                  o.order_id,
                  c.name AS customer_name,
                  p.product_name,
                  o.quantity,
                  p.price,
                  (o.quantity * p.price) AS total,
                  o.order_date
               FROM orders o
               JOIN customers c
                   ON o.customer_id = c.customer_id
               JOIN products p
                   ON o.product_id = p.product_id
               WHERE (o.quantity * p.price) = (
                   SELECT MAX(o2.quantity * p2.price)
                   FROM orders o2
                   JOIN products p2
                       ON o2.product_id = p2.product_id
               )";

$highestResult = $conn->query($highestSql);
$highestOrder = $highestResult->fetch_assoc();


/* =========================
   MOST ACTIVE CUSTOMER
========================= */

$activeSql = "SELECT
                 c.customer_id,
                 c.name,
                 c.email,
                 COUNT(o.order_id) AS total_orders
              FROM customers c
              JOIN orders o
                  ON c.customer_id = o.customer_id
              GROUP BY c.customer_id, c.name, c.email
              HAVING COUNT(o.order_id) = (
                  SELECT MAX(order_count)
                  FROM (
                      SELECT COUNT(*) AS order_count
                      FROM orders
                      GROUP BY customer_id
                  ) AS customer_orders
              )";

$activeResult = $conn->query($activeSql);
$activeCustomer = $activeResult->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Order Management Dashboard</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <!-- HEADER -->

    <header class="header">

        <div>
            <h1>Order Management</h1>

            <p>
                E-commerce Order Management Dashboard
            </p>
        </div>

    </header>


    <!-- SUMMARY CARDS -->

    <section class="summary">

        <div class="card">

            <h3>Total Customers</h3>

            <div class="number">
                <?php echo $customerCount; ?>
            </div>

        </div>


        <div class="card">

            <h3>Total Products</h3>

            <div class="number">
                <?php echo $productCount; ?>
            </div>

        </div>


        <div class="card">

            <h3>Total Orders</h3>

            <div class="number">
                <?php echo $orderCount; ?>
            </div>

        </div>

    </section>


    <!-- HIGHLIGHTS -->

    <section class="highlights">

        <!-- Highest Value Order -->

        <div class="highlight-card">

            <h2>Highest Value Order</h2>

            <?php if ($highestOrder) { ?>

                <div class="highlight-content">

                    <p>
                        <strong>Order ID:</strong>
                        <?php echo $highestOrder['order_id']; ?>
                    </p>

                    <p>
                        <strong>Customer:</strong>
                        <?php echo $highestOrder['customer_name']; ?>
                    </p>

                    <p>
                        <strong>Product:</strong>
                        <?php echo $highestOrder['product_name']; ?>
                    </p>

                    <p>
                        <strong>Quantity:</strong>
                        <?php echo $highestOrder['quantity']; ?>
                    </p>

                    <p>
                        <strong>Total:</strong>
                        ₹<?php echo number_format(
                            $highestOrder['total'],
                            2
                        ); ?>
                    </p>

                    <p>
                        <strong>Date:</strong>
                        <?php echo $highestOrder['order_date']; ?>
                    </p>

                </div>

            <?php } ?>

        </div>


        <!-- Most Active Customer -->

        <div class="highlight-card">

            <h2>Most Active Customer</h2>

            <?php if ($activeCustomer) { ?>

                <div class="highlight-content">

                    <p>
                        <strong>Name:</strong>
                        <?php echo $activeCustomer['name']; ?>
                    </p>

                    <p>
                        <strong>Email:</strong>
                        <?php echo $activeCustomer['email']; ?>
                    </p>

                    <p>
                        <strong>Total Orders:</strong>
                        <?php echo $activeCustomer['total_orders']; ?>
                    </p>

                </div>

            <?php } ?>

        </div>

    </section>


    <!-- ORDER HISTORY -->

    <section class="orders">

        <div class="section-header">

            <h2>Customer Order History</h2>

            <p>
                Latest orders displayed first
            </p>

        </div>


        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>Customer</th>
                        <th>Email</th>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Order Date</th>

                    </tr>

                </thead>


                <tbody>

                <?php while ($row = $orderResult->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?php echo $row['customer_name']; ?>
                        </td>

                        <td>
                            <?php echo $row['email']; ?>
                        </td>

                        <td>
                            #<?php echo $row['order_id']; ?>
                        </td>

                        <td>
                            <?php echo $row['product_name']; ?>
                        </td>

                        <td>
                            <?php echo $row['quantity']; ?>
                        </td>

                        <td>
                            ₹<?php echo number_format(
                                $row['price'],
                                2
                            ); ?>
                        </td>

                        <td class="total">

                            ₹<?php echo number_format(
                                $row['total'],
                                2
                            ); ?>

                        </td>

                        <td>
                            <?php echo $row['order_date']; ?>
                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </section>


    <!-- FOOTER -->

    <footer>

        <p>
            Task 4 — Order Management using Joins
        </p>

        <p>
            Full Stack Application Development
        </p>

    </footer>

</div>

</body>

</html>