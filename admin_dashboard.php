<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    // header("Location: stafflogin.php");
    // exit;
}

$conn = new mysqli("localhost", "root", "", "sunodka_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// --- Handle ADD STAFF ---
if (isset($_POST['add_staff'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    if (!empty($username) && !empty($_POST['password'])) {
        $stmt = $conn->prepare("INSERT INTO tbl_staff (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_dashboard.php?page=staff");
    exit;
}

// --- Handle DELETE STAFF ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM tbl_staff WHERE staff_id=$id");
    header("Location: admin_dashboard.php?page=staff");
    exit;
}

// --- Handle UPDATE STAFF ---
if (isset($_POST['update_staff'])) {
    $id = intval($_POST['id']);
    $username = trim($_POST['username']);
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE tbl_staff SET username=?, password=?, role=? WHERE staff_id=?");
        $stmt->bind_param("sssi", $username, $password, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE tbl_staff SET username=?, role=? WHERE staff_id=?");
        $stmt->bind_param("ssi", $username, $role, $id);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: admin_dashboard.php?page=staff");
    exit;
}
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: staff-login.php"); // redirect to login page
    exit;
}
// --- para determine if unsa na page ang makit an ---
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
 
<link rel="stylesheet" href="admin_dashboard.css">
  
</head>
<body>

<div class="sidebar">
  <h2>Admin Panel</h2>
  <a href="?page=home" class="<?= $page=='home'?'active':'' ?>">Home</a>
  <a href="?page=staff" class="<?= $page=='staff'?'active':'' ?>">Manage Staff</a>
  <a href="?page=services" class="<?= $page=='services'?'active':'' ?>">Configure Services</a>
  <a href="?page=stats" class="<?= $page=='stats'?'active':'' ?>">Queue Statistics</a>
  
  <!-- Logout button -->
  <form method="POST" style="margin-top:330px; width: 100%;">
      <button type="submit" name="logout" style="
        width: 90%; margin: 20px 5%;
        background:#ff4b5c; color:white;
        border:none; border-radius:5px;
        padding:10px; cursor:pointer;
        font-weight:bold;
      ">Logout</button>
  </form>
</div>

<div class="main">
  <h1>Welcome, Admin</h1>

  <!-- ===== home page dapita ===== -->
  <?php if ($page == 'home'): ?>
  <div class="section">
    <h2>Total Documents Served by Department</h2>
    <table>
      <tr>
        <th>Department</th>
        <th>Total Count Served</th>
      </tr>
      <?php
   
      $deptQuery = $conn->query("
        SELECT department, COUNT(*) AS total_documents 
        FROM queue 
        WHERE department IN ('Registrar','Accounting') 
        GROUP BY department
      ");

      while ($row = $deptQuery->fetch_assoc()) {
          echo "<tr><td>".htmlspecialchars($row['department'])."</td><td>".$row['total_documents']."</td></tr>";
      }
      ?>
    </table>
  </div>

  <!-- ===== manage staff dapita ===== -->
  <?php elseif ($page == 'staff'): ?>
    <?php
      $result = $conn->query("SELECT * FROM tbl_staff WHERE role != 'admin' ORDER BY staff_id ASC");
    ?>
    <div class="section">
      <h2>Manage Staff</h2>
      <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role">
          <option value="Registrar">Registrar</option>
          <option value="Accounting">Accounting</option>
        </select>   
        <button type="submit" name="add_staff">Add Staff</button>
      </form>

      <table>
        <tr><th>ID</th><th>Username</th><th>Role</th><th>Actions</th></tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <form method="POST">
            <td><?php echo $row['staff_id']; ?><input type="hidden" name="id" value="<?php echo $row['staff_id']; ?>"></td>
            <td><input type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>"></td>
            <td>
              <select name="role">
                <option value="Registrar" <?= ($row['role']=="Registrar")?"selected":"" ?>>Registrar</option>
                <option value="Accounting" <?= ($row['role']=="Accounting")?"selected":"" ?>>Accounting</option>
              </select>
            </td>
            <td>
              <input type="password" name="password" placeholder="New Password (optional)">
              <button type="submit" name="update_staff">Update</button>
              <a href="?delete=<?php echo $row['staff_id']; ?>" onclick="return confirm('Delete this staff?')">
                <button type="button">Delete</button>
              </a>
            </td>
          </form>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>

  <!-- ===== con services ni dapita ===== -->
  <?php elseif ($page == 'services'): ?>
    <div class="section">
      <h2>Configure Services Offered</h2>
      <form>
        <input type="text" placeholder="Document Name (e.g. Transcript)">
        <button type="button">Add Document</button>
      </form>
      <table>
        <tr><th>ID</th><th>Document Name</th></tr>
        <tr><td>1</td><td>Transcript of Records</td></tr>
        <tr><td>2</td><td>Good Moral Certificate</td></tr>
      </table>
    </div>

  <!-- ===== queue statistics ni dri dapita ===== -->
  <?php elseif ($page == 'stats'): ?>
  <div class="section">
    <h2>Queue Statistics by Department</h2>
    <table>
      <tr>
        <th>Department</th>
        <th>Served</th>
        <th>Pending</th>
        <th>Skip</th>
      </tr>
      <tr>
        <td>Registrar</td>
        <td>9</td>
        <td>3</td>
        <td>2</td>
      </tr>
      <tr>
        <td>Accounting</td>
        <td>6</td>
        <td>2</td>
        <td>5</td>
      </tr>
    </table>
  </div>
  <?php endif; ?>

</div>
</body>
</html>

<?php $conn->close(); ?>
