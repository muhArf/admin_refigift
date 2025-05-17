<?php
$servername = "localhost"; // Change if your database is hosted elsewhere
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "refigift_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle different requests
$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        // Fetch products
        fetch_products($conn);
        break;
    case 'POST':
        // Add a new product
        add_product($conn);
        break;
    case 'PUT':
        // Update a product
        update_product($conn);
        break;
    case 'DELETE':
        // Delete a product
        delete_product($conn);
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function fetch_products($conn) {
    $query = "SELECT * FROM products";
    $result = $conn->query($query);
    $products = array();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode($products);
}

function add_product($conn) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];

    // Handle image upload
    $image = $_FILES['image'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image["name"]);
    
    // Check if the image is a valid JPG file
    if ($image['type'] == 'image/jpeg') {
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            // Save the product to the database
            $stmt = $conn->prepare("INSERT INTO products (name, category, stock, price, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssids", $name, $category, $stock, $price, $target_file);
            $stmt->execute();

            echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Only JPG files are allowed']);
    }
}

function update_product($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    $name = $data['name'];
    $category = $data['category'];
    $stock = $data['stock'];
    $price = $data['price'];

    $stmt = $conn->prepare("UPDATE products SET name=?, category=?, stock=?, price=? WHERE id=?");
    $stmt->bind_param("ssidi", $name, $category, $stock, $price, $id);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
}

function delete_product($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
}

$conn->close();
?>
