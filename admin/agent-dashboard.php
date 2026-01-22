<?php
session_start();
if(!isset($_SESSION['agent'])){
  header("Location: agent-login.php");
  exit;
}
include "../config/db.php";

$aid = $_SESSION['agent'];
$res = mysqli_query($conn,
  "SELECT * FROM leads WHERE agent_id=$aid ORDER BY id DESC"
);
?>

<h2>Welcome <?= $_SESSION['agent_name'] ?></h2>
<a href="agent-logout.php">Logout</a>

<table border="1" cellpadding="8">
<tr>
<th>Name</th><th>Mobile</th><th>Status</th><th>Follow Date</th>
</tr>

<?php while($row=mysqli_fetch_assoc($res)){ ?>
<tr>
<td><?= $row['name'] ?></td>
<td><?= $row['mobile'] ?></td>
<td><?= $row['status'] ?></td>
<td><?= $row['follow_date'] ?></td>
</tr>
<?php } ?>
</table>
