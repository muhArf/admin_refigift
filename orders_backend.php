<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost"; // Ganti jika database Anda dihosting di tempat lain
$username = "root"; // Username database Anda
$password = ""; // Password database Anda
$dbname = "refigift_db"; // Nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Menangani berbagai permintaan
$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        fetch_orders($conn);
        break;
    case 'POST':
        add_order($conn);
        break;
    case 'DELETE':
        delete_order($conn);
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
        exit();
}

function fetch_orders($conn) {
    $query = "SELECT * FROM orders";
    $result = $conn->query($query);
    $orders = array();

    while ($row = $result->fetch_assoc()) {
        // Format tanggal
        $tanggal = new DateTime($row['tanggal_order']);
        $row['tanggal_order'] = $tanggal->format('d F Y'); // Format: 08 Oktober 2025
        $orders[] = $row;
    }

    echo json_encode($orders);
    exit(); // Hentikan eksekusi lebih lanjut
}

function add_order($conn) {
    // Read the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $customerName = $input['customerName'];
    $alamat = $input['alamat'];
    $orderItems = $input['orderItems'];

    foreach ($orderItems as $item) {
        $productName = $item['product'];
        $jumlah = $item['quantity'];
        $totalHarga = $item['quantity'] * getProductPrice($productName, $conn); // Calculate total price based on quantity

        $statusPembayaran = $input['statusPembayaran'];
        $statusValidasi = $input['statusValidasi'];
        $statusPemesanan = $input['statusPemesanan'];

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, alamat, product_name, jumlah, total_harga, tanggal_order, status_pembayaran, status_validasi, status_pemesanan) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)");
        $stmt->bind_param("sssiisss", $customerName, $alamat, $productName, $jumlah, $totalHarga, $statusPembayaran, $statusValidasi, $statusPemesanan);
        
        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
            exit();
        }
    }

    echo json_encode(['status' => 'success']);
    exit(); // Hentikan eksekusi lebih lanjut
}

function getProductPrice($productName, $conn) {
    $stmt = $conn->prepare("SELECT price FROM products WHERE name = ?");
    $stmt->bind_param("s", $productName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['price'];
}

function delete_order($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
    exit(); // Hentikan eksekusi lebih lanjut
}

$conn->close();
?>
