<?php
session_start();

// 1. Session check
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'accounting') {
    header("Location: stafflogin.php");
    exit;
}

// 2. Database Connection
$conn = new mysqli("localhost", "root", "", "sunodka_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ==========================================================
// DATA FETCHING FOR HISTORY PAGE
// ==========================================================

// Fetch all records for the history table (including 'Done')
// It's usually best to fetch done records from the last few days/weeks for performance,
// but for now, we'll fetch all unless a specific filter is applied.
$sql = "
    SELECT * FROM queue 
    WHERE department='Accounting' 
";
$params = [];
$types = "";

// Optional: Filter by Status (Done, Skipped, Serving, Pending)
if (isset($_GET['status']) && $_GET['status'] != '') {
    $status = $_GET['status'];
    $sql .= " AND status = ?";
    $types .= "s";
    $params[] = $status;
}

// Order by most recent completion or creation date
$sql .= " ORDER BY created_at DESC";

// Use prepared statement for secure filtering (even though $_GET is simple here, it's a good habit)
if ($types) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = $conn->query($sql);
}

// Close connection at the very end
// $conn->close() will be added at the end of the HTML structure
?>
<!DOCTYPE html>
<html>
<head>
    <title>Accounting History</title>
    <link rel="stylesheet" href="accounting_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Optional: Add specific styles for the history table if needed, 
           but we'll reuse accounting_dashboard.css for consistency. */
        .history-status-done { background-color: #e6ffe6; color: #1c7430; font-weight: bold; }
        .history-status-skipped { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        
        <div class="sidebar">
            <h2>Accounting Panel</h2>
            
            <nav class="sidebar-nav">
                <a href="accounting_dashboard.php" class="nav-item"><i class="fas fa-list-alt"></i> Active Queue</a>
                <a href="accounting_history.php" class="nav-item active"><i class="fas fa-history"></i> Request History</a>
                
                <div class="nav-item filter-section">
                    <label for="filterStatus"><i class="fas fa-filter"></i> Filter Status:</label>
                    <select id="filterStatus" onchange="window.location.href='?status=' + this.value;">
                        <option value="">All History</option>
                        <option value="Done">Done</option>
                        <option value="Skipped">Skipped</option>
                        <option value="Serving">Serving</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
            </nav>
            
            <a href="logout.php" class="logout" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a>

            <div class="custom-modal" id="logoutModal">
                <div class="modal-content">
                    <h2>🚪 Confirm Logout</h2>
                    <p>Are you sure you want to end your session?</p>
                    <div class="modal-actions">
                        <button class="btn btn-ghost" id="cancelLogout">Cancel</button>
                        <a href="logout.php" class="btn done" id="proceedLogout">Log Out →</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="main-content">
            <h1>Queue Request History</h1>

            <table>
                <tr>
                    <th>Queue #</th>
                    <th>Full Name</th>
                    <th>Course</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Finished/Updated At</th>
                </tr>

                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): 
                        $status_class = strtolower(str_replace(' ', '-', $row['status']));
                    ?>
                    <tr>
                        <td><?php echo str_pad($row['queue_number'], 3, "0", STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td><?php echo htmlspecialchars($row['service_type']); ?></td>
                        
                        <td class="<?php echo 'history-status-' . $status_class; ?>"><?php echo htmlspecialchars($row['status']); ?></td>
                        
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['updated_at'] ?? 'N/A'); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No history records found for Accounting.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
        
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Logout Modal Logic (reused from dashboard)
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
        
        // Pre-select filter dropdown if a status is in the URL
        const urlParams = new URLSearchParams(window.location.search);
        const statusFilter = urlParams.get('status');
        const filterSelect = document.getElementById('filterStatus');
        if (statusFilter && filterSelect) {
            filterSelect.value = statusFilter;
        }
    });
</script>

</body>
</html>
<?php $conn->close(); ?>