<?php
session_start();
include "../config/db.php";

$user = $_POST['username'];
$pass = $_POST['password'];

$stmt = mysqli_prepare($conn, "SELECT * FROM admin_users WHERE username=?");
mysqli_stmt_bind_param($stmt, "s", $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    if (password_verify($pass, $row['password'])) {
        $_SESSION['admin'] = $row['username'];
        header("Location: dashboard.php");
        exit;
    }
}

echo "Invalid Login";
?>
