<?php
session_start();

// --- Authentication ---
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'registrar') {
    header("Location: stafflogin.php");
    exit;
}

// Database Connection
$conn = new mysqli("localhost", "root", "", "sunodka_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Department filter
$department = 'Registrar';

// --- Data Fetching: History List ---
// Fetch all entries for Registrar that are either Done or Skipped
$sql = "
    SELECT * FROM queue 
    WHERE department='{$department}' 
    AND status IN ('Done', 'Skipped')
    ORDER BY created_at DESC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrar History</title>
    <link rel="stylesheet" href="registrar_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    
<div class="dashboard-container">
    
    <div class="sidebar">
        <h2>Registrar Panel</h2>
        
        <nav class="sidebar-nav">
            <a href="registrar_dashboard.php" class="nav-item"><i class="fas fa-list-alt"></i> Active Queue</a>
            <a href="registrar_history.php" class="nav-item active"><i class="fas fa-history"></i> Request History</a>
            </nav>
        
        <a href="logout.php" class="logout" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        
        <div class="custom-modal" id="logoutModal">
            <div class="modal-content">
                <h2>🚪 Confirm Logout</h2>
                <p>Are you sure you want to end your session?</p>
                <div class="modal-actions">
                    <button class="btn btn-ghost" id="cancelLogout">Cancel</button>
                    <a href="stafflogin.php" class="btn done" id="proceedLogout">Log Out →</a>
                </div>
            </div>
        </div>
        
    </div>
    
    <div class="main-content">
        <h1>Registrar Request History</h1>

        <table>
            <tr>
                <th>Queue #</th>
                <th>Full Name</th>
                <th>Course</th>
                <th>Service</th>
                <th>Details</th>
                <th>Status</th>
                <th>Timestamp</th>
            </tr>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo str_pad($row['queue_number'], 3, "0", STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                    <td><?php echo htmlspecialchars($row['service_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['request_details']); ?></td>
                    
                    <td class="status-<?php echo strtolower($row['status']); ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </td>
                    
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No history records found for the Registrar Department.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
    
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Logout Modal Logic
        const logoutBtn = document.getElementById('logoutBtn');
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogout = document.getElementById('cancelLogout');

        if(logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                logoutModal.style.display = 'flex';
            });
        }
        
        if(cancelLogout) {
            cancelLogout.addEventListener('click', () => {
                logoutModal.style.display = 'none';
            });
        }

        window.addEventListener('click', (event) => {
            if (event.target === logoutModal) {
                logoutModal.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
<?php $conn->close(); ?>