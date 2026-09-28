function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("collapsed");
}

function addemp() {
  window.location.href = "add-emp.php";
}

document
  .getElementById("imageUpload")
  .addEventListener("change", function (event) {
    const reader = new FileReader();

    reader.onload = function () {
      document.getElementById("previewImg").src = reader.result;
    };

    reader.readAsDataURL(event.target.files[0]);
  });

//   upd
// REMOVE NON-NUMBERS
function numbersOnly(value) {
  return value.replace(/\D/g, "");
}

// FORMAT FUNCTIONS
function formatTIN(val) {
  val = numbersOnly(val);
  if (val.length > 9)
    return val.replace(/(\d{3})(\d{3})(\d{3})(\d+)/, "$1-$2-$3-$4");
  else if (val.length > 6)
    return val.replace(/(\d{3})(\d{3})(\d+)/, "$1-$2-$3");
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
  if (
    tinInputs.value.replace(/-/g, "").length === 14 &&
    sssInputs.value.replace(/-/g, "").length === 10 &&
    philInputs.value.replace(/-/g, "").length === 12 &&
    pagibigInputs.value.replace(/-/g, "").length === 12
  ) {
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
