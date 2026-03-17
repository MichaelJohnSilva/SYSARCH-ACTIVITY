<?php
session_start();
include "config.php";

// Only admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.html");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Clean input
    $id_number = trim($_POST['id_number']);
    $id_number = preg_replace('/\s+/', '', $id_number); // remove spaces
    $purpose   = trim($_POST['purpose']);
    $lab       = trim($_POST['lab']);

    // 1️⃣ Check if student exists
    $stmt = $conn->prepare("SELECT first_name, last_name, sessions_remaining FROM students WHERE id_number=?");
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows == 0){
        die("Student not found in the database!"); // <- shows if ID doesn't match exactly
    }

    $stmt->bind_result($first_name, $last_name, $sessions_remaining);
    $stmt->fetch();
    $stmt->close();

    // 2️⃣ Check if student already has active sit-in
    $stmt = $conn->prepare("SELECT COUNT(*) FROM sitin_records WHERE id_number=? AND status='Active'");
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    $stmt->bind_result($activeCount);
    $stmt->fetch();
    $stmt->close();

    if($activeCount > 0){
        die("Student already has an active sit-in!");
    }

    // 3️⃣ Check sessions remaining
    if($sessions_remaining <= 0){
        die("No remaining sessions for this student!");
    }

    // 4️⃣ Insert sit-in record
    $stmt = $conn->prepare("INSERT INTO sitin_records (id_number, purpose, lab, status) VALUES (?, ?, ?, 'Active')");
    $stmt->bind_param("sss", $id_number, $purpose, $lab);
    $stmt->execute();
    $stmt->close();

    // 5️⃣ Decrease session safely
    $stmt = $conn->prepare("UPDATE students SET sessions_remaining = GREATEST(sessions_remaining - 1, 0) WHERE id_number=?");
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    $stmt->close();

    // 6️⃣ Redirect to sit-in records
    header("Location: view_sitin_records.php");
    exit();
}
?>