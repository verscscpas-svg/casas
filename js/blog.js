document.getElementById("yr").textContent = new Date().getFullYear();

// ── Carousel ──
const carouselState = {};

function moveCarousel(carouselId, direction) {
  const carousel = document.getElementById(carouselId);
  const track = carousel.querySelector(".cb-carousel-track");
  const total = track.querySelectorAll("img").length;
  if (!carouselState[carouselId]) carouselState[carouselId] = 0;
  carouselState[carouselId] = (carouselState[carouselId] + direction + total) % total;
  updateCarousel(carouselId);
}

function goToSlide(carouselId, index) {
  carouselState[carouselId] = index;
  updateCarousel(carouselId);
}

function updateCarousel(carouselId) {
  const carousel = document.getElementById(carouselId);
  const track = carousel.querySelector(".cb-carousel-track");
  const dots = carousel.querySelectorAll(".cb-carousel-dot");
  const index = carouselState[carouselId] || 0;
  track.style.transform = `translateX(-${index * 100}%)`;
  dots.forEach((dot, i) => dot.classList.toggle("active", i === index));
}

// ── Lightbox ──
const overlay = document.createElement("div");
overlay.className = "cb-lightbox-overlay";
overlay.innerHTML = `
    <button class="cb-lightbox-close" aria-label="Close">&times;</button>
    <button class="cb-lightbox-nav prev" aria-label="Previous">&#8592;</button>
    <img class="cb-lightbox-img" src="" alt="" />
    <button class="cb-lightbox-nav next" aria-label="Next">&#8594;</button>
    <div class="cb-lightbox-counter"></div>
  `;
document.body.appendChild(overlay);

const lbImg = overlay.querySelector(".cb-lightbox-img");
const lbCounter = overlay.querySelector(".cb-lightbox-counter");
let lbImages = [],
  lbCurrent = 0;

function cbOpenLightbox(carouselId) {
  const track = document.querySelector(`#${carouselId} .cb-carousel-track`);
  lbImages = Array.from(track.querySelectorAll("img"));
  lbCurrent = carouselState[carouselId] || 0;
  lbShow();
  overlay.classList.add("active");
  document.body.style.overflow = "hidden";
}

function lbClose() {
  overlay.classList.remove("active");
  document.body.style.overflow = "";
}

function lbShow() {
  lbImg.src = lbImages[lbCurrent].src;
  lbImg.alt = lbImages[lbCurrent].alt;
  lbCounter.textContent = `${lbCurrent + 1} / ${lbImages.length}`;
}

function lbMove(dir) {
  lbCurrent = (lbCurrent + dir + lbImages.length) % lbImages.length;
  lbShow();
}

overlay.querySelector(".cb-lightbox-close").addEventListener("click", lbClose);
overlay.querySelector(".cb-lightbox-nav.prev").addEventListener("click", () => lbMove(-1));
overlay.querySelector(".cb-lightbox-nav.next").addEventListener("click", () => lbMove(1));
overlay.addEventListener("click", (e) => {
  if (e.target === overlay) lbClose();
});

// Wire carousel images to lightbox
document.querySelectorAll(".cb-carousel").forEach(function (carousel) {
  const id = carousel.id;
  carousel.querySelectorAll(".cb-carousel-track img").forEach(function (img, idx) {
    img.style.cursor = "pointer";
    img.addEventListener("click", function () {
      carouselState[id] = idx;
      cbOpenLightbox(id);
    });
  });
});

document.addEventListener("keydown", (e) => {
  if (!overlay.classList.contains("active")) return;
  if (e.key === "Escape") lbClose();
  if (e.key === "ArrowLeft") lbMove(-1);
  if (e.key === "ArrowRight") lbMove(1);
});
