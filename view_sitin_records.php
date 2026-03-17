<?php
session_start();
include "config.php";

// Only admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.html");
    exit();
}

// Fetch all sit-ins with student info
$result = $conn->query("
SELECT s.id, s.id_number, s.purpose, s.lab, s.status, s.time_in, s.time_out,
       st.first_name, st.last_name, st.sessions_remaining
FROM sitin_records s
LEFT JOIN students st ON s.id_number = st.id_number
ORDER BY s.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Sit-In Records</title>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- Custom Admin CSS -->
<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    margin: 0;
}

/* NAVBAR */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #a0a0a0;
    padding: 12px 20px;
    color: white;
}

.navbar .logo {
    font-weight: bold;
    font-size: 18px;
}

.navbar ul {
    list-style: none;
    display: flex;
    gap: 15px;
    margin: 0;
    padding: 0;
}

.navbar ul li a {
    color: white;
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 5px;
    transition: 0.3s;
}

.navbar ul li a:hover {
    background: #575757;
}

/* Container */
.container {
    max-width: 1200px;
    margin: 30px auto;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Table */
table.dataTable {
    width: 100% !important;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}
</style>

<!-- jQuery & DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">College of Computer Studies Admin</div>
    <ul>
        <li><a href="admin_dashboard.php">Home</a></li>
        <li><a href="students.php">Students</a></li>
        <li><a class="active" href="view_sitin_records.php">Sit-In Records</a></li>
        <li><a href="#">Sit-In Reports</a></li>
        <li><a href="#">Feedback Reports</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="container">
    <h2>Current Sit-In Records</h2>
    <table id="sitinTable" class="display">
        <thead>
            <tr>
                <th>Sit ID</th>
                <th>ID Number</th>
                <th>Name</th>
                <th>Purpose</th>
                <th>Lab</th>
                <th>Sessions Remaining</th>
                <th>Status</th>
                <th>Time In</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['id_number']); ?></td>
                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                <td><?= htmlspecialchars($row['purpose']); ?></td>
                <td><?= htmlspecialchars($row['lab']); ?></td>
                <td><?= isset($row['sessions_remaining']) ? (int)$row['sessions_remaining'] : 0; ?></td>
                <td><?= htmlspecialchars($row['status']); ?></td>
                <td><?= $row['time_in']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- DataTables Init -->
<script>
$(document).ready(function(){
    $('#sitinTable').DataTable({
        "order": [[0, "desc"]],
        "pageLength": 10
    });
});
</script>

</body>
</html>S