const defaultUsers = [
 {username:"admin",password:"admin123",role:"admin",name:"Administrator"},
 {username:"ananya",password:"doctor123",role:"doctor",name:"Dr. Ananya Sharma"},
 {username:"patient",password:"patient123",role:"patient",name:"Demo Patient"}
];

let users = JSON.parse(localStorage.getItem("hms_users")) || defaultUsers;
let appointments = JSON.parse(localStorage.getItem("hms_appointments")) || [];
let prescriptions = JSON.parse(localStorage.getItem("hms_prescriptions")) || [];
let payments = JSON.parse(localStorage.getItem("hms_payments")) || [];
let doctors = users.filter(u => u.role === "doctor");
let currentUser = null;

function save(){
 localStorage.setItem("hms_users",JSON.stringify(users));
 localStorage.setItem("hms_appointments",JSON.stringify(appointments));
 localStorage.setItem("hms_prescriptions",JSON.stringify(prescriptions));
 localStorage.setItem("hms_payments",JSON.stringify(payments));
}
function hideAll(){["home","login","register","dashboard"].forEach(id=>document.getElementById(id).classList.add("d-none"));}
function showHome(){hideAll();document.getElementById("home").classList.remove("d-none");}
function showLogin(){hideAll();document.getElementById("login").classList.remove("d-none");}
function showRegister(){hideAll();document.getElementById("register").classList.remove("d-none");}

function login(){
 const u=document.getElementById("loginUser").value.trim(), p=document.getElementById("loginPass").value;
 const found=users.find(x=>x.username===u && x.password===p);
 if(!found){document.getElementById("loginMsg").innerHTML='<div class="alert alert-danger">Invalid username or password.</div>';return;}
 currentUser=found; renderDashboard();
}
function register(){
 const name=document.getElementById("regName").value.trim(), username=document.getElementById("regUser").value.trim(), password=document.getElementById("regPass").value, phone=document.getElementById("regPhone").value.trim();
 if(!name||!username||!password){document.getElementById("regMsg").innerHTML='<div class="alert alert-warning">Please fill all required fields.</div>';return;}
 if(users.some(u=>u.username===username)){document.getElementById("regMsg").innerHTML='<div class="alert alert-danger">Username already exists.</div>';return;}
 users.push({name,username,password,phone,role:"patient"}); save();
 document.getElementById("regMsg").innerHTML='<div class="alert alert-success">Registration successful. You can now login.</div>';
}
function logout(){currentUser=null;showHome();}

function renderDashboard(){
 hideAll(); document.getElementById("dashboard").classList.remove("d-none");
 document.getElementById("dashTitle").textContent = currentUser.role.toUpperCase()+" DASHBOARD";
 if(currentUser.role==="patient") patientDashboard();
 else if(currentUser.role==="doctor") doctorDashboard();
 else adminDashboard();
}
function patientDashboard(){
 const mine=appointments.filter(a=>a.patient===currentUser.username);
 const pres=prescriptions.filter(p=>p.patient===currentUser.username);
 document.getElementById("dashContent").innerHTML=`
 <div class="row g-3">
 <div class="col-lg-5"><div class="card p-3"><h4>Book Appointment</h4>
 <input id="aDate" type="date" class="form-control mb-2">
 <select id="aDoctor" class="form-select mb-2">${doctors.map(d=>`<option value="${d.username}">${d.name}</option>`).join("")}</select>
 <input id="aReason" class="form-control mb-2" placeholder="Reason">
 <button class="btn btn-primary" onclick="bookAppointment()">Book</button></div></div>
 <div class="col-lg-7"><div class="card p-3"><h4>My Appointments</h4>${mine.length?mine.map(a=>`<p><b>${a.date}</b> — ${a.doctor} — ${a.reason}</p>`).join(""):"<p>No appointments yet.</p>"}</div>
 <div class="card p-3 mt-3"><h4>Prescriptions</h4>${pres.length?pres.map(p=>`<p><b>${p.date}</b>: ${p.medicine}</p>`).join(""):"<p>No prescriptions yet.</p>"}</div></div>
 </div>
 <div class="card p-3 mt-3"><h4>Record Doctor Fee Payment</h4><input id="payAmount" type="number" class="form-control mb-2" placeholder="Amount"><button class="btn btn-success" onclick="pay()">Record Payment</button></div>`;
}
function bookAppointment(){
 const date=document.getElementById("aDate").value, doctor=document.getElementById("aDoctor").value, reason=document.getElementById("aReason").value;
 if(!date||!reason){alert("Enter date and reason.");return;}
 appointments.push({patient:currentUser.username,doctor,date,reason});save();alert("Appointment booked.");renderDashboard();
}
function pay(){
 const amount=document.getElementById("payAmount").value;
 if(!amount){alert("Enter amount.");return;}
 payments.push({patient:currentUser.username,amount,date:new Date().toLocaleDateString()});save();alert("Payment recorded.");renderDashboard();
}
function doctorDashboard(){
 const mine=appointments.filter(a=>a.doctor===currentUser.username);
 document.getElementById("dashContent").innerHTML=`
 <div class="card p-3"><h4>My Appointments</h4>${mine.length?mine.map(a=>`<p>${a.date} — Patient: <b>${a.patient}</b> — ${a.reason}</p>`).join(""):"<p>No appointments.</p>"}</div>
 <div class="card p-3 mt-3"><h4>Add Prescription</h4>
 <input id="pPatient" class="form-control mb-2" placeholder="Patient username">
 <input id="pMedicine" class="form-control mb-2" placeholder="Prescription / medicine">
 <button class="btn btn-primary" onclick="addPrescription()">Save Prescription</button></div>`;
}
function addPrescription(){
 const patient=document.getElementById("pPatient").value.trim(), medicine=document.getElementById("pMedicine").value.trim();
 if(!patient||!medicine){alert("Fill all fields.");return;}
 prescriptions.push({doctor:currentUser.username,patient,medicine,date:new Date().toLocaleDateString()});save();alert("Prescription added.");renderDashboard();
}
function adminDashboard(){
 const patients=users.filter(u=>u.role==="patient").length, doc=users.filter(u=>u.role==="doctor").length;
 document.getElementById("dashContent").innerHTML=`
 <div class="row g-3">
 <div class="col-md-4"><div class="stat">Patients <b>${patients}</b></div></div>
 <div class="col-md-4"><div class="stat">Doctors <b>${doc}</b></div></div>
 <div class="col-md-4"><div class="stat">Appointments <b>${appointments.length}</b></div></div>
 </div>
 <div class="card p-3 mt-4"><h4>Add Doctor</h4>
 <input id="dName" class="form-control mb-2" placeholder="Doctor name">
 <input id="dUser" class="form-control mb-2" placeholder="Username">
 <input id="dPass" class="form-control mb-2" placeholder="Password">
 <button class="btn btn-primary" onclick="addDoctor()">Add Doctor</button></div>
 <div class="card p-3 mt-3"><h4>Appointments</h4>${appointments.length?appointments.map(a=>`<p>${a.date} — Patient: ${a.patient} — Doctor: ${a.doctor} — ${a.reason}</p>`).join(""):"<p>No appointments.</p>"}</div>`;
}
function addDoctor(){
 const name=document.getElementById("dName").value.trim(), username=document.getElementById("dUser").value.trim(), password=document.getElementById("dPass").value;
 if(!name||!username||!password){alert("Fill all fields.");return;}
 if(users.some(u=>u.username===username)){alert("Username already exists.");return;}
 users.push({name,username,password,role:"doctor"});save();alert("Doctor added.");renderDashboard();
}
save();
