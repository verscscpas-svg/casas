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
    submittingDiv.style.display = "block";
    responseDiv.innerHTML = "";

    fetch(form.action, { method: "POST", body: new FormData(form) })
      .then((res) => res.text())
      .then((data) => {
        submittingDiv.style.display = "none";
        responseDiv.innerHTML = data;
        submitBtn.disabled = false;
        submitBtn.value = "Send Message";

        if (data.toLowerCase().includes("message sent successfully")) {
          form.reset();
          if (typeof grecaptcha !== "undefined") grecaptcha.reset();
        }
        form.dataset.submitted = "false";
      })
      .catch((err) => {
        submittingDiv.style.display = "none";
        responseDiv.innerHTML = "An error occurred. Please try again.";
        submitBtn.disabled = false;
        submitBtn.value = "Send Message";
        form.dataset.submitted = "false";
        console.error(err);
      });
  });
});
