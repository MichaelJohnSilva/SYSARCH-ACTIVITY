<?php
session_start();
include "config.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.html");
    exit();
}

/* FETCH STUDENTS */
$students = $conn->query("SELECT * FROM students WHERE course != 'Administrator' ORDER BY id_number ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Students</title>

<link rel="stylesheet" href="styles.css">

<style>

body{
font-family:Arial;
background:#f5f5f5;
margin:0;
}

/* NAVBAR */
.topnav{
display:flex;
justify-content:space-between;
align-items:center;
background:#a0a0a0;
padding:10px 20px;
color:white;
}

.topnav ul{
list-style:none;
display:flex;
gap:15px;
}

.topnav ul li a{
color:white;
text-decoration:none;
}



/* If you have a specific class for logout button */
.logout-btn {
  color: white;
  padding: 5px 10px;
  border-radius: 4px;
  text-decoration: none;
  transition: background-color 0.3s ease;
}

.logout-btn:hover {
  color: #fff;
  cursor: pointer;
}

.topnav a[href="logout.php"] {
  color: white;
  padding: 5px 10px;
  border-radius: 4px;
  text-decoration: none;
  transition: background-color 0.3s ease;
}

.topnav a[href="logout.php"]:hover {
  background-color: #23211e;
  cursor: pointer;
}

/* PAGE */
.container{
width:90%;
margin:auto;
margin-top:20px;
}

h1{
text-align:center;
}

/* BUTTONS */

.btn{
padding:8px 12px;
border:none;
border-radius:4px;
cursor:pointer;
color:white;
}

.btn-add{
background:#0d6efd;
}

.btn-reset{
background:#dc3545;
}

/* TABLE */

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
background:white;
}

table th, table td{
padding:10px;
border-bottom:1px solid #ddd;
text-align:center;
}

table th{
background:#f2f2f2;
}

/* ACTION BUTTONS */

.edit{
background:#0d6efd;
}

.delete{
background:#dc3545;
}

.search-box{
float:right;
margin-top:-35px;
}

</style>
</head>

<body>

<div class="topnav">

<div>College of Computer Studies Admin</div>

<ul>
<li><a href="admin_dashboard.php">Home</a></li>
<li><a href="#">Search</a></li>
<li><a href="students.php">Students</a></li>
<li><a href="#">Sit-in</a></li>
<li><a href="#">View Sit-in Records</a></li>
<li><a href="#">Sit-in Reports</a></li>
<li><a href="#">Feedback Reports</a></li>
<li><a href="#">Reservation</a></li>
<li><a class="logout" href="logout.php">Log out</a></li>
</ul>

</div>


<div class="container">

<h1>Students Information</h1>

<button class="btn btn-add">Add Students</button>
<button class="btn btn-reset">Reset All Session</button>

<div class="search-box">
Search: <input type="text">
</div>

<table>

<tr>
<th>ID Number</th>
<th>Name</th>
<th>Year Level</th>
<th>Course</th>
<th>Remaining Session</th>
<th>Actions</th>
</tr>

<?php while($row = $students->fetch_assoc()){ ?>

<tr>

<td><?php echo $row['id_number']; ?></td>

<td>
<?php
echo $row['first_name']." ".
$row['middle_name']." ".
$row['last_name'];
?>
</td>

<td><?php echo $row['year_level'] ?? "-"; ?></td>

<td><?php echo $row['course']; ?></td>

<td><?php echo $row['sessions_remaining'] ?? 30; ?></td>

<td>

<button class="btn edit">Edit</button>

<button class="btn delete">Delete</button>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>