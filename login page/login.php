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

    // Siapkan dan bind
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verifikasi password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username; // Simpan username di session
            $success_message = "Login berhasil!";
            // Redirect ke halaman login dan tambahkan parameter untuk menampilkan alert
            header("Location: login.php?success=1");
            exit();
        } else {
            $error_message = "Password tidak valid.";
        }
    } else {
        $error_message = "Pengguna tidak ditemukan.";
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
    <title>Login</title>
</head>
<body>
    <div class="d-lg-flex half">
      <div class="bg order-1 order-md-1" style="background-image: url('images/bg_1.jpg');"></div>
      <div class="contents order-2 order-md-2">
        <div class="container form-container">
          <div class="row align-items-center justify-content-center">
            <div class="col-md-12">
              <h3>Login</h3>
              <p class="mb-4">Lorem ipsum dolor sit amet elit. Sapiente sit aut eos consectetur adipisicing.</p>
              <form action="" method="post">
                <div class="form-group first">
                  <label for="username">Username</label>
                  <input type="text" class="form-control" placeholder="your-email@gmail.com" name="username" required>
                </div>
                <div class="form-group last mb-3">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" placeholder="Your Password" name="password" required>
                </div>
                
                <div class="d-flex mb-5 align-items-center">
                  <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>
                    <input type="checkbox" checked="checked"/>
                    <div class="control__indicator"></div>
                  </label>
                  <span class="ml-auto"><a href="#" class="forgot-pass">Forgot Password</a></span> 
                </div>

                <input type="submit" value="Log In" class="btn btn-block btn-primary">
              </form>

              <?php if (isset($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>

              <!-- Create Account Section -->
              <div class="create-account">
                <p>Don't have an account? <a href="create_account.php">Create Account</a></p>
              </div>
              <!-- End of Create Account Section -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script> <!-- SweetAlert JS -->
    
    <script>
      // Cek jika parameter success ada di URL
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('success')) {
        swal("Berhasil!", "Login berhasil!", "success");
      }
    </script>
</body>
</html>
