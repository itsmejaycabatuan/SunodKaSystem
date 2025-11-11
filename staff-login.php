<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // check credentials for log in
  $sql = "SELECT * FROM tbl_staff WHERE username='$username' AND password='$password'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['username'] = $row['username'];
    $_SESSION['role'] = $row['role'];

    // direct based sa ilahang role
    if ($row['role'] == 'registrar') {
      header("Location: registrar_dashboard.php");
      exit;
    } elseif ($row['role'] == 'accounting') {
      header("Location: accounting_dashboard.php");
      exit;
    } elseif ($row['role'] == 'admin') {
      header("Location: admin_dashboard.php");
      exit;
    }
  } else {
    $error = "Invalid username or password!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SunodKa - Staff Login</title>
  <link rel="stylesheet" href="stafflogin.css">
</head>
<body>
  <div class="navbar">
    <div class="first-logo">
      <img src="images/logo.png" alt="systemlogo" class="logo">
    </div>
    <div class="text-center">
      <p class="location">St.Cecilia's College-Cebu, Inc.<br>
        Supervised by the Lasallian Schools Supervision Office (LASSO)<br>
        Cebu South National Highway, Ward II, Minglanilla, Cebu.
      </p>
    </div>
    <div class="second-logo">
      <img src="images/itlogo.png" alt="BSIT" class="itlogo">
    </div>
  </div>

  <div class="login-box">
    <h2>Staff / Admin Login</h2>
    <form method="POST" action="">
      <input type="text" name="username" placeholder="Username" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <button type="submit">Login</button>
      <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </form>
  </div>
</body>
</html>
