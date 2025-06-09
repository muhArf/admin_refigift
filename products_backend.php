<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost"; // Change if your database is hosted elsewhere
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "refigift_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Handle different requests
$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        if (isset($_GET['id'])) {
            fetch_product($conn); // Fetch a single product if ID is provided
        } else {
            fetch_products($conn); // Fetch all products
        }
        break;
    case 'POST':
        if (isset($_POST['edit_id'])) {
            update_product($conn); // Call update function for POST with edit_id
        } else {
            add_product($conn); // Call add function for POST without edit_id
        }
        break;
    case 'DELETE':
        delete_product($conn);
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
        exit();
}

function fetch_products($conn) {
    $query = "SELECT * FROM products";
    $result = $conn->query($query);
    $products = array();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode($products);
    exit(); // Stop further execution
}

function fetch_product($conn) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    }
    exit(); // Stop further execution
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
    
    if ($image['type'] == 'image/jpeg') {
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO products (name, category, stock, price, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssids", $name, $category, $stock, $price, $target_file);
            $stmt->execute();

            echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
            exit(); // Stop further execution
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Only JPG files are allowed']);
        exit();
    }
}

function update_product($conn) {
    $id = $_POST['edit_id']; // Use edit_id for updating
    $name = $_POST['name'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];

    // Prepare the update statement
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image["name"]);

        if ($image['type'] == 'image/jpeg') {
            if (move_uploaded_file($image["tmp_name"], $target_file)) {
                // Update with new image
                $stmt = $conn->prepare("UPDATE products SET name=?, category=?, stock=?, price=?, image=? WHERE id=?");
                $stmt->bind_param("ssissi", $name, $category, $stock, $price, $target_file, $id);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Only JPG files are allowed']);
            exit();
        }
    } else {
        // Update without new image
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, stock=?, price=? WHERE id=?");
        $stmt->bind_param("ssisi", $name, $category, $stock, $price, $id);
    }

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No changes made or invalid ID']);
    }
    exit(); // Stop further execution
}

function delete_product($conn) {
    // Decode the raw POST data
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['id'])) {
        $id = $data['id'];

        $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID or no rows affected']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID not provided']);
    }
    exit(); // Stop further execution
}

$conn->close();
?>
