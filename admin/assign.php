<?php
session_start();
if(!isset($_SESSION['admin'])) exit;
include "../config/db.php";

$id = intval($_GET['id']);
$users = mysqli_query($conn,"SELECT * FROM users WHERE role='agent'");

if($_SERVER['REQUEST_METHOD']=='POST'){
  $aid = intval($_POST['agent']);
  mysqli_query($conn,
    "UPDATE leads SET agent_id=$aid WHERE id=$id"
  );
  header("Location: dashboard.php");
  exit;
}
?>

<form method="POST">
<h3>Assign Agent</h3>
<select name="agent">
<?php while($u=mysqli_fetch_assoc($users)){ ?>
<option value="<?= $u['id'] ?>"><?= $u['name'] ?></option>
<?php } ?>
</select>
<button>Assign</button>
</form>
