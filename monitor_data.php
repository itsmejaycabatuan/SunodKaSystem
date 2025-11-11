<?php
$conn = new mysqli("localhost", "root", "", "sunodka_db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get current serving for each department
$data = [];

$departments = ['Registrar', 'Accounting'];
foreach ($departments as $dept) {
  $sql = "SELECT queue_number, full_name 
          FROM queue 
          WHERE department='$dept' AND status='Serving'
          ORDER BY queue_number ASC LIMIT 1";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $data[$dept] = $row ? $row : null;
}

echo json_encode($data);
$conn->close();
?>
