// ── Password toggle ───────────────────────────────────────────────────────────
const togglePass = document.getElementById("togglePass");
const passInput = document.getElementById("pass");
const eyeShow = document.getElementById("eyeShow");
const eyeHide = document.getElementById("eyeHide");

togglePass.addEventListener("click", () => {
  const isPassword = passInput.type === "password";
  passInput.type = isPassword ? "text" : "password";
  eyeShow.style.display = isPassword ? "none" : "block";
  eyeHide.style.display = isPassword ? "block" : "none";
});

// ── Subtle card lift on mouse move (right panel only) ─────────────────────────
const card = document.querySelector(".card");
const rightPanel = document.querySelector(".right-panel");

rightPanel.addEventListener("mousemove", (e) => {
  const r = rightPanel.getBoundingClientRect();
  const cx = r.left + r.width / 2;
  const cy = r.top + r.height / 2;
  const rx = ((e.clientY - cy) / (r.height / 2)) * 3.5;
  const ry = -((e.clientX - cx) / (r.width / 2)) * 3.5;
  card.style.transform = `perspective(900px) rotateX(${rx}deg) rotateY(${ry}deg)`;
});
rightPanel.addEventListener("mouseleave", () => {
  card.style.transform = "perspective(900px) rotateX(0) rotateY(0)";
  card.style.transition = "transform .5s";
});
rightPanel.addEventListener("mouseenter", () => {
  card.style.transition = "transform .1s";
});

// ── Helpers ───────────────────────────────────────────────────────────────────
function setLoading(state) {
  const btn = document.getElementById("loginBtn");
  btn.classList.toggle("loading", state);
  btn.disabled = state;
}

function clearErrors() {
  document
    .querySelectorAll(".input-error")
    .forEach((el) => el.classList.remove("input-error"));
  document.querySelectorAll(".field-error").forEach((el) => el.remove());
  const banner = document.querySelector(".login-error-banner");
  if (banner) banner.remove();
}

function showBannerError(msg) {
  const old = document.querySelector(".login-error-banner");
  if (old) old.remove();

  const banner = document.createElement("div");
  banner.className = "login-error-banner";
  banner.style.cssText = `
    background: rgba(224,82,82,.12);
    border: 1px solid rgba(224,82,82,.35);
    color: #e05252;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 13px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
  `;
  banner.innerHTML = `
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="12" cy="12" r="10"/>
      <line x1="12" y1="8" x2="12" y2="12"/>
      <line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    ${msg}
  `;

  const btn = document.getElementById("loginBtn");
  btn.parentNode.insertBefore(banner, btn);
}

// ── Login handler ─────────────────────────────────────────────────────────────
function doLogin() {
  const user = document.getElementById("user").value.trim();
  const pass = document.getElementById("pass").value.trim();

  clearErrors();

  if (!user || !pass) {
    showBannerError("Please enter your Employee ID / Email and password.");
    return;
  }

  setLoading(true);

  const formData = new FormData();
  formData.append("user", user);
  formData.append("pass", pass);

  fetch("assets/forindex/login_process.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        // Show the existing success overlay, then redirect
        document.getElementById("successOverlay").classList.add("show");
        setTimeout(() => {
          window.location.href = data.redirect || "../employee/index.php";
        }, 1800);
      } else {
        setLoading(false);
        showBannerError(
          data.message || "Invalid credentials. Please try again.",
        );
      }
    })
    .catch(() => {
      setLoading(false);
      showBannerError("Server error. Please try again later.");
    });
}

// ── Attach events ─────────────────────────────────────────────────────────────
document.getElementById("loginBtn").addEventListener("click", doLogin);

document.addEventListener("keydown", (e) => {
  if (e.key === "Enter") doLogin();
});
