// Profile image preview
const imageUpload = document.getElementById("imageUpload");
const previewImg = document.getElementById("previewImg");

if (imageUpload && previewImg) {
  imageUpload.addEventListener("change", function (event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      previewImg.src = e.target.result;
    };
    reader.readAsDataURL(file);
  });
}

// Bank QR preview
const bankqrUpload = document.getElementById("bankqrUpload");
const previewBankQr = document.getElementById("previewBankQr");

if (bankqrUpload && previewBankQr) {
  bankqrUpload.addEventListener("change", function (event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      previewBankQr.src = e.target.result;
    };
    reader.readAsDataURL(file);
  });
}
//   upd
// REMOVE NON-NUMBERS
function numbersOnly(value) {
  return value.replace(/\D/g, "");
}

// FORMAT FUNCTIONS
function formatTIN(val) {
  val = numbersOnly(val);
  if (val.length > 9) return val.replace(/(\d{3})(\d{3})(\d{3})(\d+)/, "$1-$2-$3-$4");
  else if (val.length > 6) return val.replace(/(\d{3})(\d{3})(\d+)/, "$1-$2-$3");
  else if (val.length > 3) return val.replace(/(\d{3})(\d+)/, "$1-$2");
  return val;
}

function formatSSS(val) {
  val = numbersOnly(val);
  if (val.length > 9) return val.replace(/(\d{2})(\d{7})(\d+)/, "$1-$2-$3");
  else if (val.length > 2) return val.replace(/(\d{2})(\d+)/, "$1-$2");
  return val;
}

function formatPhil(val) {
  val = numbersOnly(val);
  if (val.length > 11) return val.replace(/(\d{2})(\d{9})(\d+)/, "$1-$2-$3");
  else if (val.length > 2) return val.replace(/(\d{2})(\d+)/, "$1-$2");
  return val;
}

function formatPagIbig(val) {
  val = numbersOnly(val);
  if (val.length > 8) return val.replace(/(\d{4})(\d{4})(\d+)/, "$1-$2-$3");
  else if (val.length > 4) return val.replace(/(\d{4})(\d+)/, "$1-$2");
  return val;
}

// APPLY ON LOAD + INPUT
window.addEventListener("DOMContentLoaded", function () {
  const tin = document.querySelector("input[name='tinNo']");
  const sss = document.querySelector("input[name='sssNo']");
  const phil = document.querySelector("input[name='philHealthNo']");
  const pagibig = document.querySelector("input[name='pagIbigNo']");

  // 👉 Check muna kung exist (iwas error)
  if (tin) {
    tin.value = formatTIN(tin.value);
    tin.addEventListener("input", () => {
      tin.value = formatTIN(tin.value);
    });
  }

  if (sss) {
    sss.value = formatSSS(sss.value);
    sss.addEventListener("input", () => {
      sss.value = formatSSS(sss.value);
    });
  }

  if (phil) {
    phil.value = formatPhil(phil.value);
    phil.addEventListener("input", () => {
      phil.value = formatPhil(phil.value);
    });
  }

  if (pagibig) {
    pagibig.value = formatPagIbig(pagibig.value);
    pagibig.addEventListener("input", () => {
      pagibig.value = formatPagIbig(pagibig.value);
    });
  }
});

// reusable validation
function validateLength(input, expectedLength, messageEl) {
  input.addEventListener("input", function () {
    let rawValue = input.value.replace(/-/g, ""); // tanggal dash
    let length = rawValue.length;

    if (length === 0) {
      messageEl.textContent = "";
    } else if (length < expectedLength) {
      messageEl.textContent = "❌ Kulang pa";
      messageEl.style.color = "orange";
    } else if (length > expectedLength) {
      messageEl.textContent = "❌ Sobra";
      messageEl.style.color = "red";
    } else {
      messageEl.textContent = "✅ Sakto";
      messageEl.style.color = "green";
    }
  });
}

// auto format function (example: 1234-5678-9012)
function autoFormat(input, formatArray) {
  input.addEventListener("input", function () {
    let value = input.value.replace(/\D/g, "");
    let result = "";
    let index = 0;

    for (let i = 0; i < formatArray.length; i++) {
      if (value.length > index) {
        result += value.substr(index, formatArray[i]);
        index += formatArray[i];
        if (i < formatArray.length - 1 && value.length > index) {
          result += "-";
        }
      }
    }

    input.value = result;
  });
}

// inputs
const tinInputs = document.getElementById("tinNo");
const sssInputs = document.getElementById("sssNo");
const philInputs = document.getElementById("philHealthNo");
const pagibigInputs = document.getElementById("pagIbigNo");

const btnSubmit = document.getElementById("submitBtn");

// apply format
autoFormat(tinInputs, [3, 3, 3, 5]); // example TIN format
autoFormat(sssInputs, [2, 7, 1]); // SSS
autoFormat(philInputs, [2, 9, 1]); // PhilHealth
autoFormat(pagibigInputs, [4, 4, 4]); // Pag-IBIG

// apply validation
validateLength(tinInputs, 14, document.getElementById("tinMsg"));
validateLength(sssInputs, 10, document.getElementById("sssMsg"));
validateLength(philInputs, 12, document.getElementById("philMsg"));
validateLength(pagibigInputs, 12, document.getElementById("pagibigMsg"));

// check form validity
function checkFormValidity() {
  if (tinInputs.value.replace(/-/g, "").length === 14 && sssInputs.value.replace(/-/g, "").length === 10 && philInputs.value.replace(/-/g, "").length === 12 && pagibigInputs.value.replace(/-/g, "").length === 12) {
    btnSubmit.disabled = false;
  } else {
    btnSubmit.disabled = true;
  }
}

// real-time check
tinInputs.addEventListener("input", checkFormValidity);
sssInputs.addEventListener("input", checkFormValidity);
philInputs.addEventListener("input", checkFormValidity);
pagibigInputs.addEventListener("input", checkFormValidity);

// remove dashes before submit
function removeDashes(value) {
  return value.replace(/-/g, "");
}

document.querySelector("form").addEventListener("submit", function () {
  tinInputs.value = removeDashes(tinInputs.value);
  sssInputs.value = removeDashes(sssInputs.value);
  philInputs.value = removeDashes(philInputs.value);
  pagibigInputs.value = removeDashes(pagibigInputs.value);
});

// ✅ IDAGDAG ITO BAGO ANG MGA SWAL CONDITIONS
const urlParams = new URLSearchParams(window.location.search);

if (urlParams.get("updated") === "1") {
  Swal.fire({
    icon: "success",
    title: "Updated!",
    text: "Employee details updated successfully.",
    confirmButtonColor: "#294169",
    timer: 2500,
    timerProgressBar: true,
  });
  window.history.replaceState({}, "", window.location.pathname + "?id=" + urlParams.get("id"));
}

if (urlParams.get("error") === "1") {
  Swal.fire({
    icon: "error",
    title: "Update Failed!",
    text: "Something went wrong. Please try again.",
    confirmButtonColor: "#e05252",
  });
  window.history.replaceState({}, "", window.location.pathname + "?id=" + urlParams.get("id"));
}
// Password validation
const passwordInput = document.querySelector("input[name='Password']");
const passMsg = document.getElementById("passMsg");

passwordInput.addEventListener("input", function () {
  const length = passwordInput.value.length;

  if (length === 0) {
    passMsg.textContent = "";
  } else if (length < 10) {
    passMsg.textContent = "❌ Password must be at least 10 characters";
    passMsg.style.color = "red";
  } else {
    passMsg.textContent = "✅ Good";
    passMsg.style.color = "green";
  }
});
function checkFormValidity() {
  if (
    tinInputs.value.replace(/-/g, "").length === 14 &&
    sssInputs.value.replace(/-/g, "").length === 10 &&
    philInputs.value.replace(/-/g, "").length === 12 &&
    pagibigInputs.value.replace(/-/g, "").length === 12 &&
    passwordInput.value.length >= 10 // ✅ IDAGDAG ITO
  ) {
    btnSubmit.disabled = false;
  } else {
    btnSubmit.disabled = true;
  }
}

// ✅ IDAGDAG DIN ITO para real-time check
passwordInput.addEventListener("input", checkFormValidity);
document.querySelector("form").addEventListener("submit", function (e) {
  e.preventDefault();

  tinInputs.value = removeDashes(tinInputs.value);
  sssInputs.value = removeDashes(sssInputs.value);
  philInputs.value = removeDashes(philInputs.value);
  pagibigInputs.value = removeDashes(pagibigInputs.value);

  Swal.fire({
    icon: "question",
    title: "Update Employee?",
    text: "Are you sure you want to save the changes?",
    showCancelButton: true,
    confirmButtonText: "Yes, Update",
    cancelButtonText: "Cancel",
    confirmButtonColor: "#294169",
    cancelButtonColor: "#e05252",
  }).then((result) => {
    if (result.isConfirmed) {
      // ✅ SHOW SUCCESS MUNA BAGO SUBMIT
      Swal.fire({
        icon: "success",
        title: "Updated!",
        text: "Employee details updated successfully.",
        confirmButtonColor: "#294169",
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false,
      }).then(() => {
        e.target.submit(); // ✅ SUBMIT PAGKATAPOS NG ALERT
      });
    }
  });
});

// ── Sidebar toggle ────────────────────────────────────────────────────────────
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("collapsed");
}

// ── Logout confirmation ───────────────────────────────────────────────────────
function confirmLogout() {
  const overlay = document.getElementById("logoutOverlay");
  overlay.style.display = "flex"; // ✅ directly set display
}

function cancelLogout() {
  const overlay = document.getElementById("logoutOverlay");
  overlay.style.display = "none"; // ✅ hide it back
}

function doLogout() {
  window.location.href = "../process/logout.php"; // adjust path if needed
}

// Close overlay on backdrop click
document.addEventListener("DOMContentLoaded", () => {
  const overlay = document.getElementById("logoutOverlay");
  if (overlay) {
    overlay.addEventListener("click", (e) => {
      if (e.target === overlay) cancelLogout();
    });
  }
});
