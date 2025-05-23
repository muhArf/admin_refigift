<?php
session_start();
$host = 'localhost'; // Ganti dengan host database Anda
$db = 'refigift_db'; // Nama database Anda
$user = 'root'; // Username database Anda
$pass = ''; // Password database Anda (kosong jika tidak ada)

// Buat koneksi
$conn = new mysqli($host, $user, $pass, $db);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Variabel untuk notifikasi
$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Siapkan dan bind
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        $success_message = "Akun berhasil dibuat!";
    } else {
        $error_message = "Gagal membuat akun: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css"> <!-- SweetAlert CSS -->
    <title>Create Account</title>
    <style>
      /* Custom styles to align form to the right */
      .contents {
        display: flex;
        justify-content: flex-start; /* Aligns content to the left */
      }
      .form-container {
        width: 100%; /* Optional: Set width to 100% to take full height */
        max-width: 400px; /* Optional: Set a max width for the form */
      }
      .create-account {
        text-align: center;
        margin-top: 20px; /* Add some space above the link */
      }
      .create-account a {
        color: #fb771a; /* Color for the link */
        text-decoration: none; /* Remove underline */
      }
      .create-account a:hover {
        text-decoration: underline; /* Underline on hover */
      }
    </style>
</head>
<body>
    <div class="d-lg-flex half">
      <div class="bg order-1 order-md-1" style="background-image: url('images/bg_1.jpg');"></div>
      <div class="contents order-2 order-md-2">
        <div class="container form-container">
          <div class="row align-items-center justify-content-center">
            <div class="col-md-12">
              <h3>Create Account</h3>
              <form action="" method="post">
                <div class="form-group first">
                  <label for="username">Username</label>
                  <input type="text" class="form-control" placeholder="Enter your username" name="username" required>
                </div>
                <div class="form-group last mb-3">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" placeholder="Enter your password" name="password" required>
                </div>

                <input type="submit" value="Create Account" class="btn btn-block btn-primary">
              </form>

              <?php if ($success_message): ?>
                <script>
                  window.onload = function() {
                    swal("Berhasil!", "<?php echo $success_message; ?>", "success").then(() => {
                      window.location.href = "/Applications/XAMPP/xamppfiles/htdocs/admin_refigift/index.html"; // Redirect ke dashboard setelah popup ditutup
                    });
                  };
                </script>
              <?php endif; ?>

              <?php if (isset($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>

              <div class="create-account">
                <p>Already have an account? <a href="login.php">Login here</a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script> <!-- SweetAlert JS -->
</body>
</html>
