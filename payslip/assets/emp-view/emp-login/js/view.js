// =============================================================================
// SIDEBAR CONTROLS (Mobile & Desktop)
// =============================================================================

function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");
  if (!sidebar) return;

  const isOpen = sidebar.classList.toggle("active");
  if (overlay) {
    overlay.style.display = isOpen ? "block" : "none";
  }
}

function openSidebar() {
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");

  if (sidebar) sidebar.classList.add("active");
  if (overlay) overlay.style.display = "block";
}

function closeSidebar() {
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");

  if (sidebar) sidebar.classList.remove("active");
  if (overlay) overlay.style.display = "none";
}

// =============================================================================
// QR MODAL CONTROLS
// =============================================================================

function openQRModal(imgSrc, empName) {
  const qrImage = document.getElementById("qrImage");
  const qrEmployeeName = document.getElementById("qrEmployeeName");
  const qrModalOverlay = document.getElementById("qrModalOverlay");

  if (qrImage) qrImage.src = imgSrc;
  if (qrEmployeeName) qrEmployeeName.textContent = empName || "";

  if (qrModalOverlay) {
    // Sinusuportahan pareho ang class 'active' o display flex depende sa CSS mo
    qrModalOverlay.classList.add("active");
    qrModalOverlay.style.display = "flex";
  }
}

function closeQRModal(event) {
  const qrModalOverlay = document.getElementById("qrModalOverlay");
  if (!qrModalOverlay) return;

  // Isasara kapag click sa labas, click sa close button, o pinilit isara (walang event)
  if (!event || event.target === qrModalOverlay || event.target.classList.contains("qr-modal-close")) {
    qrModalOverlay.classList.remove("active");
    qrModalOverlay.style.display = "none";
  }
}

// =============================================================================
// SENSITIVE INFO TOGGLE (SSS, TIN, PhilHealth, etc.)
// =============================================================================

function toggleSensitive() {
  const masked = document.querySelectorAll(".sensitive-masked");
  const real = document.querySelectorAll(".sensitive-real");
  const eyeOff = document.getElementById("mainEyeOff");
  const eyeOn = document.getElementById("mainEyeOn");

  if (!real.length) return;

  const isHidden = real[0].style.display === "none";

  masked.forEach((el) => (el.style.display = isHidden ? "none" : "inline"));
  real.forEach((el) => (el.style.display = isHidden ? "inline" : "none"));

  if (eyeOff) eyeOff.style.display = isHidden ? "none" : "inline";
  if (eyeOn) eyeOn.style.display = isHidden ? "inline" : "none";
}

// =============================================================================
// NET PAY TOGGLE CONTROLS
// =============================================================================

function toggleNetPayCol() {
  const masked = document.querySelectorAll(".np-masked");
  const real = document.querySelectorAll(".np-real");
  const eyeOff = document.getElementById("npEyeOff");
  const eyeOn = document.getElementById("npEyeOn");

  if (!real.length) return;

  const isHidden = real[0].style.display === "none";

  masked.forEach((el) => (el.style.display = isHidden ? "none" : "inline"));
  real.forEach((el) => (el.style.display = isHidden ? "inline" : "none"));

  if (eyeOff) eyeOff.style.display = isHidden ? "none" : "inline";
  if (eyeOn) eyeOn.style.display = isHidden ? "inline" : "none";
}

// =============================================================================
// PROOF OF PAYMENT MODAL CONTROLS
// =============================================================================

function viewProofOfPayment(btn) {
  const image = btn.getAttribute("data-image");
  const label = btn.getAttribute("data-label");

  const viewProofTitle = document.getElementById("viewProofTitle");
  const viewProofImage = document.getElementById("viewProofImage");
  const viewProofModal = document.getElementById("viewProofModal");

  if (viewProofTitle) viewProofTitle.textContent = label;
  if (viewProofImage) {
    // Inayos ang path para sigurado kung saan kukuha ng image file
    viewProofImage.src = "../../employee/process/uploads/proof/" + image;
  }
  if (viewProofModal) viewProofModal.style.display = "flex";
}

function closeViewProofModal() {
  const viewProofModal = document.getElementById("viewProofModal");
  const viewProofImage = document.getElementById("viewProofImage");

  if (viewProofModal) viewProofModal.style.display = "none";
  if (viewProofImage) viewProofImage.src = "";
}

// =============================================================================
// LOGOUT MODAL SYSTEM
// =============================================================================

function confirmLogout() {
  const overlay = document.getElementById("logoutOverlay");
  if (overlay) overlay.style.display = "flex";
}

function cancelLogout() {
  const overlay = document.getElementById("logoutOverlay");
  if (overlay) overlay.style.display = "none";
}

function doLogout() {
  window.location.href = "process/logout.php";
}

// =============================================================================
// PAGE NAVIGATION
// =============================================================================

function viewEmp(id) {
  window.location.href = "current-payslip.php?id=" + id;
}

// =============================================================================
// AUTOMATIC EVENT LISTENERS ON LOAD
// =============================================================================

document.addEventListener("DOMContentLoaded", () => {
  // 1. Sidebar overlay listener
  const sidebarOverlay = document.getElementById("sidebarOverlay");
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener("click", closeSidebar);
  }

  // 2. Logout overlay background click handler
  const logoutOverlay = document.getElementById("logoutOverlay");
  if (logoutOverlay) {
    logoutOverlay.addEventListener("click", (e) => {
      if (e.target === logoutOverlay) cancelLogout();
    });
  }

  // 3. View proof modal background click handler
  const viewProofModal = document.getElementById("viewProofModal");
  if (viewProofModal) {
    viewProofModal.addEventListener("click", function (e) {
      if (e.target === this) closeViewProofModal();
    });
  }

  // 4. QR Modal background click handler
  const qrModalOverlay = document.getElementById("qrModalOverlay");
  if (qrModalOverlay) {
    qrModalOverlay.addEventListener("click", closeQRModal);
  }

  // 5. ESC Key integration para isara lahat ng modals nang sabay-sabay
  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    closeQRModal();
    closeViewProofModal();
    cancelLogout();
    closeSidebar();
  });

  // 6. Sidebar active state highlighter base sa kasalukuyang web URL
  const currentUrl = window.location.href;
  const sidebarItems = document.querySelectorAll(".sidebar .list li"); // Inayos ang selector (.li li sa luma mo ay mali)

  sidebarItems.forEach((item) => {
    const clickAttr = item.getAttribute("onclick");
    if (clickAttr && clickAttr.includes("'")) {
      const pageName = clickAttr.split("'")[1];
      if (currentUrl.includes(pageName)) {
        item.classList.add("active");
      }
    }
  });
});
