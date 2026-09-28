window.dataLayer = window.dataLayer || [];
function gtag() {
  dataLayer.push(arguments);
}
gtag("js", new Date());

gtag("config", "UA-23581568-13");

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

gtag("config", "UA-23581568-13");

// Auto-update copyright year
document.addEventListener("DOMContentLoaded", function () {
  const yearEl = document.getElementById("csl-footer-year");
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }
}); // ✅ }) lang — ) muna, tapos }
