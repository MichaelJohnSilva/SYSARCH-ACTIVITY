<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

/* FETCH USER DATA */
$stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

/* UPDATE PROFILE */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstName = trim($_POST['firstName']);
    $middle_name = trim($_POST['middle_name']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    $photoPath = $user['photo'] ?? '';

    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){

        $uploadDir = "uploads/";

        if(!is_dir($uploadDir)){
            mkdir($uploadDir,0777,true);
        }

        $fileName = time()."_".basename($_FILES['photo']['name']);
        $targetFile = $uploadDir.$fileName;

        if(move_uploaded_file($_FILES['photo']['tmp_name'],$targetFile)){
            $photoPath = $targetFile;
        }
    }

    $update = $conn->prepare("UPDATE students SET first_name=?, middle_name=?, last_name=?, email=?, address=?, photo=? WHERE id=?");
    $update->bind_param("ssssssi",
        $firstName,
        $middle_name,
        $lastName,
        $email,
        $address,
        $photoPath,
        $user_id
    );

    if($update->execute()){
        echo "<script>alert('Profile Updated');window.location='dashboard.php';</script>";
        exit();
    }

    $update->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles.css">

    <style>
    /* ===== DASHBOARD NAVBAR COPY ===== */
    body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #a0a0a0;
        padding: 10px 20px;
        color: white;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .navbar .logo { font-weight:bold; font-size:20px; }
    .navbar ul {
        list-style:none;
        display:flex;
        margin:0;
        padding:0;
    }
    .navbar ul li {
        margin-left:20px;
        position:relative;
    }
    .navbar ul li a {
        text-decoration:none;
        color:white;
        font-weight:500;
        padding:8px 12px;
        transition:0.3s;
    }
    .navbar ul li a:hover {
        background: rgba(255,255,255,0.2);
        border-radius:6px;
    }
    .dropdown-content {
        display:none;
        position:absolute;
        top:40px;
        right:0;
        background:#fff;
        min-width:180px;
        box-shadow:0 4px 8px rgba(0,0,0,0.2);
        border-radius:6px;
        overflow:hidden;
        z-index:100;
    }
    .dropdown-content p {
        padding:10px;
        margin:0;
        font-size:14px;
        color:#333;
    }
    .dropdown:hover .dropdown-content { display:block; }

    /* ===== EDIT PROFILE FORM ===== */
    .edit-container{
        max-width:600px;
        margin:40px auto;
        background:#fff;
        padding:30px;
        border-radius:12px;
        box-shadow:0 8px 20px rgba(0,0,0,0.1);
    }
    .edit-container h2{
        text-align:center;
        margin-bottom:20px;
    }
    .edit-container label{
        display:block;
        font-weight:bold;
        margin-top:12px;
        color:#333;
    }
    .edit-container input{
        width:100%;
        padding:10px;
        margin-top:5px;
        border:1px solid #ccc;
        border-radius:6px;
    }
    .edit-container input:focus{
        border-color:#007bff;
        outline:none;
    }
    .edit-container button{
        margin-top:20px;
        width:48%;
        padding:12px;
        background:#007bff;
        border:none;
        color:white;
        font-size:16px;
        border-radius:8px;
        cursor:pointer;
        transition:0.3s;
    }
    .edit-container button:hover{
        background:#0056b3;
    }
    .edit-container .cancel-btn{
        background:#6c757d;
    }
    .edit-container .cancel-btn:hover{
        background:#5a6268;
    }
    .profile-preview{
        margin-top:10px;
        width:100px;
        border-radius:50%;
    }
    .btn-group{
        display:flex;
        justify-content:space-between;
        gap:10px;
    }

    /* EDIT PROFILE FORM */
    .edit-container{
        max-width:600px;
        margin:40px auto;
        background:#fff;
        padding:30px;
        border-radius:12px;
        box-shadow:0 8px 20px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
    }
    .edit-container h2{
        text-align:center;
        margin-bottom:20px;
    }
    .edit-container label{
        display:block;
        font-weight:bold;
        margin-top:12px;
        color:#333;
    }
    .edit-container input{
        width:100%;
        padding:10px;
        margin-top:5px;
        border:1px solid #ccc;
        border-radius:6px;
    }
    .edit-container input:focus{
        border-color:#007bff;
        outline:none;
    }
    .edit-container button{
        margin-top:20px;
        width:48%;
        padding:12px;
        background:#007bff;
        border:none;
        color:white;
        font-size:16px;
        border-radius:8px;
        cursor:pointer;
        transition:0.3s;
    }
    .edit-container button:hover{
        background:#0056b3;
    }
    .edit-container .cancel-btn{
        background:#6c757d;
    }
    .edit-container .cancel-btn:hover{
        background:#5a6268;
    }
    .profile-preview{
        margin-top:10px;
        width:100px;
        border-radius:50%;
    }
    .btn-group{
        display:flex;
        justify-content:space-between;
        gap:10px;
    }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">Dashboard</div>
    <ul>
         <li class="dropdown">
            <a href="javascript:void(0)">Notifications &#9662;</a>
            <div class="dropdown-content">
                <p>No new notifications</p>
            </div>
        </li>
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="edit_profile.php">Edit Profile</a></li>
        <li><a href="history.php">History Reservation</a></li>
        <li><a href="logout.php">Logout</a></li>
    
    </ul>
</div>

<!-- EDIT PROFILE FORM -->
<div class="edit-container">
    <h2>Edit Profile</h2>
    <form method="POST" enctype="multipart/form-data">

        <label>ID Number</label>
        <input type="text" value="<?php echo htmlspecialchars($user['id_number']); ?>" disabled>

        <label>First Name</label>
        <input type="text" name="firstName" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

        <label>Middle Name</label>
        <input type="text" name="middle_name" value="<?php echo htmlspecialchars($user['middle_name']); ?>">

        <label>Last Name</label>
        <input type="text" name="lastName" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>Address</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">

        <label>Profile Photo</label>
        <input type="file" name="photo">

        <?php if(!empty($user['photo'])): ?>
            <img src="<?php echo htmlspecialchars($user['photo']); ?>" class="profile-preview">
        <?php endif; ?>

        <div class="btn-group">
            <button type="submit">Update Profile</button>
            <button type="button" class="cancel-btn" onclick="window.location='dashboard.php'">Cancel</button>
        </div>

    </form>
</div>

</body>
</html>