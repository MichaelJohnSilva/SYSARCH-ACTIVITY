<?php
session_start();
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM students WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $user = $result->fetch_assoc();
       if(password_verify($password, $user['password'])){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                if($user['role'] === 'admin'){
                    header("Location: admin_dashboard.php");
                }else{
                header("Location: dashboard.php");           
                }

                exit();
            } else {
            $stmt->close();
            $conn->close();
            echo "<script>alert('Incorrect password'); window.location.href='login.html';</script>";
            exit();
        }
    } else {
        $stmt->close();
        $conn->close();
        echo "<script>alert('User not found'); window.location.href='login.html';</script>";
        exit();
    }
} else {
    echo "Invalid request";
}
?>