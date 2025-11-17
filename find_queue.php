<?php
// DB connection
$conn = new mysqli("localhost", "root", "", "sunodka_db");
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}

// Get and sanitize input from the form
$queue_number = isset($_POST['queue_number']) ? (int)$_POST['queue_number'] : 0;
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';

$result_data = null;
$error_message = "";

if ($queue_number > 0 && !empty($full_name)) {
    // Query the database to find the queue slip matching the queue number AND full name
    // The full_name comparison is crucial for security and accuracy
    $sql = "SELECT student_id, full_name, course, service_type, department, request_details, queue_number, status, created_at 
            FROM queue 
            WHERE queue_number = ? AND full_name = ? AND status = 'Pending'"; // Only show active/pending queues
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $queue_number, $full_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $result_data = $result->fetch_assoc();
        $creation_time = date('M d, Y h:i A', strtotime($result_data['created_at']));
    } else {
        $error_message = "Queue Slip Not Found. Check your Queue Number and Full Name, or the request might already be Completed/Cancelled.";
    }
    $stmt->close();
} else {
    $error_message = "Please enter both a valid Queue Number and Full Name.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retrieved Queue Slip</title>
    <link rel="stylesheet" href="submit_request.css"> 
</head>
<body>
    <div class="queue-card">
        <?php if ($result_data): ?>
            <h1 style="color: green;">Queue Slip Retrieved!</h1>
            <p><strong>Name:</strong> <?= htmlspecialchars($result_data['full_name']) ?></p>
            <p><strong>Course & Year:</strong> <?= htmlspecialchars($result_data['course']) ?></p>
            <p><strong>Student ID:</strong> <?= htmlspecialchars($result_data['student_id']) ?></p>
            <p><strong>Department:</strong> <?= htmlspecialchars($result_data['department']) ?></p>
            <p><strong>Service:</strong> <?= htmlspecialchars($result_data['service_type']) ?></p>
            <p><strong>Details:</strong> <?= htmlspecialchars($result_data['request_details']) ?></p>
            <p><strong>Date & Time:</strong> <?= htmlspecialchars($creation_time) ?></p>

            <div class="queue-number">
                <?= str_pad($result_data['queue_number'], 3, "0", STR_PAD_LEFT); ?>
            </div>
            
            <p style="font-weight: bold; color: #b30000;">Status: <?= htmlspecialchars($result_data['status']) ?></p>
            <p>Estimated wait: ~<?= $result_data['queue_number'] * 5; ?> minutes</p>

        <?php else: ?>
            <h1 style="color: #b30000;">Queue Slip Error</h1>
            <p><?= htmlspecialchars($error_message) ?></p>
            <p>Please double-check the details you entered.</p>
        <?php endif; ?>

        <a href="index.html" class="btn">← Back to Home</a>
    </div>
</body>
</html>