// Footer year
document.getElementById("csl-footer-year").textContent = new Date().getFullYear();

// Hero Slider
(function () {
  "use strict";
  var wrapper = document.querySelector(".slider-hero");
  var slider = document.querySelector(".hero-slider");
  if (!wrapper || !slider) return;
  var slides = Array.from(slider.querySelectorAll(".item"));
  if (!slides.length) return;
  var current = 0,
    AUTOPLAY_MS = 5500,
    timer = null;

  var prevBtn = document.createElement("button");
  prevBtn.className = "hero-arrow hero-arrow--prev";
  prevBtn.setAttribute("aria-label", "Previous slide");
  prevBtn.innerHTML = "&#8249;";
  var nextBtn = document.createElement("button");
  nextBtn.className = "hero-arrow hero-arrow--next";
  nextBtn.setAttribute("aria-label", "Next slide");
  nextBtn.innerHTML = "&#8250;";

  var dotsWrap = null,
    dots = [];
  if (slides.length > 1) {
    dotsWrap = document.createElement("div");
    dotsWrap.className = "hero-dots";
    slides.forEach(function (_, i) {
      var dot = document.createElement("button");
      dot.className = "hero-dot";
      dot.setAttribute("aria-label", "Go to slide " + (i + 1));
      dot.addEventListener("click", function () {
        goTo(i);
        restartAutoplay();
      });
      dotsWrap.appendChild(dot);
      dots.push(dot);
    });
  }
  wrapper.appendChild(prevBtn);
  if (slides.length > 1) wrapper.appendChild(nextBtn);
  if (dotsWrap) wrapper.appendChild(dotsWrap);

  function goTo(index) {
    slides[current].classList.remove("is-active");
    if (dots[current]) dots[current].classList.remove("is-active");
    current = (index + slides.length) % slides.length;
    slides[current].classList.add("is-active");
    if (dots[current]) dots[current].classList.add("is-active");
  }
  function next() {
    goTo(current + 1);
  }
  function prev() {
    goTo(current - 1);
  }
  function startAutoplay() {
    if (slides.length < 2) return;
    timer = setInterval(next, AUTOPLAY_MS);
  }
  function stopAutoplay() {
    if (timer) clearInterval(timer);
    timer = null;
  }
  function restartAutoplay() {
    stopAutoplay();
    startAutoplay();
  }

  prevBtn.addEventListener("click", function () {
    prev();
    restartAutoplay();
  });
  nextBtn.addEventListener("click", function () {
    next();
    restartAutoplay();
  });
  wrapper.addEventListener("mouseenter", stopAutoplay);
  wrapper.addEventListener("mouseleave", startAutoplay);
  wrapper.addEventListener("touchstart", stopAutoplay, { passive: true });
  wrapper.setAttribute("tabindex", "0");
  wrapper.addEventListener("keydown", function (e) {
    if (e.key === "ArrowLeft") {
      prev();
      restartAutoplay();
    }
    if (e.key === "ArrowRight") {
      next();
      restartAutoplay();
    }
  });

  slides.forEach(function (s) {
    s.classList.remove("is-active");
  });
  slides[0].classList.add("is-active");
  if (dots[0]) dots[0].classList.add("is-active");
  startAutoplay();
})();
