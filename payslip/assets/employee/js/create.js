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
let showResigned = false;

document.getElementById("viewAllBtn").addEventListener("click", function () {
  showResigned = !showResigned; // toggle true/false
  loadEmployees(showResigned);

  // Optional: change button text
  this.textContent = showResigned ? "View Only Employed" : "View All Employee";
});

function loadEmployees(includeResigned) {
  let xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "process/load_employees.php?resigned=" + (includeResigned ? 1 : 0),
    true,
  );
  xhr.onload = function () {
    if (xhr.status === 200) {
      document.querySelector("table tbody").innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}

function create() {
  window.location.href = "create-payslip.php";
}
