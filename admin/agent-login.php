<?php
session_start();
include "../config/db.php";

if($_SERVER['REQUEST_METHOD']=='POST'){
  $u = $_POST['username'];
  $p = $_POST['password'];

  $q = mysqli_prepare($conn,
    "SELECT * FROM users WHERE username=? AND role='agent'"
  );
  mysqli_stmt_bind_param($q,"s",$u);
  mysqli_stmt_execute($q);
  $r = mysqli_stmt_get_result($q);

  if($row=mysqli_fetch_assoc($r)){
    if(password_verify($p,$row['password'])){
      $_SESSION['agent'] = $row['id'];
      $_SESSION['agent_name'] = $row['name'];
      header("Location: agent-dashboard.php");
      exit;
    }
  }
  $error="Invalid login";
}
?>

<form method="POST">
<h2>Agent Login</h2>
<?= $error ?? '' ?>
<input name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button>Login</button>
</form>
