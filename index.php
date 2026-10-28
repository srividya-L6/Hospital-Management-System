<?php
require_once "config.php";
$doctors = $conn->query("SELECT id,full_name,department FROM users WHERE role='doctor' ORDER BY full_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediCare Hospital | Hospital Management System</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <div class="container nav">
    <a class="brand" href="index.php"><span>✚</span> MediCare HMS</a>
    <nav>
      <a href="#home">Home</a><a href="#services">Services</a><a href="#doctors">Doctors</a><a href="#about">About</a>
      <a class="nav-btn" href="login.php">Login</a>
    </nav>
  </div>
</header>

<main>
<section id="home" class="hero">
<div class="container hero-grid">
  <div>
    <span class="eyebrow">WEB-BASED HOSPITAL MANAGEMENT SYSTEM</span>
    <h1>Healthcare made <span>simple and connected.</span></h1>
    <p>Manage patients, doctors, appointments and prescriptions through one secure web platform.</p>
    <div class="actions"><a class="btn" href="register.php">Register as Patient</a><a class="btn light" href="login.php">Login Portal</a></div>
    <div class="mini-stats"><div><b>24/7</b><small>Support</small></div><div><b>50+</b><small>Doctors</small></div><div><b>1000+</b><small>Patients</small></div></div>
  </div>
  <div class="hero-card"><div class="hospital-icon">✚</div><h3>Complete Hospital Care</h3><p>Patients, doctors and administrators can access their respective modules with role-based authentication.</p><div class="feature-line">✓ Secure login &nbsp; ✓ Online appointments &nbsp; ✓ Digital prescriptions</div></div>
</div>
</section>

<section id="services" class="section container">
<div class="section-title"><span>WHAT WE OFFER</span><h2>Hospital services in one place</h2></div>
<div class="cards">
<div class="card"><div class="card-icon">📅</div><h3>Appointment Booking</h3><p>Patients can select a doctor, date and time and submit an appointment request online.</p></div>
<div class="card"><div class="card-icon">💊</div><h3>Digital Prescriptions</h3><p>Doctors can create prescriptions and patients can view their medicines and instructions.</p></div>
<div class="card"><div class="card-icon">💳</div><h3>Fee Payment</h3><p>Patients can record consultation fee payments for their appointments.</p></div>
<div class="card"><div class="card-icon">🧑‍⚕️</div><h3>Doctor Management</h3><p>Administrators can add doctors and maintain department information.</p></div>
</div>
</section>

<section id="doctors" class="section soft">
<div class="container"><div class="section-title"><span>OUR TEAM</span><h2>Meet our doctors</h2></div>
<div class="doctor-grid">
<?php while($d=$doctors->fetch_assoc()): ?>
<div class="doctor-card"><div class="avatar">👨‍⚕️</div><h3><?=htmlspecialchars($d['full_name'])?></h3><p><?=htmlspecialchars($d['department'])?></p><a href="login.php">Book Appointment →</a></div>
<?php endwhile; ?>
</div></div></section>

<section id="about" class="section container">
<div class="about"><div><span class="eyebrow">ABOUT THE SYSTEM</span><h2>A digital platform for modern hospital administration.</h2></div>
<p>MediCare HMS provides separate modules for patients, doctors and administrators. It reduces manual work by managing registrations, appointments, prescriptions, doctor information and payment records online. The system uses HTML5, CSS3, JavaScript, Bootstrap-style responsive design, PHP and MySQL.</p></div>
</section>
</main>

<footer><div class="container"><b>🏥 MediCare Hospital</b><span>Web-Based Hospital Management System</span><span>Hyderabad, Telangana · +91 98765 43210</span></div></footer>
</body></html>