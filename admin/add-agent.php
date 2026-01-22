<?php
session_start();
if(!isset($_SESSION['admin'])){
  header("Location: login.php");
  exit;
}
include "../config/db.php";

$msg = "";

if($_SERVER['REQUEST_METHOD']=="POST"){
  $name = trim($_POST['name']);
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  if($name && $username && $password){
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn,
      "INSERT INTO users (name, username, password, role)
       VALUES (?,?,?,'agent')"
    );
    mysqli_stmt_bind_param($stmt,"sss",$name,$username,$hash);

    if(mysqli_stmt_execute($stmt)){
      $msg = "✅ Agent created successfully";
    }else{
      $msg = "❌ Username already exists";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Create Agent</title>
<style>
body{font-family:Arial;background:#f7f7fb;padding:30px}
.box{max-width:400px;background:#fff;padding:25px;border-radius:12px}
input,button{width:100%;padding:10px;margin-top:10px}
button{background:#5a2dbd;color:#fff;border:none}
.msg{margin-top:10px;font-weight:bold}
</style>
</head>
<body>

<div class="box">
<h2>Create New Agent</h2>

<?php if($msg) echo "<div class='msg'>$msg</div>"; ?>

<form method="POST">
  <input name="name" placeholder="Agent Name" required>
  <input name="username" placeholder="Username" required>
  <input name="password" placeholder="Password" required>
  <button>Create Agent</button>
</form>

<br>
<a href="dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
