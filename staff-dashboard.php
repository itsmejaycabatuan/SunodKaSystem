<?php
$conn = new mysqli("localhost", "root", "", "sunodka_db");
$result = $conn->query("SELECT * FROM queue ORDER BY queue_number DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff - Registrar Queue Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="staff-dashboard.css">
</head>
<body>

<h1>Staff - Registrar Queue Dashboard</h1>

<table>
<thead>
<tr>
<th>Queue #</th>
<th>Name</th>
<th>Course & Year</th>
<th>Service</th> 
<th>Details</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr id="row-<?php echo $row['queue_id']; ?>">
<td><?php echo str_pad($row['queue_number'],3,"0",STR_PAD_LEFT); ?></td>
<td><?php echo htmlspecialchars($row['full_name']); ?></td>
<td><?php echo htmlspecialchars($row['course']); ?></td>
<td><?php echo htmlspecialchars($row['service_type']); ?></td>
<td><?php echo htmlspecialchars($row['request_details']); ?></td>
<td id="status-<?php echo $row['queue_id']; ?>"><?php echo $row['status']; ?></td>
<td>
<?php if($row['status'] === 'Pending'): ?>
<button class="btn-serve" onclick="markServed(<?php echo $row['queue_id']; ?>)">✔ Served</button>
<?php else: ?>
<span class="served-text">Served</span>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<script>
function markServed(id) {
    fetch('mark_served.php?id=' + id)
        .then(res => res.text())
        .then(data => {
            if(data === 'success'){
                document.getElementById('status-' + id).innerText = 'Served';
                document.getElementById('row-' + id).querySelector('button').outerHTML = '<span class="served-text">Served</span>';
            } else {
                alert('Failed to update status: ' + data);
            }
        });
}
</script>
<script>
// Refresh the page every 5 seconds
setInterval(() => {
    location.reload();
}, 5000); // 5000 milliseconds = 5 seconds
</script>

</body>
</html>
