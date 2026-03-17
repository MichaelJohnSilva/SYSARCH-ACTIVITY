<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle photo upload
$uploadError = "";
if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK){
    $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
    $fileName = $_FILES['profile_photo']['name'];
    $fileSize = $_FILES['profile_photo']['size'];
    $fileType = $_FILES['profile_photo']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if(in_array($fileExtension, $allowedExtensions)){
        $newFileName = 'uploads/profile_' . $user_id . '.' . $fileExtension;
        if(!is_dir('uploads')){
            mkdir('uploads', 0755, true); // create uploads folder if not exists
        }

        if(move_uploaded_file($fileTmpPath, $newFileName)){
            // Update in database
            $stmt = $conn->prepare("UPDATE students SET photo=? WHERE id=?");
            $stmt->bind_param("si", $newFileName, $user_id);
            $stmt->execute();
            $stmt->close();
        } else {
            $uploadError = "Error moving uploaded file.";
        }
    } else {
        $uploadError = "Only jpg, jpeg, png, gif files are allowed.";
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch announcements from database
$announcement_query = "SELECT * FROM announcements ORDER BY created_at DESC";
$announcement_result = $conn->query($announcement_query);

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* ===== NAVBAR ===== */
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #a0a0a0;
            color: white;
            padding: 12px 25px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar .left {
            font-size: 22px;
            font-weight: bold;
        }

        .navbar .right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar a, .navbar button {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            transition: 0.3s;
            border: none;
            background: transparent;
            cursor: pointer;
        }

        .navbar a:hover, .navbar button:hover {
            background-color: rgba(255,255,255,0.2);
        }

        /* Notification Dropdown */
        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 250px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            border-radius: 8px;
            overflow: hidden;
            color: #333;
            z-index: 10;
        }

        .dropdown-content p {
            padding: 12px 15px;
            margin: 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .dropdown-content p:last-child { border-bottom: none; }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown button {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .container {
            max-width: 1200px;
            margin: 25px auto;
            display: grid;
            grid-template-columns: 1fr 2fr 2fr;
            gap: 25px;
        }

        /* Profile, Announcement, Rules cards */
        .card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 25px;
        }

        .profile-card {
            width: 320px;
            margin: 40px auto;
            background: #f2f2f2;
            border-radius: 12px;
            padding: 30px 25px;
            text-align: center;
          }

        .profile-card img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 5px solid #a3cbf6;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .profile-card h2 { 
            margin: 10px 0;
            font-size: 28px;
        }
        .profile-card h3{
            margin-bottom: 20px;
        }
        .profile-info{
            text-align: left;
            margin-top: 15px;
        }
        .profile-info p{
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }
        .profile-buttons{
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .upload-btn input[type="file"]{
            display: none;
        }

        .upload-btn label{
            background-color: #98c1ec;
            color: white;
            border-radius: 10px;
            cursor: pointer;
        }

        .upload-btn button{
            padding: 5px 5px;
            background-color: #81cced;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }
        .profile-buttons button{
            display: block;
            border: none;
            border-radius: 10px;
            background-color: #8bb7e6;
            color: white;
            font-size: 16px;
        }

        .profile-buttons button:hover{
            background: #5b9cff;
        }
        .profile-card p { 
            margin: 5px 0; 
            color: #555; 
            font-size: 15px; 
        }
        .upload-btn input[type="file"] { 
            display: none; 
        }
        .upload-btn label {
            padding: 10px 20px;
            background-color: #98c1ec;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
        .upload-btn label:hover { 
            background-color: #98c1ec; 
        }
        .upload-btn button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #81cced;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
        .upload-btn button:hover { background-color: #218838; }

        .announcement h3, .rules h3 { margin-top: 0; color: #007bff; }
        .announcement p, .rules p, .rules li { color: #555; font-size: 14px; }
        .announcement hr { margin: 15px 0; border: none; border-top: 1px solid #ddd; }

        .error { color: red; margin-top: 10px; }

        @media (max-width: 1024px) { .container { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 768px) { .container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="left">Dashboard</div>
    <div class="right">
        <!-- Notification Dropdown -->
        <div class="dropdown">
            <button>Notifications &#9662;</button>
            <div class="dropdown-content">
                <p>No new notifications</p>
            </div>
        </div>
        <a href="index.html">Home</a>
        <a href="edit_profile.php">Edit Profile</a>
        <a href="history.php">History Reservation</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="container">
    <!-- Profile Card -->
    <div class="card profile-card">
        <?php if(!empty($user['photo']) && file_exists($user['photo'])): ?>
            <img src="<?php echo htmlspecialchars($user['photo']); ?>" alt="Profile Photo">
        <?php else: ?>
            <img src="default_avatar.png" alt="Profile Photo">
        <?php endif; ?>
        <h2><?php echo htmlspecialchars($user['first_name'].' '.$user['middle_name'].' '.$user['last_name']); ?></h2>
        <div class="profile-info">

            <p>
            <strong>Course:</strong>
            <span><?php echo htmlspecialchars($user['course']); ?></span>
            </p>

            <p>
            <strong>Email:</strong>
            <span><?php echo htmlspecialchars($user['email']); ?></span>
            </p>

            <p>
            <strong>Address:</strong>
            <span><?php echo htmlspecialchars($user['address']); ?></span>
            </p>

            <p>
            <strong>Sessions:</strong>
            <span>
            <?php
            $max_sessions = 30;

            $current_sessions = isset($user['sessions_remaining']) 
                ? (int)$user['sessions_remaining'] 
                : $max_sessions;

            if($current_sessions > $max_sessions){
                $current_sessions = $max_sessions;
            }

            echo $current_sessions . " / " . $max_sessions;
            ?>
            </span>
            </p>

        </div>



        <!-- Photo Upload -->
        <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="upload-btn">
            <label for="profile_photo">Choose Photo</label>
            <input type="file" name="profile_photo" id="profile_photo" accept="image/*" required>
            <button type="submit">Upload</button>
        </form>
        <?php if(!empty($uploadError)): ?>
            <div class="error"><?php echo htmlspecialchars($uploadError); ?></div>
        <?php endif; ?>
    </div>

   <!-- Announcements -->
    <div class="card announcement">
        <h3>Announcements</h3>

        <?php if($announcement_result && $announcement_result->num_rows > 0): ?>
            <?php while($row = $announcement_result->fetch_assoc()): ?>
                <p><strong>CCS Admin | <?php echo date("Y-M-d", strtotime($row['created_at'])); ?></strong></p>
                <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                <hr>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No announcements yet.</p>
        <?php endif; ?>
    </div>

    <!-- Rules -->
    <div class="card rules">
        <h3>Rules and Regulations</h3>
        <p><strong>University of Cebu</strong><br>College of Information & Computer Studies</p>
        <p><strong>Laboratory Rules and Regulations</strong></p>
        <ol>
            <li>Maintain silence, proper decorum, and discipline inside the laboratory.</li>
            <li>Games are not allowed inside the lab.</li>
            <li>Surfing the Internet is allowed only with the permission of the instructor.</li>
        </ol>
    </div>
</div>

</body>
</html>