<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idNumber = trim($_POST['idNumber']);
    $lastName = trim($_POST['lastName']);
    $firstName = trim($_POST['firstName']);
    $middleName = trim($_POST['middleName']);
    $course = trim($_POST['course']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $repeatPassword = trim($_POST['repeatPassword']);
    $address = trim($_POST['address']);

    if ($password !== $repeatPassword) {
        die("Passwords do not match!");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO students 
        (id_number, last_name, first_name, middle_name, course, email, password, address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $idNumber, $lastName, $firstName, $middleName, $course, $email, $hashedPassword, $address);

    if ($stmt->execute()) {
        echo "<script>
                alert('Registration successful!');
                window.location.href='login.html';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    echo "Invalid request.";
}
?>