<?php
session_start();


if (!isset($_SESSION['username']) || $_SESSION['role'] != 'accounting') {
  header("Location: stafflogin.php");
  exit;
}


$conn = new mysqli("localhost", "root", "", "sunodka_db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// mao ni to Handle actions  like kani (Skip, Serve, Done)
if (isset($_GET['action']) && isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $action = $_GET['action'];

  if ($action == "skip") {
    $conn->query("UPDATE queue SET status='Skipped' WHERE queue_id=$id AND department='Accounting'");
  } elseif ($action == "serve") {
    $conn->query("UPDATE queue SET status='Serving' WHERE queue_id=$id AND department='Accounting'");
  } elseif ($action == "done") {
    $conn->query("UPDATE queue SET status='Done' WHERE queue_id=$id AND department='Accounting'");
  }

 
  header("Location: accounting_dashboard.php");
  exit;
}

// e hide if done
$result = $conn->query("
  SELECT * FROM queue 
  WHERE department='Accounting' 
  AND status != 'Done'
  ORDER BY queue_number ASC
");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Accounting Dashboard</title>
<link rel="stylesheet" href="accounting_dashboard.css">
</head>
<body>
  <h1>Accounting Queue Dashboard</h1>

  <table>
    <tr>
      <th>Queue #</th>
      <th>Full Name</th>
      <th>Course</th>
      <th>Service</th>
      <th>Details</th>
      <th>Status</th>
      <th>Created At</th>
      <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo str_pad($row['queue_number'], 3, "0", STR_PAD_LEFT); ?></td>
        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
        
        <td><?php echo htmlspecialchars($row['course']); ?></td>
        <td><?php echo htmlspecialchars($row['service_type']); ?></td>

        <td>
          <?php echo htmlspecialchars($row['request_details']); ?>
        </td>

        <td><?php echo htmlspecialchars($row['status']); ?></td>
        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        <td>   
          <a href="?action=serve&id=<?php echo $row['queue_id']; ?>" class="btn serve">Serve</a>
          <a href="?action=skip&id=<?php echo $row['queue_id']; ?>" class="btn skip">Skip</a>
          <?php if ($row['status'] == 'Serving'): ?>
            <a href="?action=done&id=<?php echo $row['queue_id']; ?>" class="btn done">Done</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <a href="staff-login.php" class="logout">Logout</a>
</body>
</html>

<?php $conn->close(); ?>
