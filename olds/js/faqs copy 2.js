// Custom Alert Function
function showCustomAlert({ type = "success", title = "", text = "", duration = 3000, onClose = null }) {
  const overlay = document.getElementById("custom-alert-overlay");
  const stripe = document.getElementById("ca-stripe");
  const ring = document.getElementById("ca-ring");
  const icon = document.getElementById("ca-icon");
  const label = document.getElementById("ca-label");
  const titleEl = document.getElementById("custom-alert-title");
  const textEl = document.getElementById("custom-alert-text");
  const fill = document.getElementById("custom-alert-timer-fill");
  const timerBar = document.getElementById("custom-alert-timer-bar");
  const btn = document.getElementById("custom-alert-btn");

  if (type === "success") {
    stripe.className = "ca-stripe success";
    ring.className = "ca-icon-ring success";
    icon.setAttribute("stroke", "#294169");
    icon.innerHTML = '<polyline points="20 6 9 17 4 12"/>';
    label.className = "ca-label success";
    label.textContent = "Status confirmed";
    btn.className = "success";
    timerBar.style.display = "block";
    fill.style.background = "#294169";
    fill.style.transition = "none";
    fill.style.width = "100%";
    requestAnimationFrame(() => {
      fill.style.transition = `width ${duration}ms linear`;
      fill.style.width = "0%";
    });
  } else {
    stripe.className = "ca-stripe error";
    ring.className = "ca-icon-ring error";
    icon.setAttribute("stroke", "#e05252");
    icon.innerHTML = '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>';
    label.className = "ca-label error";
    label.textContent = "Transmission failed";
    btn.className = "error";
    timerBar.style.display = "none";
  }

  titleEl.textContent = title;
  textEl.textContent = text;
  overlay.classList.add("show");

  let timer = null;
  const close = () => {
    clearTimeout(timer);
    overlay.classList.remove("show");
    if (onClose) onClose();
  };

  btn.onclick = close;
  if (type === "success") timer = setTimeout(close, duration);
}

// Toggle accordion
function toggleAccordion(wrapperId) {
  const wrapper = document.getElementById(wrapperId);
  wrapper.classList.toggle("active");
}

// Check if element is in viewport
function isInViewport(element) {
  const rect = element.getBoundingClientRect();
  return rect.top <= (window.innerHeight || document.documentElement.clientHeight) - 100;
}

// Scroll-triggered animation
function animateOnScroll() {
  document.querySelectorAll(".wrapper").forEach((wrapper) => {
    if (isInViewport(wrapper)) {
      wrapper.classList.add("scroll-in");
    } else {
      wrapper.classList.remove("scroll-in");
    }
  });
}

window.addEventListener("scroll", animateOnScroll);
window.addEventListener("load", animateOnScroll);

document.addEventListener("DOMContentLoaded", function () {
  // Auto-update copyright year
  const yearEl = document.getElementById("csl-footer-year");
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  // Contact form handler
  const form = document.getElementById("contactForm");
  if (!form) return;

  const submitBtn = form.querySelector('input[type="submit"]');
  const submittingDiv = form.querySelector(".submitting");
  const responseDiv = form.querySelector(".form-response");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    if (form.dataset.submitted === "true") return;
    form.dataset.submitted = "true";

    submitBtn.disabled = true;
    submitBtn.value = "Sending...";
    submittingDiv.style.display = "flex";
    responseDiv.innerHTML = "";

    fetch(form.action, { method: "POST", body: new FormData(form) })
      .then((res) => res.text())
      .then((data) => {
        submittingDiv.style.display = "none";
        submitBtn.disabled = false;
        submitBtn.value = "Send Message";

        if (data.toLowerCase().includes("message sent successfully")) {
          form.reset();
          if (typeof grecaptcha !== "undefined") grecaptcha.reset();

          // ✅ Custom success alert
          showCustomAlert({
            type: "success",
            title: "Message Sent!",
            text: "Your message was sent successfully. We'll get back to you soon.",
            duration: 6000,
            onClose: () => location.reload(),
          });
        } else {
          responseDiv.innerHTML = data;
          form.dataset.submitted = "false";
        }
      })
      .catch((err) => {
        submittingDiv.style.display = "none";
        submitBtn.disabled = false;
        submitBtn.value = "Send Message";
        form.dataset.submitted = "false";
        console.error(err);

        // ❌ Custom error alert
        showCustomAlert({
          type: "error",
          title: "Something went wrong",
          text: "An error occurred. Please try again.",
        });
      });
  });
});
