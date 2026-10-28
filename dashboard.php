<?php
require_once "config.php";
if(!isset($_SESSION["user_id"])) { header("Location: login.php"); exit; }
$id=(int)$_SESSION["user_id"]; $role=$_SESSION["role"]; $name=$_SESSION["name"];
function e($s){return htmlspecialchars($s??'',ENT_QUOTES,'UTF-8');}
$doctors=$conn->query("SELECT id,full_name,department FROM users WHERE role='doctor' ORDER BY full_name");
$notice=""; $error="";
if($role==="patient" && $_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["book"])){
 $doctor=(int)$_POST["doctor_id"];$date=$_POST["appointment_date"];$time=$_POST["appointment_time"];$reason=trim($_POST["reason"]);
 $st=$conn->prepare("INSERT INTO appointments(patient_id,doctor_id,appointment_date,appointment_time,reason) VALUES(?,?,?,?,?)");
 $st->bind_param("iisss",$id,$doctor,$date,$time,$reason);
 if($st->execute()) $notice="Appointment booked successfully.";
}
if($role==="patient" && $_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["pay"])){
 $appointment=(int)$_POST["appointment_id"];$amount=(float)$_POST["amount"];
 $q=$conn->prepare("SELECT doctor_id FROM appointments WHERE id=? AND patient_id=?");$q->bind_param("ii",$appointment,$id);$q->execute();$a=$q->get_result()->fetch_assoc();
 if($a){$st=$conn->prepare("INSERT INTO payments(patient_id,doctor_id,appointment_id,amount,payment_method,payment_status) VALUES(?,?,?,?, 'Online','Paid')");$st->bind_param("iiid",$id,$a["doctor_id"],$appointment,$amount);$st->execute();$notice="Payment recorded successfully.";}
}
if($role==="doctor" && $_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["prescribe"])){
 $appt=(int)$_POST["appointment_id"];$patient=(int)$_POST["patient_id"];$diagnosis=trim($_POST["diagnosis"]);$med=trim($_POST["medicines"]);$inst=trim($_POST["instructions"]);
 $st=$conn->prepare("INSERT INTO prescriptions(appointment_id,doctor_id,patient_id,diagnosis,medicines,instructions) VALUES(?,?,?,?,?,?)");$st->bind_param("iiisss",$appt,$id,$patient,$diagnosis,$med,$inst);$st->execute();$notice="Prescription added successfully.";
}
if($role==="admin" && $_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["add_doctor"])){
 $n=trim($_POST["full_name"]);$u=trim($_POST["username"]);$p=$_POST["password"];$dep=trim($_POST["department"]);$em=trim($_POST["email"]);
 $st=$conn->prepare("INSERT INTO users(full_name,username,password,role,email,department) VALUES(?,?,?,'doctor',?,?)");$st->bind_param("sssss",$n,$u,$p,$em,$dep);
 if($st->execute())$notice="Doctor added successfully.";else$error="Could not add doctor. Username may already exist.";
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dashboard | MediCare HMS</title><link rel="stylesheet" href="assets/style.css"><style>
.dash{background:#f6f9fd;min-height:100vh}.dashbar{background:#fff;border-bottom:1px solid #e2e8f0;padding:16px 0}.dashnav{display:flex;justify-content:space-between;align-items:center;gap:15px}.logout{background:#fff;border:1px solid #e2e8f0;padding:9px 14px;border-radius:8px;text-decoration:none;color:#e33}.dashmain{padding:35px 0}.welcome{display:flex;justify-content:space-between;gap:20px;align-items:center;margin-bottom:25px}.welcome h1{font-size:32px;margin:0}.panel{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:22px;margin-bottom:20px}.panel h2{margin-top:0;color:#10233f}.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.panel input,.panel select,.panel textarea{width:100%;padding:11px;border:1px solid #d9e2ef;border-radius:8px;box-sizing:border-box}.panel textarea{min-height:85px}.panel label{display:block;font-weight:700;font-size:13px;margin-bottom:5px}.panel .field{margin-bottom:12px}.alert{padding:12px;border-radius:8px;margin-bottom:16px}.success{background:#e9fbf4;color:#087454}.error{background:#fff0f0;color:#a22}.table-wrap{overflow:auto}.table{width:100%;border-collapse:collapse}.table th,.table td{padding:11px;border-bottom:1px solid #e8edf3;text-align:left;font-size:13px;white-space:nowrap}.cards{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.statcard{background:#fff;border:1px solid #e2e8f0;padding:20px;border-radius:14px}.statcard b{font-size:28px;color:#1264e8;display:block}@media(max-width:700px){.form-grid,.cards{grid-template-columns:1fr}.welcome{align-items:flex-start;flex-direction:column}}
</style></head><body class="dash"><header class="dashbar"><div class="container dashnav"><a class="brand" href="index.php">✚ MediCare HMS</a><div>Welcome, <b><?=e($name)?></b> &nbsp; <a class="logout" href="logout.php">Logout</a></div></div></header>
<main class="container dashmain">
<div class="welcome"><div><span class="eyebrow"><?=strtoupper(e($role))?> MODULE</span><h1>Dashboard</h1></div></div>
<?php if($notice):?><div class="alert success"><?=e($notice)?></div><?php endif;?><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>

<?php if($role==="patient"): ?>
<div class="panel"><h2>Book an Appointment</h2><form method="post"><input type="hidden" name="book" value="1"><div class="form-grid"><div class="field"><label>Doctor</label><select name="doctor_id" required><option value="">Select doctor</option><?php while($d=$doctors->fetch_assoc()):?><option value="<?=$d['id']?>"><?=e($d['full_name'])?> — <?=e($d['department'])?></option><?php endwhile;?></select></div><div class="field"><label>Reason</label><input name="reason" placeholder="Reason for visit"></div><div class="field"><label>Date</label><input type="date" name="appointment_date" required></div><div class="field"><label>Time</label><input type="time" name="appointment_time" required></div></div><button class="btn" type="submit">Book Appointment</button></form></div>
<div class="panel"><h2>My Appointments</h2><div class="table-wrap"><table class="table"><tr><th>Doctor</th><th>Department</th><th>Date</th><th>Time</th><th>Status</th><th>Payment</th></tr>
<?php $st=$conn->prepare("SELECT a.*,u.full_name,u.department FROM appointments a JOIN users u ON a.doctor_id=u.id WHERE a.patient_id=? ORDER BY a.appointment_date DESC");$st->bind_param("i",$id);$st->execute();$res=$st->get_result();while($a=$res->fetch_assoc()):?>
<tr><td><?=e($a['full_name'])?></td><td><?=e($a['department'])?></td><td><?=e($a['appointment_date'])?></td><td><?=e($a['appointment_time'])?></td><td><?=e($a['status'])?></td><td><form method="post" style="display:flex;gap:5px"><input type="hidden" name="pay" value="1"><input type="hidden" name="appointment_id" value="<?=$a['id']?>"><input name="amount" type="number" min="1" value="500" style="width:80px"><button class="btn" type="submit">Pay</button></form></td></tr>
<?php endwhile;?></table></div></div>
<div class="panel"><h2>My Prescriptions</h2><div class="table-wrap"><table class="table"><tr><th>Date</th><th>Diagnosis</th><th>Medicines</th><th>Instructions</th></tr>
<?php $st=$conn->prepare("SELECT p.*,u.full_name FROM prescriptions p JOIN users u ON p.doctor_id=u.id WHERE p.patient_id=? ORDER BY p.created_at DESC");$st->bind_param("i",$id);$st->execute();$res=$st->get_result();while($p=$res->fetch_assoc()):?><tr><td><?=e($p['created_at'])?></td><td><?=e($p['diagnosis'])?></td><td><?=e($p['medicines'])?></td><td><?=e($p['instructions'])?></td></tr><?php endwhile;?></table></div></div>

<?php elseif($role==="doctor"): ?>
<div class="panel"><h2>My Appointments</h2><div class="table-wrap"><table class="table"><tr><th>Patient</th><th>Phone</th><th>Date</th><th>Time</th><th>Reason</th><th>Prescription</th></tr>
<?php $st=$conn->prepare("SELECT a.*,u.full_name,u.phone FROM appointments a JOIN users u ON a.patient_id=u.id WHERE a.doctor_id=? ORDER BY a.appointment_date DESC");$st->bind_param("i",$id);$st->execute();$res=$st->get_result();while($a=$res->fetch_assoc()):?><tr><td><?=e($a['full_name'])?></td><td><?=e($a['phone'])?></td><td><?=e($a['appointment_date'])?></td><td><?=e($a['appointment_time'])?></td><td><?=e($a['reason'])?></td><td><details><summary>Add</summary><form method="post"><input type="hidden" name="prescribe" value="1"><input type="hidden" name="appointment_id" value="<?=$a['id']?>"><input type="hidden" name="patient_id" value="<?=$a['patient_id']?>"><p><input name="diagnosis" placeholder="Diagnosis" required></p><p><textarea name="medicines" placeholder="Medicines and dosage" required></textarea></p><p><textarea name="instructions" placeholder="Instructions"></textarea></p><button class="btn" type="submit">Save</button></form></details></td></tr><?php endwhile;?></table></div></div>
<div class="panel"><h2>Search Patients</h2><form method="get"><input name="q" placeholder="Search patient name" value="<?=e($_GET['q']??'')?>"> <button class="btn" type="submit">Search</button></form>
<?php if(isset($_GET['q'])){$q="%".$_GET['q']."%";$st=$conn->prepare("SELECT full_name,email,phone FROM users WHERE role='patient' AND full_name LIKE ?");$st->bind_param("s",$q);$st->execute();$r=$st->get_result();?><div class="table-wrap"><table class="table"><tr><th>Name</th><th>Email</th><th>Phone</th></tr><?php while($p=$r->fetch_assoc()):?><tr><td><?=e($p['full_name'])?></td><td><?=e($p['email'])?></td><td><?=e($p['phone'])?></td></tr><?php endwhile;?></table></div><?php }?></div>

<?php else: ?>
<?php
$pc=$conn->query("SELECT COUNT(*) c FROM users WHERE role='patient'")->fetch_assoc()['c'];
$dc=$conn->query("SELECT COUNT(*) c FROM users WHERE role='doctor'")->fetch_assoc()['c'];
$ac=$conn->query("SELECT COUNT(*) c FROM appointments")->fetch_assoc()['c'];
?>
<div class="cards"><div class="statcard">Patients<b><?=$pc?></b></div><div class="statcard">Doctors<b><?=$dc?></b></div><div class="statcard">Appointments<b><?=$ac?></b></div></div>
<div class="panel" style="margin-top:20px"><h2>Add New Doctor</h2><form method="post"><input type="hidden" name="add_doctor" value="1"><div class="form-grid"><div class="field"><label>Doctor Name</label><input name="full_name" required></div><div class="field"><label>Username</label><input name="username" required></div><div class="field"><label>Password</label><input name="password" required></div><div class="field"><label>Department</label><input name="department" required></div><div class="field"><label>Email</label><input name="email" type="email"></div></div><button class="btn" type="submit">Add Doctor</button></form></div>
<div class="panel"><h2>Doctors</h2><div class="table-wrap"><table class="table"><tr><th>Name</th><th>Username</th><th>Department</th><th>Email</th></tr><?php $r=$conn->query("SELECT full_name,username,department,email FROM users WHERE role='doctor'");while($d=$r->fetch_assoc()):?><tr><td><?=e($d['full_name'])?></td><td><?=e($d['username'])?></td><td><?=e($d['department'])?></td><td><?=e($d['email'])?></td></tr><?php endwhile;?></table></div></div>
<div class="panel"><h2>All Appointments</h2><div class="table-wrap"><table class="table"><tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr><?php $r=$conn->query("SELECT a.*,p.full_name patient,d.full_name doctor FROM appointments a JOIN users p ON a.patient_id=p.id JOIN users d ON a.doctor_id=d.id ORDER BY a.appointment_date DESC");while($a=$r->fetch_assoc()):?><tr><td><?=e($a['patient'])?></td><td><?=e($a['doctor'])?></td><td><?=e($a['appointment_date'])?></td><td><?=e($a['appointment_time'])?></td><td><?=e($a['status'])?></td></tr><?php endwhile;?></table></div></div>
<?php endif; ?>
</main></body></html>