// ── Sidebar Toggle ────────────────────────────────────────────────────────────
function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");
  const isOpen = sidebar.classList.toggle("active");
  overlay.classList.toggle("active", isOpen);
}

function openSidebar() {
  document.getElementById("sidebar").classList.add("active");
  document.getElementById("sidebarOverlay").classList.add("active");
}

function closeSidebar() {
  document.getElementById("sidebar").classList.remove("active");
  document.getElementById("sidebarOverlay").classList.remove("active");
}

// ── Logout Modal ──────────────────────────────────────────────────────────────
function confirmLogout() {
  document.getElementById("logoutOverlay").style.display = "flex";
}

function cancelLogout() {
  document.getElementById("logoutOverlay").style.display = "none";
}

function doLogout() {
  window.location.href = "logout.php";
}

// ── DOMContentLoaded ──────────────────────────────────────────────────────────
document.addEventListener("DOMContentLoaded", function () {
  // Close logout overlay on backdrop click
  const logoutOverlay = document.getElementById("logoutOverlay");
  if (logoutOverlay) {
    logoutOverlay.addEventListener("click", (e) => {
      if (e.target === logoutOverlay) cancelLogout();
    });
  }

  // Real-time preview for Profile Image Upload
  const imgUpload = document.getElementById("imageUpload");
  if (imgUpload) {
    imgUpload.addEventListener("change", function () {
      readURL(this, "previewImg");
    });
  }

  // Real-time preview for Bank QR Code Upload
  const qrUpload = document.getElementById("bankqrUpload");
  if (qrUpload) {
    qrUpload.addEventListener("change", function () {
      readURL(this, "previewBankQr");
    });
  }
});

// ── File Preview Helper ───────────────────────────────────────────────────────
function readURL(input, previewId) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      document.getElementById(previewId).setAttribute("src", e.target.result);
    };
    reader.readAsDataURL(input.files[0]); // ✅ Fixed: was reader.読み込む(...)
  }
}
