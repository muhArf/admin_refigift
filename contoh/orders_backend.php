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
        $orders[] = $row;
    }

    echo json_encode($orders);
    exit(); // Hentikan eksekusi lebih lanjut
}

function add_order($conn) {
    $customerName = $_POST['customerName'];
    $productName = $_POST['productName'];
    $jumlah = $_POST['jumlah'];
    $totalHarga = $_POST['totalHarga'];
    $tanggalOrder = $_POST['tanggalOrder'];
    $statusPembayaran = $_POST['statusPembayaran'];
    $statusValidasi = $_POST['statusValidasi'];
    $statusPemesanan = $_POST['statusPemesanan'];

    $stmt = $conn->prepare("INSERT INTO orders (customer_name, product_name, jumlah, total_harga, tanggal_order, status_pembayaran, status_validasi, status_pemesanan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisisss", $customerName, $productName, $jumlah, $totalHarga, $tanggalOrder, $statusPembayaran, $statusValidasi, $statusPemesanan);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add order']);
    }
    exit(); // Hentikan eksekusi lebih lanjut
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
