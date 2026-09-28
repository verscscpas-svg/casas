document.getElementById("yr").textContent = new Date().getFullYear();

function toggleAccordion(id) {
  var el = document.getElementById(id);
  var isOpen = el.classList.contains("open");
  // Close all
  document.querySelectorAll(".wrapper.open").forEach(function (w) {
    w.classList.remove("open");
  });
  // Open clicked if it was closed
  if (!isOpen) el.classList.add("open");
}
