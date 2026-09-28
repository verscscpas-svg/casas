function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("collapsed");
}
function addemp() {
  window.location.href = "add-emp.php";
}

function viewEmp(id) {
  window.location.href = "view-emp.php?id=" + id;
}

// view emp
