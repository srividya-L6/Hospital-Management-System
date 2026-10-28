MEDICARE HOSPITAL MANAGEMENT SYSTEM
===================================

TECHNOLOGIES
HTML5, CSS3, JavaScript, PHP, MySQL, XAMPP

SETUP
1. Install XAMPP.
2. Copy the complete "Hospital_Management_System" folder into:
   C:\xampp\htdocs\
3. Start Apache and MySQL from XAMPP Control Panel.
4. Open http://localhost/phpmyadmin
5. Create/import the database by selecting database.sql and clicking Import.
6. Open:
   http://localhost/Hospital_Management_System/

DEMO LOGIN
Admin:
username: admin
password: admin123

Doctor:
username: ananya
password: doctor123

Patient:
username: patient
password: patient123

MAIN MODULES
1. Patient - registration, login, appointment booking, payment record, prescriptions.
2. Doctor - login, view appointments, search patients, add prescriptions.
3. Admin - login, dashboard, view doctors/patients/appointments, add doctors.

NOTE
This is a college-project demonstration. The payment feature records a payment in
the local MySQL database; it is not connected to a real payment gateway.
