<?php
session_start();

// --- authentication ---
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'registrar') {
    header("Location: stafflogin.php");
    exit;
}

// Database Connection
$conn = new mysqli("localhost", "root", "", "sunodka_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Action Handling (Skip, Serve, Done) ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    $department = 'Registrar'; // Target department

    if ($action == "skip") {
        $stmt = $conn->prepare("UPDATE queue SET status='Skipped' WHERE queue_id=? AND department=?");
    } elseif ($action == "serve") {
        $stmt = $conn->prepare("UPDATE queue SET status='Serving' WHERE queue_id=? AND department=?");
    } elseif ($action == "done") {
        $stmt = $conn->prepare("UPDATE queue SET status='Done' WHERE queue_id=? AND department=?");
    }
    
    if (isset($stmt)) {
        $stmt->bind_param("is", $id, $department);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: registrar_dashboard.php");
    exit;
}

// ==========================================================
// DATA FETCHING: Now Serving, Stats, and Queue List
// ==========================================================
$department = 'Registrar';

// 1. Get the current "Now Serving" number
$serving_result = $conn->query("
    SELECT queue_number, full_name, service_type 
    FROM queue 
    WHERE department='{$department}' 
    AND status='Serving' 
    ORDER BY created_at ASC 
    LIMIT 1
");
$now_serving = $serving_result->fetch_assoc();
$serving_number = $now_serving ? str_pad($now_serving['queue_number'], 3, "0", STR_PAD_LEFT) : 'N/A';
$serving_name = $now_serving ? htmlspecialchars($now_serving['full_name']) : 'No one';
$serving_service = $now_serving ? htmlspecialchars($now_serving['service_type']) : '—';


// 2. Get Statistics (Today's count)
$stats_result = $conn->query("
    SELECT 
        SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN status='Done' THEN 1 ELSE 0 END) AS done_count,
        SUM(CASE WHEN status='Skipped' THEN 1 ELSE 0 END) AS skipped_count,
        COUNT(*) AS total_count
    FROM queue 
    WHERE department='{$department}'
    AND DATE(created_at) = CURDATE()
");
$stats = $stats_result->fetch_assoc();


// 3. Queue Table Data (Filtering Implemented)
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "
    SELECT * FROM queue 
    WHERE department='{$department}' 
    AND status != 'Done'
";

if (!empty($filter_status) && $filter_status != 'All Active') {
    $sql .= " AND status = '" . $conn->real_escape_string($filter_status) . "'";
}

$sql .= " ORDER BY queue_number ASC";

// Execute the final query
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrar Dashboard</title>
    <link rel="stylesheet" href="registrar_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    
<div class="dashboard-container">
    
    <div class="sidebar">
        <h2>Registrar Panel</h2>
        
        <div class="card now-serving-card">
            <h3><i class="fas fa-bullhorn"></i> Now Serving</h3>
            <div class="serving-number-large"><?= $serving_number; ?></div>
            <p><strong><?= $serving_name; ?></strong></p>
            <p><small><?= $serving_service; ?></small></p>
        </div>
        
        <div class="card stats-card">
            <h3><i class="fas fa-chart-line"></i> Today's Stats</h3>
            <div class="stat-grid">
                <div>
                    <span class="stat-value"><?= $stats['pending_count'] ?? 0; ?></span>
                    <span class="stat-label">Pending</span>
                </div>
                <div>
                    <span class="stat-value"><?= $stats['done_count'] ?? 0; ?></span>
                    <span class="stat-label">Served (Done)</span>
                </div>
                <div>
                    <span class="stat-value"><?= $stats['skipped_count'] ?? 0; ?></span>
                    <span class="stat-label">Skipped</span>
                </div>
                <div>
                    <span class="stat-value total"><?= $stats['total_count'] ?? 0; ?></span>
                    <span class="stat-label">Total Requests</span>
                </div>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <a href="registrar_dashboard.php" class="nav-item active"><i class="fas fa-list-alt"></i> Active Queue</a>
            <a href="registrar_history.php" class="nav-item"><i class="fas fa-history"></i> Request History</a>
            <div class="nav-item filter-section">
                <label for="filterStatus"><i class="fas fa-filter"></i> Filter Status:</label>
                <select id="filterStatus" onchange="window.location.href='?status=' + this.value;">
                    <option value="" <?= $filter_status == '' ? 'selected' : ''; ?>>All Active</option>
                    <option value="Pending" <?= $filter_status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Serving" <?= $filter_status == 'Serving' ? 'selected' : ''; ?>>Serving</option>
                    <option value="Skipped" <?= $filter_status == 'Skipped' ? 'selected' : ''; ?>>Skipped</option>
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
                    <a href="staff-login.php" class="btn done" id="proceedLogout">Log Out →</a>
                </div>
            </div>
        </div>
        
    </div>
    
    <div class="main-content">
        <h1>Registrar Queue Dashboard</h1>

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
                    <?php if ($row['status'] != 'Done'): ?>
                        <a href="?action=serve&id=<?php echo $row['queue_id']; ?>" class="btn serve">Serve</a>
                        <a href="?action=skip&id=<?php echo $row['queue_id']; ?>" class="btn skip">Skip</a>
                    <?php endif; ?>
                    
                    <?php if ($row['status'] == 'Serving'): ?>
                        <a href="?action=done&id=<?php echo $row['queue_id']; ?>" class="btn done">Done</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
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