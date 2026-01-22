<?php
session_start();
if (!isset($_SESSION['admin'])) {
  exit("Unauthorized");
}

include "../config/db.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = $_GET['s'] ?? '';

$allowed = ['New', 'Follow-up', 'Closed'];
if (!in_array($status, $allowed)) {
  die("Invalid status value");
}

/* DEBUG: check prepare */
$stmt = mysqli_prepare(
  $conn,
  "UPDATE leads SET status=? WHERE id=?"
);

if (!$stmt) {
  die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "si", $status, $id);

if (!mysqli_stmt_execute($stmt)) {
  die("Execute failed: " . mysqli_stmt_error($stmt));
}

header("Location: dashboard.php");
exit;
