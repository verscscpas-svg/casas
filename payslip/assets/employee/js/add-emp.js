const salaryInput = document.getElementById("salary");

// Format when user leaves the input
salaryInput.addEventListener("blur", function (e) {
  let value = e.target.value.replace(/,/g, "").replace("₱", "").trim();

  if (!isNaN(value) && value !== "") {
    let number = parseFloat(value);

    e.target.value =
      "₱ " +
      number.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
  }
});

// Optional: remove ₱ when typing again
salaryInput.addEventListener("focus", function (e) {
  let value = e.target.value.replace(/,/g, "").replace("₱", "").trim();
  e.target.value = value;
});

function ClearForm() {
  document.querySelector("form").reset();
}

// for modal js

window.onload = function () {
  const params = new URLSearchParams(window.location.search);
  if (params.get("success") === "1") {
    document.getElementById("successModal").classList.add("show");

    // remove ?success=1 sa URL
    window.history.replaceState({}, document.title, window.location.pathname);
  }
};

function addMore() {
  window.location.href = "add-emp.php";
}

function goDone() {
  window.location.href = "index.php"; // palitan mo
}

// for ajax

const empInput = document.getElementById("empNo");
const empError = document.getElementById("empError");
const submitBtn = document.querySelector("button[type='submit']");

empInput.addEventListener("input", function () {
  let empNo = this.value;

  if (empNo !== "") {
    fetch("process/check_emp.php?empNo=" + empNo)
      .then((res) => res.text())
      .then((data) => {
        if (data === "exists") {
          empInput.style.border = "2px solid red";
          empError.style.display = "block";
          submitBtn.disabled = true;
        } else {
          empInput.style.border = "2px solid green";
          empError.style.display = "none";
          submitBtn.disabled = false;
        }
      });
  }
});

// tin number

const tinInput = document.getElementById("tinNo");

tinInput.addEventListener("input", function (e) {
  let value = e.target.value.replace(/\D/g, ""); // numbers only

  let formatted = "";

  if (value.length > 0) {
    formatted += value.substring(0, 3);
  }
  if (value.length > 3) {
    formatted += "-" + value.substring(3, 6);
  }
  if (value.length > 6) {
    formatted += "-" + value.substring(6, 9);
  }
  if (value.length > 9) {
    formatted += "-" + value.substring(9, 14);
  }

  e.target.value = formatted;
});

// for sss umber

const sssInput = document.getElementById("sssNo");

sssInput.addEventListener("input", function (e) {
  let value = e.target.value.replace(/\D/g, ""); // numbers only

  let formatted = "";

  if (value.length > 0) {
    formatted += value.substring(0, 2);
  }
  if (value.length > 2) {
    formatted += "-" + value.substring(2, 9);
  }
  if (value.length > 9) {
    formatted += "-" + value.substring(9, 10);
  }

  e.target.value = formatted;
});

// for phic

const phInput = document.getElementById("philHealthNo");

phInput.addEventListener("input", function (e) {
  let value = e.target.value.replace(/\D/g, ""); // numbers only

  let formatted = "";

  if (value.length > 0) {
    formatted = value.substring(0, 2);
  }
  if (value.length > 2) {
    formatted += "-" + value.substring(2, 11);
  }
  if (value.length > 11) {
    formatted += "-" + value.substring(11, 12);
  }

  e.target.value = formatted;
});

// hdmf

const pagibigInput = document.getElementById("pagIbigNo");

pagibigInput.addEventListener("input", function (e) {
  let value = e.target.value.replace(/\D/g, ""); // numbers only

  let formatted = "";

  if (value.length > 0) {
    formatted = value.substring(0, 4);
  }
  if (value.length > 4) {
    formatted += "-" + value.substring(4, 8);
  }
  if (value.length > 8) {
    formatted += "-" + value.substring(8, 12);
  }

  e.target.value = formatted;
});

const imageInput = document.getElementById("imageUpload");
const previewImg = document.getElementById("previewImg");

imageInput.addEventListener("change", function () {
  const file = this.files[0];

  if (file) {
    const reader = new FileReader();

    reader.onload = function (e) {
      previewImg.src = e.target.result;
      previewImg.style.display = "block";
    };

    reader.readAsDataURL(file);
  }
});

function validateLength(input, expectedLength, messageEl) {
  input.addEventListener("input", function () {
    let length = input.value.length;

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

// Apply validation
validateLength(
  document.getElementById("tinNo"),
  17,
  document.getElementById("tinMsg"),
);
validateLength(
  document.getElementById("sssNo"),
  12,
  document.getElementById("sssMsg"),
);
validateLength(
  document.getElementById("philHealthNo"),
  14,
  document.getElementById("philMsg"),
);
validateLength(
  document.getElementById("pagIbigNo"),
  14,
  document.getElementById("pagibigMsg"),
);

const tinInputs = document.getElementById("tinNo");
const sssInputs = document.getElementById("sssNo");
const philInputs = document.getElementById("philHealthNo");
const pagibigInputs = document.getElementById("pagIbigNo");
const btnSubmit = document.getElementById("submitBtn");

function checkFormValidity() {
  if (
    tinInputs.value.length === 17 &&
    sssInputs.value.length === 12 &&
    philInputs.value.length === 14 &&
    pagibigInputs.value.length === 14
  ) {
    btnSubmit.disabled = false;
  } else {
    btnSubmit.disabled = true;
  }
}

// real-time check habang nagta-type
tinInputs.addEventListener("input", checkFormValidity);
sssInputs.addEventListener("input", checkFormValidity);
philInputs.addEventListener("input", checkFormValidity);
pagibigInputs.addEventListener("input", checkFormValidity);

// remove dashhhhhhhhhhhhh ------------

function removeDashes(value) {
  return value.replace(/-/g, "");
}

document.querySelector("form").addEventListener("submit", function () {
  document.getElementById("tinNo").value = removeDashes(tinNo.value);
  document.getElementById("sssNo").value = removeDashes(sssNo.value);
  document.getElementById("philHealthNo").value = removeDashes(philInput.value);
  document.getElementById("pagIbigNo").value = removeDashes(pagibigInput.value);
});
