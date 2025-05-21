<<<<<<< HEAD
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
        fetch_products($conn);
        break;
    case 'POST':
        if (isset($_POST['id'])) {
            update_product($conn); // Call update function for POST with ID
        } else {
            add_product($conn); // Call add function for POST without ID
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
    $id = $_POST['id'];
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
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
    exit(); // Stop further execution
}

$conn->close();
?>
=======
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
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];

    // Handle image upload if a new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image["name"]);

        // Check if the image is a valid JPG file
        if ($image['type'] == 'image/jpeg') {
            if (move_uploaded_file($image["tmp_name"], $target_file)) {
                // Update the product with new image
                $stmt = $conn->prepare("UPDATE products SET name=?, category=?, stock=?, price=?, image=? WHERE id=?");
                $stmt->bind_param("ssissi", $name, $category, $stock, $price, $target_file, $id);
                $stmt->execute();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
                return;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Only JPG files are allowed']);
            return;
        }
    } else {
        // Update the product without changing the image
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, stock=?, price=? WHERE id=?");
        $stmt->bind_param("ssisi", $name, $category, $stock, $price, $id);
        $stmt->execute();
    }

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
>>>>>>> f304a262b3158fe716b5d9df3b6cd87a847c7a82
