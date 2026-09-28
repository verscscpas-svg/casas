// Login button
const btn = document.getElementById("loginBtn");
btn.addEventListener("click", () => {
  btn.classList.add("loading");
  setTimeout(() => {
    btn.classList.remove("loading");
    document.getElementById("successOverlay").classList.add("show");
  }, 2000);
});
document.addEventListener("keydown", (e) => {
  if (e.key === "Enter") btn.click();
});

// Subtle card lift on mouse move (right panel only)
const card = document.querySelector(".card");
const rightPanel = document.querySelector(".right-panel");
rightPanel.addEventListener("mousemove", (e) => {
  const r = rightPanel.getBoundingClientRect();
  const cx = r.left + r.width / 2;
  const cy = r.top + r.height / 2;
  const rx = ((e.clientY - cy) / (r.height / 2)) * 3.5;
  const ry = (-(e.clientX - cx) / (r.width / 2)) * 3.5;
  card.style.transform = `perspective(900px) rotateX(${rx}deg) rotateY(${ry}deg)`;
});
rightPanel.addEventListener("mouseleave", () => {
  card.style.transform = "perspective(900px) rotateX(0) rotateY(0)";
  card.style.transition = "transform .5s";
});
rightPanel.addEventListener("mouseenter", () => {
  card.style.transition = "transform .1s";
});

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
