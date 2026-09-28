let currentProofPayslipId = null;

// ─── VIEW PROOF ───────────────────────────────────────────────
function viewProofOfPayment(btn) {
  currentProofPayslipId = btn.getAttribute("data-id");
  const image = btn.getAttribute("data-image");
  const label = btn.getAttribute("data-label");

  document.getElementById("viewProofTitle").textContent = label;
  document.getElementById("viewProofImage").src =
    "process/uploads/proof/" + image;
  document.getElementById("viewProofModal").style.display = "flex";
}

function closeViewProofModal() {
  document.getElementById("viewProofModal").style.display = "none";
  document.getElementById("viewProofImage").src = "";
  currentProofPayslipId = null;
}

// ─── DELETE PROOF (triggered inside View Modal) ───────────────
function deleteProofFromModal() {
  const label = document.getElementById("viewProofTitle").textContent;

  Swal.fire({
    title: "Delete Proof of Payment?",
    html: `<span style="font-size:14px;color:#6b7280;">Are you sure you want to delete the proof for:<br><strong>${label}</strong></span><br><br>
               <span style="font-size:13px;color:#ef4444;">This action cannot be undone.</span>`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#6b7280",
    confirmButtonText: "Yes, Delete",
    cancelButtonText: "Cancel",
    reverseButtons: true,
    focusCancel: true,
  }).then((result) => {
    if (result.isConfirmed) {
      confirmDeleteProof();
    }
  });
}

function confirmDeleteProof() {
  if (!currentProofPayslipId) return;

  const formData = new FormData();
  formData.append("payslip_id", currentProofPayslipId);

  Swal.fire({
    title: "Deleting...",
    text: "Please wait.",
    allowOutsideClick: false,
    allowEscapeKey: false,
    didOpen: () => Swal.showLoading(),
  });

  fetch("process/delete_proof.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        Swal.fire({
          icon: "success",
          title: "Deleted!",
          text: "Proof of payment has been deleted.",
          confirmButtonColor: "#10b981",
          timer: 2000,
          timerProgressBar: true,
        }).then(() => {
          closeViewProofModal();
          location.reload();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Delete Failed",
          text: data.message || "Something went wrong.",
          confirmButtonColor: "#dc2626",
        });
      }
    })
    .catch(() => {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "An unexpected error occurred. Please try again.",
        confirmButtonColor: "#dc2626",
      });
    });
}

// ─── UPLOAD PROOF ─────────────────────────────────────────────
function uploadProofOfPayment(btn) {
  const id = btn.getAttribute("data-id");
  const label = btn.getAttribute("data-label");

  document.getElementById("uploadPayslipId").value = id;
  document.getElementById("uploadProofTitle").textContent =
    "Upload Proof – " + label;
  document.getElementById("proofFileInput").value = "";
  document.getElementById("uploadPreviewContainer").style.display = "none";
  document.getElementById("uploadPreviewImage").src = "";
  document.getElementById("uploadProofModal").style.display = "flex";
}

function closeUploadProofModal() {
  document.getElementById("uploadProofModal").style.display = "none";
}

function submitProofOfPayment() {
  const payslipId = document.getElementById("uploadPayslipId").value;
  const fileInput = document.getElementById("proofFileInput");

  if (!fileInput.files.length) {
    Swal.fire({
      icon: "warning",
      title: "No File Selected",
      text: "Please select an image before uploading.",
      confirmButtonColor: "#f59e0b",
    });
    return;
  }

  Swal.fire({
    title: "Uploading...",
    text: "Please wait while we upload your file.",
    allowOutsideClick: false,
    allowEscapeKey: false,
    didOpen: () => Swal.showLoading(),
  });

  const formData = new FormData();
  formData.append("payslip_id", payslipId);
  formData.append("proof_image", fileInput.files[0]);

  fetch("process/upload_proof.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        Swal.fire({
          icon: "success",
          title: "Uploaded!",
          text: "Proof of payment uploaded successfully.",
          confirmButtonColor: "#10b981",
          timer: 2000,
          timerProgressBar: true,
        }).then(() => {
          closeUploadProofModal();
          location.reload();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Upload Failed",
          text: data.message || "Something went wrong.",
          confirmButtonColor: "#dc2626",
        });
      }
    })
    .catch(() => {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "An unexpected error occurred. Please try again.",
        confirmButtonColor: "#dc2626",
      });
    });
}

// ─── FILE PREVIEW ─────────────────────────────────────────────
document
  .getElementById("proofFileInput")
  .addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        document.getElementById("uploadPreviewImage").src = e.target.result;
        document.getElementById("uploadPreviewContainer").style.display =
          "block";
      };
      reader.readAsDataURL(file);
    }
  });

// ─── DELETE PAYSLIP ───────────────────────────────────────────
document.querySelectorAll(".delete-btn").forEach((btn) => {
  btn.addEventListener("click", function () {
    const id = this.dataset.id;
    const label = this.dataset.label;
    const row = this.closest("tr");

    Swal.fire({
      title: "Delete Payslip?",
      text: label,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#dc2626",
      cancelButtonColor: "#6b7280",
      confirmButtonText: "Yes, Delete",
      cancelButtonText: "Cancel",
      reverseButtons: true,
      focusCancel: true,
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: "Deleting...",
          text: "Please wait.",
          allowOutsideClick: false,
          allowEscapeKey: false,
          didOpen: () => Swal.showLoading(),
        });

        fetch("process/delete-payslip.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: id }),
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.status === "success") {
              Swal.fire({
                icon: "success",
                title: "Deleted!",
                text: "Payslip has been deleted.",
                confirmButtonColor: "#10b981",
                timer: 2000,
                timerProgressBar: true,
              }).then(() => row.remove());
            } else {
              Swal.fire({
                icon: "error",
                title: "Delete Failed",
                text: data.message || "Something went wrong.",
                confirmButtonColor: "#dc2626",
              });
            }
          })
          .catch(() => {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "An unexpected error occurred. Please try again.",
              confirmButtonColor: "#dc2626",
            });
          });
      }
    });
  });
});

// ─── QR MODAL ─────────────────────────────────────────────────
function openQRModal(imgSrc, empName = "") {
  document.getElementById("qrImage").src = imgSrc;
  if (empName) {
    document.getElementById("qrEmployeeName").textContent = empName;
  }
  document.getElementById("qrModalOverlay").classList.add("active");
}

function closeQRModal(event) {
  if (!event || event.target === document.getElementById("qrModalOverlay")) {
    document.getElementById("qrModalOverlay").classList.remove("active");
  }
}

// ─── CLOSE MODALS ON OUTSIDE CLICK ────────────────────────────
document
  .getElementById("viewProofModal")
  .addEventListener("click", function (e) {
    if (e.target === this) closeViewProofModal();
  });
document
  .getElementById("uploadProofModal")
  .addEventListener("click", function (e) {
    if (e.target === this) closeUploadProofModal();
  });

// ─── ESCAPE KEY ───────────────────────────────────────────────
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    closeViewProofModal();
    closeUploadProofModal();
    closeQRModal();
  }
});

// js file css
// ─── FILE INPUT ENHANCEMENTS ──────────────────────────────────
const dropZone = document.getElementById("dropZone");
const fileChosen = document.getElementById("fileChosen");
const chosenName = document.getElementById("chosenName");
const chosenSize = document.getElementById("chosenSize");
const fileRemove = document.getElementById("fileRemove");

function showChosenFile(file) {
  chosenName.textContent = file.name;
  chosenSize.textContent = (file.size / 1024).toFixed(1) + " KB";
  fileChosen.classList.add("visible");
}

function clearChosenFile() {
  document.getElementById("proofFileInput").value = "";
  fileChosen.classList.remove("visible");
  document.getElementById("uploadPreviewContainer").style.display = "none";
  document.getElementById("uploadPreviewImage").src = "";
}

fileRemove.addEventListener("click", function (e) {
  e.stopPropagation();
  clearChosenFile();
});

dropZone.addEventListener("dragover", (e) => {
  e.preventDefault();
  dropZone.classList.add("dragover");
});
dropZone.addEventListener("dragleave", () =>
  dropZone.classList.remove("dragover"),
);
dropZone.addEventListener("drop", (e) => {
  e.preventDefault();
  dropZone.classList.remove("dragover");
  const file = e.dataTransfer.files[0];
  if (file) {
    document.getElementById("proofFileInput").files = e.dataTransfer.files;
    showChosenFile(file);
  }
});

// diko alam para san to
document
  .getElementById("proofFileInput")
  .addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
      showChosenFile(file); // ✅ dagdag ito
      const reader = new FileReader();
      reader.onload = function (e) {
        document.getElementById("uploadPreviewImage").src = e.target.result;
        document.getElementById("uploadPreviewContainer").style.display =
          "block";
      };
      reader.readAsDataURL(file);
    }
  });
