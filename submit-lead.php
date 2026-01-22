<?php
include "config/db.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

$name = $_POST['name'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$city = $_POST['city'] ?? '';
$employment = $_POST['employment'] ?? '';
$income = $_POST['income'] ?? '';
$loan_type = "Personal Loan";

$stmt = mysqli_prepare(
  $conn,
  "INSERT INTO leads (name,mobile,city,employment,income,loan_type)
   VALUES (?,?,?,?,?,?)"
);

if(!$stmt){
  die("Prepare failed: ".mysqli_error($conn));
}

mysqli_stmt_bind_param(
  $stmt,
  "ssssss",
  $name,$mobile,$city,$employment,$income,$loan_type
);

if(!mysqli_stmt_execute($stmt)){
  die("Execute failed: ".mysqli_stmt_error($stmt));
}

/* redirect */
header("Location: thank-you.php");
exit;
?>
