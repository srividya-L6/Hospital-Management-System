<?php
require_once "config.php";
$error="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
    $username=trim($_POST["username"]);
    $password=$_POST["password"];
    $stmt=$conn->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s",$username); $stmt->execute();
    $user=$stmt->get_result()->fetch_assoc();
    if($user && $password===$user["password"]){
        $_SESSION["user_id"]=$user["id"]; $_SESSION["role"]=$user["role"]; $_SESSION["name"]=$user["full_name"];
        header("Location: dashboard.php"); exit;
    }
    $error="Invalid username or password.";
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login | MediCare HMS</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="auth-page"><div class="auth-box"><a class="brand" href="index.php">✚ MediCare HMS</a><h1>Welcome back</h1><p>Login to your hospital portal.</p>
<?php if($error): ?><div class="alert error"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post"><label>Username</label><input name="username" required><label>Password</label><input type="password" name="password" required><button class="btn full" type="submit">Login</button></form>
<div class="demo"><b>Demo accounts</b><br>Admin: admin / admin123<br>Doctor: ananya / doctor123<br>Patient: patient / patient123</div><p>New patient? <a href="register.php">Register here</a></p></div>
</body></html>