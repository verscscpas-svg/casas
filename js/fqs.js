// ============ Accordion Toggle ============
function toggleAccordion(wrapperId) {
  const wrapper = document.getElementById(wrapperId);
  wrapper.classList.toggle("active");
}

// ============ Scroll-triggered Animation ============
function isInViewport(element) {
  const rect = element.getBoundingClientRect();
  return rect.top <= (window.innerHeight || document.documentElement.clientHeight) - 100;
}

function animateOnScroll() {
  const wrappers = document.querySelectorAll(".wrapper");
  wrappers.forEach((wrapper) => {
    if (isInViewport(wrapper)) {
      wrapper.classList.add("scroll-in");
    } else {
      wrapper.classList.remove("scroll-in");
    }
  });
}

window.addEventListener("scroll", animateOnScroll);
window.addEventListener("load", animateOnScroll);

// ============ Custom Alert (loading / success / error) ============
const caOverlay = document.getElementById("custom-alert-overlay");
const caBox = document.getElementById("custom-alert-box");
const caLabel = document.getElementById("ca-label");
const caTitle = document.getElementById("custom-alert-title");
const caText = document.getElementById("custom-alert-text");
const caIconRing = document.getElementById("ca-ring");
const caIcon = document.getElementById("ca-icon");
const caFill = document.getElementById("custom-alert-timer-fill");
const caBtn = document.getElementById("custom-alert-btn");

function showLoadingAlert(text = "Please wait while we send your message...") {
  caBox.classList.remove("ca-success", "ca-error");
  caBox.classList.add("ca-loading");

  caLabel.textContent = "Processing";
  caTitle.textContent = "Sending Message...";
  caText.textContent = text;

  // Swap icon area into a spinner
  caIconRing.innerHTML = '<span class="ca-spinner"></span>';

  // Hide timer bar and OK button while loading (walang exit habang loading)
  caFill.parentElement.style.visibility = "hidden";
  caBtn.style.display = "none";

  caOverlay.classList.add("active");
  clearTimeout(window.__caTimeout);
}

function showResultAlert(success, text) {
  caBox.classList.remove("ca-loading", "ca-success", "ca-error");
  caBox.classList.add(success ? "ca-success" : "ca-error");

  caLabel.textContent = success ? "Message Sent" : "Something Went Wrong";
  caTitle.textContent = success ? "Thank you!" : "Oops!";
  caText.textContent = text;

  // Restore icon ring back to check/x svg
  caIconRing.innerHTML = `
      <svg id="ca-icon" width="28" height="28" viewBox="0 0 24 24" fill="none"
        stroke="${success ? "#294169" : "#b3261e"}" stroke-width="2.5"
        stroke-linecap="round" stroke-linejoin="round">
        ${success ? '<polyline points="20 6 9 17 4 12"></polyline>' : '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>'}
      </svg>`;

  // Show timer bar + OK button again
  caFill.parentElement.style.visibility = "visible";
  caBtn.style.display = "inline-block";

  caOverlay.classList.add("active");

  // Reset & animate timer bar
  caFill.style.transition = "none";
  caFill.style.width = "100%";
  requestAnimationFrame(() => {
    caFill.style.transition = "width 5s linear";
    caFill.style.width = "0%";
  });

  clearTimeout(window.__caTimeout);
  window.__caTimeout = setTimeout(() => {
    caOverlay.classList.remove("active");
    if (success) location.reload(); // reload after success auto-close
  }, 5000);
}

caBtn.addEventListener("click", () => {
  clearTimeout(window.__caTimeout);
  caOverlay.classList.remove("active");
  if (caBox.classList.contains("ca-success")) {
    location.reload(); // reload din kapag manual na sinara ang alert
  }
});

// ============ Contact Form Submit (AJAX) ============
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("contactForm");
  const submitBtn = form.querySelector('input[type="submit"]');

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    if (form.dataset.submitted === "true") return;
    form.dataset.submitted = "true";

    submitBtn.disabled = true;
    submitBtn.value = "Sending...";

    // Show loading state sa alert mismo (overlay = blocked na yung likod)
    showLoadingAlert();

    const formData = new FormData(form);

    fetch(form.action, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        submitBtn.disabled = false;
        submitBtn.value = "Send Message";
        form.dataset.submitted = "false";

        showResultAlert(data.success, data.message);

        if (data.success) {
          form.reset();
        }
      })
      .catch((err) => {
        submitBtn.disabled = false;
        submitBtn.value = "Send Message";
        form.dataset.submitted = "false";
        showResultAlert(false, "An error occurred. Please try again.");
        console.error(err);
      });
  });
});
