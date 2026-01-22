<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}
include "../config/db.php";

/* Dates */
$today = date('Y-m-d');

/* STATS */
$stats = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT
    COUNT(*) total,
    SUM(status='New') newc,
    SUM(status='Follow-up') followc,
    SUM(status='Closed') closedc
  FROM leads
"));

$todayFollow = mysqli_num_rows(mysqli_query(
  $conn,"SELECT id FROM leads WHERE follow_date='$today' AND status!='Closed'"
));
$overdueFollow = mysqli_num_rows(mysqli_query(
  $conn,"SELECT id FROM leads WHERE follow_date<'$today' AND status!='Closed'"
));

$conversion = $stats['total'] > 0
  ? round(($stats['closedc'] / $stats['total']) * 100,1)
  : 0;

/* FILTERS */
$search  = $_GET['search'] ?? '';
$fstatus = $_GET['status'] ?? '';
$filterToday   = isset($_GET['today']);
$filterOverdue = isset($_GET['overdue']);

$sql = "SELECT * FROM leads WHERE 1";
$bind = [];

if ($search != '') {
  $sql .= " AND (name LIKE ? OR mobile LIKE ?)";
}
if ($fstatus != '') {
  $sql .= " AND status=?";
}
if ($filterToday) {
  $sql .= " AND follow_date='$today'";
}
if ($filterOverdue) {
  $sql .= " AND follow_date<'$today' AND status!='Closed'";
}

$sql .= " ORDER BY id DESC";
$stmt = mysqli_prepare($conn,$sql);

/* Bind */
$params = [];
if ($search != '') {
  $like = "%$search%";
  $params[] = &$like;
  $params[] = &$like;
}
if ($fstatus != '') {
  $params[] = &$fstatus;
}
if (count($params)) {
  mysqli_stmt_bind_param($stmt,str_repeat("s",count($params)),...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

/* Agent Performance */
$agents = mysqli_query($conn,"
  SELECT u.name,
         COUNT(l.id) total,
         SUM(l.status='Closed') closed
  FROM users u
  LEFT JOIN leads l ON u.id = l.agent_id
  WHERE u.role = 'agent'
  GROUP BY u.id
");

if(!$agents){
  $agentError = mysqli_error($conn);
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
*{box-sizing:border-box}
body{
  font-family:'Segoe UI',Arial;
  background:#f4f6fb;
  padding:20px;
  color:#333;
}
a{text-decoration:none;color:#5a2dbd}
h2{margin:10px 0 20px}

/* HEADER */
.header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
}
.header a{
  margin-left:10px;
  font-weight:600;
}

/* STATS */
.cards{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(170px,1fr));
  gap:16px;
  margin-bottom:25px;
}
.card{
  padding:18px;
  border-radius:16px;
  color:#fff;
  box-shadow:0 10px 20px rgba(0,0,0,.08);
}
.card b{font-size:22px}
.c1{background:#6a4de6}
.c2{background:#607d8b}
.c3{background:#ff9800}
.c4{background:#4caf50}
.c5{background:#03a9f4}
.c6{background:#f44336}
.c7{background:#9c27b0}

/* BOX */
.box{
  background:#fff;
  padding:20px;
  border-radius:18px;
  box-shadow:0 10px 25px rgba(0,0,0,.06);
}

/* FILTER BAR */
.top{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  margin-bottom:15px;
}
input,select,button{
  padding:10px 12px;
  border-radius:10px;
  border:1px solid #ddd;
}
button{
  background:#5a2dbd;
  color:#fff;
  border:none;
  cursor:pointer;
}

/* TABLE */
table{
  width:100%;
  border-collapse:collapse;
  margin-top:15px;
}
th{
  background:#5a2dbd;
  color:#fff;
  padding:12px;
  font-size:14px;
}
td{
  padding:12px;
  border-bottom:1px solid #eee;
  font-size:14px;
}
tr:hover{background:#faf9ff}

/* STATUS */
.badge{
  padding:6px 12px;
  border-radius:30px;
  font-size:12px;
  font-weight:600;
}
.new{background:#e0e0e0}
.followup{background:#ff9800;color:#fff}
.closed{background:#4caf50;color:#fff}

/* ACTIONS */
.actions a{
  padding:6px 10px;
  border-radius:8px;
  font-size:13px;
  margin-right:5px;
}
.whatsapp{background:#25d366;color:#fff}
.follow{background:#ff9800;color:#fff}
.close{background:#4caf50;color:#fff}
.assign{background:#607d8b;color:#fff}

/* AGENT TABLE */
h3{margin-top:30px}

/* MOBILE */
@media(max-width:768px){
  th,td{font-size:12px}
}
</style>

</head>

<body>
<a href="add-agent.php">➕ Add Agent</a>

<h2>Loan Leads Dashboard</h2>

<div class="cards">
  <div class="card c1">Total<br><b><?= $stats['total'] ?></b></div>
  <div class="card c2">New<br><b><?= $stats['newc'] ?></b></div>
  <div class="card c3">Follow-up<br><b><?= $stats['followc'] ?></b></div>
  <div class="card c4">Closed<br><b><?= $stats['closedc'] ?></b></div>
  <div class="card c5">Today<br><b><?= $todayFollow ?></b></div>
  <div class="card c6">Overdue<br><b><?= $overdueFollow ?></b></div>
  <div class="card c7">Conversion<br><b><?= $conversion ?>%</b></div>
</div>

<div class="box">
<form method="GET" class="top">
  <input name="search" placeholder="Search name/mobile" value="<?= htmlspecialchars($search) ?>">
  <select name="status">
    <option value="">All Status</option>
    <option>New</option>
    <option>Follow-up</option>
    <option>Closed</option>
  </select>
  <button>Filter</button>
  <a href="?today=1">Today</a>
  <a href="?overdue=1">Overdue</a>
  <a href="export.php">Export</a>
  <a href="logout.php">Logout</a>
</form>

<table>
<tr>
<th>Follow-Up</th><th>Notes</th><th>Name</th><th>Mobile</th>
<th>City</th><th>Loan</th><th>Status</th><th>Date</th><th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($result)){
$st = $row['status'] ?? 'New';
$cls = strtolower(str_replace('-','',$st));
$follow = $row['follow_date'];
$style='';
if($follow){
  if($follow<$today) $style='style="color:red;font-weight:bold"';
  elseif($follow==$today) $style='style="color:orange;font-weight:bold"';
}
?>
<tr>
<td <?= $style ?>>
  <?= $follow?date("d M Y",strtotime($follow)):'-' ?>
  <a href="followup.php?id=<?= $row['id'] ?>">Set</a>
</td>
<td><?= $row['notes']?htmlspecialchars($row['notes']):'<span style="color:#999">No notes</span>' ?>
    <a href="note.php?id=<?= $row['id'] ?>">✏</a></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= $row['mobile'] ?></td>
<td><?= $row['city'] ?></td>
<td><?= $row['loan_type'] ?></td>
<td><span class="badge <?= $cls ?>"><?= $st ?></span></td>
<td><?= date("d M Y",strtotime($row['created_at'])) ?></td>
<td class="actions">
  <a class="whatsapp" target="_blank"
 href="https://wa.me/91<?= $row['mobile'] ?>">WhatsApp</a>

<a class="follow" href="status.php?id=<?= $row['id'] ?>&s=Follow-up">Follow</a>
<a class="close" href="status.php?id=<?= $row['id'] ?>&s=Closed">Close</a>
<a class="assign" href="assign.php?id=<?= $row['id'] ?>">Assign</a>

</td>
</tr>
<?php } ?>
</table>

<h3>Agent Performance</h3>
<table>
<tr><th>Agent</th><th>Total</th><th>Closed</th></tr>
<?php while($a=mysqli_fetch_assoc($agents)){ ?>
<tr><td><?= $a['name'] ?></td><td><?= $a['total'] ?></td><td><?= $a['closed'] ?></td></tr>
<?php } ?>
</table>

</div>
</body>
</html>
