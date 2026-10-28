<?php
require_once "config.php";
$msg=""; $error="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
 $name=trim($_POST["full_name"]);$username=trim($_POST["username"]);$password=$_POST["password"];$email=trim($_POST["email"]);$phone=trim($_POST["phone"]);
 $stmt=$conn->prepare("INSERT INTO users(full_name,username,password,role,email,phone) VALUES(?,?,?,'patient',?,?)");
 $stmt->bind_param("sssss",$name,$username,$password,$email,$phone);
 if($stmt->execute()) $msg="Registration successful. You can now login.";
 else $error="Username may already exist. Please choose another username.";
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Patient Registration</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="auth-page"><div class="auth-box wide"><a class="brand" href="index.php">✚ MediCare HMS</a><h1>Patient Registration</h1><p>Create your patient account.</p>
<?php if($msg): ?><div class="alert success"><?=htmlspecialchars($msg)?></div><?php endif; ?><?php if($error): ?><div class="alert error"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post"><label>Full Name</label><input name="full_name" required><label>Username</label><input name="username" required><label>Email</label><input type="email" name="email"><label>Phone</label><input name="phone"><label>Password</label><input type="password" name="password" required><button class="btn full" type="submit">Create Account</button></form>
<p>Already registered? <a href="login.php">Login</a></p></div>
</body></html>