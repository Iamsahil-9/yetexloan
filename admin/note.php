<?php
session_start();
if(!isset($_SESSION['admin'])){
  header("Location: login.php");
  exit;
}
include "../config/db.php";

$id = intval($_GET['id'] ?? 0);

$res = mysqli_query($conn, "SELECT * FROM leads WHERE id=$id");
$lead = mysqli_fetch_assoc($res);

if(!$lead){
  die("Lead not found");
}

if($_SERVER['REQUEST_METHOD']=='POST'){
  $notes = mysqli_real_escape_string($conn, $_POST['notes']);
  mysqli_query($conn, "UPDATE leads SET notes='$notes' WHERE id=$id");
  header("Location: dashboard.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Note</title>
<style>
body{font-family:Arial;background:#f7f7fb;padding:30px}
.box{background:#fff;padding:20px;border-radius:10px;max-width:500px;margin:auto}
textarea{width:100%;height:120px;padding:10px}
button{margin-top:10px;padding:10px 15px;background:#5a2dbd;color:#fff;border:none}
</style>
</head>
<body>

<div class="box">
<h3>Add Note for <?= htmlspecialchars($lead['name']) ?></h3>

<form method="POST">
  <textarea name="notes" placeholder="Enter call notes..."><?= htmlspecialchars($lead['notes']) ?></textarea>
  <br>
  <button type="submit">Save Note</button>
</form>
</div>

</body>
</html>
