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
const rows = document.querySelectorAll(".vm-row");
const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        setTimeout(() => entry.target.classList.add("vm-visible"), i * 200);
        observer.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.15 },
);

rows.forEach((r) => observer.observe(r));
const el = document.querySelector(".aboutthem-inner");
const obs = new IntersectionObserver(
  ([e]) => {
    if (e.isIntersecting) {
      el.classList.add("at-visible");
      obs.unobserve(el);
    }
  },
  { threshold: 0.2 },
);
obs.observe(el);
