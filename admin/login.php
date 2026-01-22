<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<style>
body{font-family:Arial;background:#f7f7fb}
.box{width:320px;margin:100px auto;background:#fff;padding:25px;border-radius:10px}
input,button{width:100%;padding:10px;margin-top:10px}
button{background:#5a2dbd;color:#fff;border:none}
</style>
</head>
<body>

<div class="box">
<h2>Admin Login</h2>
<form method="POST" action="login-check.php">
  <input type="text" name="username" placeholder="Username" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit">Login</button>
</form>
</div>

</body>
</html>
