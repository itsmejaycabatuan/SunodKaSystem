<?php
session_start();

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
// --- AUTHENTICATION/CONNECTION (Keep original code here) ---
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
header("Location: staff-login.php");
exit;
}

$conn = new mysqli("localhost", "root", "", "sunodka_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// --- Handle ADD STAFF, DELETE STAFF, UPDATE STAFF, LOGOUT (Keep original code here) ---
// ... (existing handlers remain unchanged) ...

if (isset($_POST['add_staff'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password']; 
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

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM tbl_staff WHERE staff_id=$id");
    header("Location: admin_dashboard.php?page=staff");
    exit;
}

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
// --- end of original code ---


// --- DATA FETCHING FOR ALL PAGES ---

// Home Page Data
if ($page == 'home') {
    // Fetch today's summary statistics across all departments
    $statsQuery = $conn->query("
        SELECT 
            SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) AS served_count,
            SUM(CASE WHEN status='Serving' THEN 1 ELSE 0 END) AS active_count,
            SUM(CASE WHEN status='Done' THEN 1 ELSE 0 END) AS total_count
        FROM queue 
        WHERE DATE(created_at) = CURDATE()
    ");
    
    if ($statsQuery && $statsQuery->num_rows > 0) {
        $stats = $statsQuery->fetch_assoc();
    } else {
        $stats = ['served_count' => 0, 'active_count' => 0, 'skipped_count' => 0, 'total_count' => 0];
    }

    $deptQuery = $conn->query("
        SELECT 
            department, 
            SUM(CASE WHEN status='Done' THEN 1 ELSE 0 END) AS total_documents 
        FROM queue 
        WHERE department IN ('Registrar','Accounting') AND DATE(created_at) = CURDATE()
        GROUP BY department
    ");
}

// Global Monitor Data
if ($page == 'monitor') {
    // NEW QUERY: Fetch all active (Pending or Serving) requests across all departments
    $monitorQuery = $conn->query("
        SELECT 
            queue_number, 
            full_name, 
            department, 
            service_type, 
            status, 
            created_at 
        FROM queue 
        WHERE status IN ('Pending', 'Serving') 
        ORDER BY department ASC, created_at ASC
    ");
}

// --- Determine current page ---
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link rel="stylesheet" href="admin_dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
<style>
/* Basic styling for high-priority statuses */
.status-Pending {
    font-weight: bold;
    color: #FF9800; /* Orange */
}
.status-Serving {
    font-weight: bold;
    color: #4CAF50; /* Green */
}
</style>
</head>
<body>

<div class="sidebar">
<h2><i class="fas fa-tools"></i> Admin Panel</h2>
<a href="?page=home" class="<?= $page=='home'?'active':'' ?>"><i class="fas fa-home"></i> Home</a>
<a href="?page=monitor" class="<?= $page=='monitor'?'active':'' ?>"><i class="fas fa-eye"></i> Global Monitor</a> <a href="?page=staff" class="<?= $page=='staff'?'active':'' ?>"><i class="fas fa-users"></i> Manage Staff</a>
<a href="?page=services" class="<?= $page=='services'?'active':'' ?>"><i class="fas fa-cogs"></i> Configure Services</a>
<a href="?page=stats" class="<?= $page=='stats'?'active':'' ?>"><i class="fas fa-chart-bar"></i> Queue Statistics</a>

<form method="POST">
<button type="submit" name="logout">
<i class="fas fa-sign-out-alt"></i> Logout
</button>
</form>
</div>

<div class="main">
<h1>Welcome, Admin</h1>

<?php if ($page == 'home'): ?>
<div class="welcome-banner section">
 <h2>👋 Good Day!</h2>
<p>This is your operational overview for Today's queue activity. Monitor active requests, staff efficiency, and system configuration from this panel.</p>
 </div>

<div class="stats-grid">

<div class="stat-card served-card">
 <i class="fas fa-check-circle card-icon"></i>
 <span class="stat-value"><?= $stats['served_count'] ?></span>
<span class="stat-label">Requests Served Today (PENDING)</span>
 </div>

<div class="stat-card active-card">
 <i class="fas fa-clock card-icon"></i>
<span class="stat-value"><?= $stats['active_count'] ?></span>
<span class="stat-label">Active Requests (SERVING)</span>
</div>

 <div class="stat-card total-card">
<i class="fas fa-list-ol card-icon"></i>
 <span class="stat-value"><?= $stats['total_count'] ?></span>
<span class="stat-label">Total Requests Today (DONE)</span>
 </div>

 </div>

 <div class="section">
<h2>Today's Served Documents by Department</h2>
<table>
<tr>
<th>Department</th>
<th>Total Served Count</th>
</tr>
<?php
while ($row = $deptQuery->fetch_assoc()) {
echo "<tr><td>".htmlspecialchars($row['department'])."</td><td>".$row['total_documents']."</td></tr>";
}
?>
</table>
</div>

<?php elseif ($page == 'monitor'): ?>
<div class="section">
    <h2><i class="fas fa-eye"></i> Global Active Queue Monitor</h2>
    <p>Displays all queue numbers currently waiting (Pending) or being served (Serving) across all departments.</p>

    <?php if ($monitorQuery->num_rows > 0): ?>
    <table>
        <tr>
            <th>Queue #</th>
            <th>Department</th>
            <th>Full Name</th>
            <th>Service</th>
            <th>Status</th>
            <th>Time Since Created</th>
        </tr>
        <?php while($row = $monitorQuery->fetch_assoc()): 
            // Calculate time difference for 'Time Since Created'
            $created_timestamp = strtotime($row['created_at']);
            $time_diff = time() - $created_timestamp;
            $minutes = floor($time_diff / 60);
            $seconds = $time_diff % 60;
            $time_display = "$minutes min $seconds sec";
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row['queue_number']); ?></td>
            <td><?php echo htmlspecialchars($row['department']); ?></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['service_type']); ?></td>
            <td class="status-<?php echo $row['status']; ?>"><?php echo htmlspecialchars($row['status']); ?></td>
            <td><?php echo $time_display; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p style="padding: 15px; border: 1px solid #ccc; background-color: #f9f9f9;">
            <i class="fas fa-check-circle" style="color: #4CAF50;"></i> The queue is currently empty across all departments.
        </p>
    <?php endif; ?>
</div>
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
<option value="registrar">Registrar</option>
<option value="accounting">Accounting</option>
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
<option value="registrar" <?= ($row['role']=="registrar")?"selected":"" ?>>Registrar</option>
<option value="accounting" <?= ($row['role']=="accounting")?"selected":"" ?>>Accounting</option>
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