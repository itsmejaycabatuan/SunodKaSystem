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
} else {
  $new_queue_number = 1;
}
$stmt->close();

// Insert new request
$stmt = $conn->prepare("INSERT INTO queue 
  (student_id, full_name, course, service_type, department, request_details, queue_number, status, created_at)
  VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())");
$stmt->bind_param("ssssssi", $student_id, $full_name, $course_year, $service_type, $department, $details, $new_queue_number);
$stmt->execute();
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

    <div class="queue-number">
      <?= str_pad($new_queue_number, 3, "0", STR_PAD_LEFT); ?>
    </div>

    <p>Estimated wait: ~<?= $new_queue_number * 5; ?> minutes</p>

    <a href="services.html" class="btn" onclick="return warnBeforeBack()">← Back to Services</a>
  </div>

  <script>
  function warnBeforeBack() {
      const confirmLeave = confirm(
          "Please secure a screenshot of your queue slip before making another transaction. Click 'Cancel' if not done. Click 'Okay' if you already saved your queue slip."
      );
      return confirmLeave; 
  }
  </script>
</body>
</html>
