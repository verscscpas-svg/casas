function toggleAccordion(wrapperId) {
  const wrapper = document.getElementById(wrapperId);
  wrapper.classList.toggle("active");
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
  const wrappers = document.querySelectorAll(".wrapper");
  wrappers.forEach((wrapper) => {
    if (isInViewport(wrapper)) {
      wrapper.classList.add("scroll-in");
    } else {
      wrapper.classList.remove("scroll-in"); // optional: remove when out of view
    }
  });
}

// Run on scroll and page load
window.addEventListener("scroll", animateOnScroll);
window.addEventListener("load", animateOnScroll);

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("contactForm");
  const submitBtn = form.querySelector('input[type="submit"]');
  const submittingDiv = form.querySelector(".submitting");
  const responseDiv = form.querySelector(".form-response");

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // STOP normal form submission

    // Prevent double submission
    if (form.dataset.submitted === "true") return;
    form.dataset.submitted = "true";

    // Disable button and show spinner
    submitBtn.disabled = true;
    submitBtn.value = "Sending...";
    submittingDiv.style.display = "block";
    responseDiv.innerHTML = "";

    // Collect form data
    const formData = new FormData(form);

    // Send via AJAX
    fetch(form.action, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.text())
      .then((data) => {
        submittingDiv.style.display = "none"; // hide spinner
        responseDiv.innerHTML = data; // show PHP message
        submitBtn.disabled = false;
        submitBtn.value = "Send Message";

        // Reset form if success
        if (data.toLowerCase().includes("message sent successfully")) {
          form.reset();
          form.dataset.submitted = "false"; // allow future submissions
          if (typeof grecaptcha !== "undefined") {
            grecaptcha.reset(); // Reset the reCAPTCHA
          }
        } else {
          form.dataset.submitted = "false"; // allow retry if failed
        }
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

gtag("config", "UA-23581568-13");
// Auto-update copyright year
document.addEventListener("DOMContentLoaded", function () {
  const yearEl = document.getElementById("csl-footer-year");
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }
});
