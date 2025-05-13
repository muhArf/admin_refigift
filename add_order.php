<?php
$servername = "localhost"; // Change if necessary
$username = ""; // Your database username
$password = ""; // Your database password
$dbname = "refigift.db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $product_name = $_POST['product_name'];
    $status = $_POST['status'];
    $order_date = $_POST['order_date'];

    $stmt = $conn->prepare("INSERT INTO orders (customer_name, product_name, status, order_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $customer_name, $product_name, $status, $order_date);

    if ($stmt->execute()) {
        echo "New order added successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
