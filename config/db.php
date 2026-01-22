<?php
$conn = mysqli_connect(
  "localhost",
  "root",
  "",
  "crm_db"
);

if (!$conn) {
  die("Database connection failed");
}
?>
