<?php
// monitor_data.php

$conn = new mysqli("localhost", "root", "", "sunodka_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = [];
$departments = ['Registrar', 'Accounting'];

foreach ($departments as $dept) {
    // 1. Get current serving number
    $serving_sql = "SELECT queue_number, full_name 
                    FROM queue 
                    WHERE department='$dept' AND status='Serving'
                    ORDER BY queue_number ASC LIMIT 1";
    $serving_result = $conn->query($serving_sql);
    $serving_row = $serving_result->fetch_assoc();
    
    // 2. Get list of skipped numbers (up to 5 recent skips)
    $skipped_sql = "SELECT queue_number 
                    FROM queue 
                    WHERE department='$dept' AND status='Skipped' 
                    ORDER BY created_at DESC LIMIT 5";
    $skipped_result = $conn->query($skipped_sql);
    
    $skipped_numbers = [];
    while($row = $skipped_result->fetch_assoc()) {
        // Format the queue number to three digits
        $skipped_numbers[] = str_pad($row['queue_number'], 3, '0', STR_PAD_LEFT);
    }

    // Combine serving data and skipped data
    $data[$dept] = [
        'serving' => $serving_row ? $serving_row : null,
        'skipped_list' => $skipped_numbers
    ];
}

echo json_encode($data);
$conn->close();
?>