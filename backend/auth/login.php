<?php
session_start();
include "../db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        header("Location: ../../login.php?error=Please fill all fields");
        exit;
    }

    // Prepare statement
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Login successful, store in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name']; // store name
            header("Location: ../../dashboard.php");
            exit;
        } else {
            header("Location: ../../login.php?error=Invalid password");
            exit;
        }
    } else {
        header("Location: ../../login.php?error=Email not found");
        exit;
    }
} else {
    header("Location: ../../login.php");
    exit;
}
?>
