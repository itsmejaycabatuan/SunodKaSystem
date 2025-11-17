<?php
// DB connection
$conn = new mysqli("localhost", "root", "", "sunodka_db");
if ($conn->connect_error) {

die("Connection failed: " . $conn->connect_error);
}

$details = "";

// field gikan sa student
$student_id = !empty($_POST['student_id']) ? $_POST['student_id'] : "N/A";
$full_name = $_POST['full_name'];
$course_year = $_POST['course_year'];
$service_type = $_POST['service_type'];


if (in_array($service_type, ['Tuition Balance', 'Financial Clearance', 'Statement of Account'])) {
$department = "Accounting";
} else {
$department = "Registrar";
}

//details kada service type
if ($service_type == "Transcript of Records") {
$copies = $_POST['copies'];
$purpose = $_POST['purpose'];
$notes = isset($_POST['notes']) ? $_POST['notes'] : "None";
$details = "Copies: $copies, Purpose: $purpose, Notes: $notes";
} 
elseif ($service_type == "Certificate of Grades") {
$semester = $_POST['semester'];
$sy = $_POST['sy'];
$purpose = $_POST['purpose'];
$notes = isset($_POST['notes']) ? $_POST['notes'] : "None";
$copies = $_POST['copies'];
$details = "Semester: $semester, SY: $sy, Copies: $copies, Purpose: $purpose, Notes: $notes";
} 
elseif ($service_type == "Certificate of Enrollment") {
$term = $_POST['term'];
$copies = $_POST['copies'];
$details = "Term: $term, Copies: $copies";
} 
elseif ($service_type == "Good Moral Certificate") {
$purpose = $_POST['purpose'];
$details = "Purpose: $purpose";
} 
elseif ($service_type == "Accounting Clearance") {
$remarks = $_POST['remarks'];
$details = "Remarks: $remarks";
} 
elseif ($service_type == "Tuition Balance") {
$semester = $_POST['semester'];
$remarks = $_POST['remarks'];
$details = "Semester: $semester, Remarks: $remarks";
} 
elseif ($service_type == "Financial Clearance") {
$purpose = $_POST['purpose'];
$details = "Purpose: $purpose";
} 
elseif ($service_type == "Statement of Account") {
$remarks = $_POST['remarks'];
$details = "Remarks: $remarks";
}

// Get last queue number per DEPARTMENT
$sql = "SELECT queue_number FROM queue 
WHERE department = ? 
ORDER BY queue_number DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
$row = $result->fetch_assoc();
$new_queue_number = $row['queue_number'] + 1;
} else { $new_queue_number = 1;
}
$stmt->close();

// Insert new request
$stmt = $conn->prepare("INSERT INTO queue 
(student_id, full_name, course, service_type, department, request_details, queue_number, status, created_at)
VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())");
$stmt->bind_param("ssssssi", $student_id, $full_name, $course_year, $service_type, $department, $details, $new_queue_number);
$stmt->execute();

// --- NEW CODE BLOCK: Get the created_at time ---
$insert_id = $conn->insert_id; // Get the ID of the newly inserted row
$time_sql = "SELECT created_at FROM queue WHERE queue_id = ?"; // Assuming 'id' is your primary key
$time_stmt = $conn->prepare($time_sql);
$time_stmt->bind_param("i", $insert_id);
$time_stmt->execute();
$time_result = $time_stmt->get_result();

$creation_time = "N/A";
if ($time_result->num_rows > 0) {
    $time_row = $time_result->fetch_assoc();
    // Format the time for display (e.g., "Nov 15, 2025 08:47 PM")
    $creation_time = date('M d, Y h:i A', strtotime($time_row['created_at']));
}
$time_stmt->close();
// --- END NEW CODE BLOCK ---

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Queue Number</title>
<link rel="stylesheet" href="submit_request.css">

</head>
<body>
<div class="queue-card">
<h1>Queue Number</h1>
<p><strong>Name:</strong> <?= htmlspecialchars($full_name) ?></p>
<p><strong>Course & Year:</strong> <?= htmlspecialchars($course_year) ?></p>
<p><strong>Student ID:</strong> <?= !empty($student_id) ? htmlspecialchars($student_id) : "N/A" ?></p>
<p><strong>Department:</strong> <?= htmlspecialchars($department) ?></p>
<p><strong>Service:</strong> <?= htmlspecialchars($service_type) ?></p>
<p><strong>Details:</strong> <?= htmlspecialchars($details) ?></p>
<p><strong>Date & Time:</strong> <?= htmlspecialchars($creation_time) ?></p>

<div class="queue-number">
<?= str_pad($new_queue_number, 3, "0", STR_PAD_LEFT); ?>
</div>

<p>Estimated wait: ~<?= $new_queue_number * 5; ?> minutes</p>

<a href="#" class="btn" id="backToServicesBtn">← Back to Services</a>
</div>

<div class="custom-modal" id="warningModal">
<div class="modal-content">
<h2>⚠️ Confirmation Required</h2>
<p>Please secure a screenshot or capture of your queue slip before making another transaction.</p>
<p class="modal-instruction">Click **'Proceed'** if you have already saved your queue slip. Click **'Cancel'** to return and save it.</p>
<div class="modal-actions">
<button class="btn btn-ghost" id="cancelBtn">Cancel</button>
<a href="services.html" class="btn btn-primary" id="proceedBtn">Proceed →</a>
</div>
</div>
</div>

<script>
 document.addEventListener('DOMContentLoaded', () => {
const modal = document.getElementById('warningModal');
const backBtn = document.getElementById('backToServicesBtn');
const cancelBtn = document.getElementById('cancelBtn');

// Show modal when 'Back to Services' is clicked
backBtn.addEventListener('click', (e) => {
e.preventDefault();
modal.style.display = 'flex';
});

 // Hide modal when 'Cancel' is clicked
cancelBtn.addEventListener('click', () => {
modal.style.display = 'none';
 });

 // Hide modal when clicking outside (optional)
 window.addEventListener('click', (event) => {
 if (event.target === modal) {
 modal.style.display = 'none';
 }
 });
 });
 </script>
</body>
</html>