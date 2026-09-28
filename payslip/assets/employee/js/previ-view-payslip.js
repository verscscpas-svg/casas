function formatTIN(tin) {
  tin = tin.replace(/\D/g, "");

  // kunin lang first 12 digits
  tin = tin.substring(0, 12);

  return tin.match(/.{1,3}/g)?.join("-") || tin;
}

function formatSSS(sss) {
  return sss.replace(/^(\d{2})(\d{7})(\d{1})$/, "$1-$2-$3");
}

function formatPagIbig(pagibig) {
  return pagibig.replace(/^(\d{4})(\d{4})(\d{4})$/, "$1-$2-$3");
}

function formatPhilHealth(ph) {
  return ph.replace(/^(\d{2})(\d{9})(\d{1})$/, "$1-$2-$3");
}

// Apply formatting after page loads
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".tin").forEach((el) => {
    el.textContent = formatTIN(el.textContent.trim());
  });

  document.querySelectorAll(".sss").forEach((el) => {
    el.textContent = formatSSS(el.textContent.trim());
  });

  document.querySelectorAll(".pagibig").forEach((el) => {
    el.textContent = formatPagIbig(el.textContent.trim());
  });

  document.querySelectorAll(".philhealth").forEach((el) => {
    el.textContent = formatPhilHealth(el.textContent.trim());
  });
});
