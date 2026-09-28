let currentSort = "empno";

function fmt(n) {
  return parseFloat(n).toLocaleString("en-PH", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

function getVisibleRows() {
  return Array.from(
    document.querySelectorAll("#tableBody tr[data-empno]"),
  ).filter((r) => r.style.display !== "none");
}

function renderTable() {
  const q = document.getElementById("searchInput").value.toLowerCase().trim();
  const tbody = document.getElementById("tableBody");
  const rows = Array.from(tbody.querySelectorAll("tr[data-empno]"));

  rows.sort((a, b) => {
    if (currentSort === "name")
      return a.dataset.name.localeCompare(b.dataset.name);
    if (currentSort === "cutoff")
      return a.dataset.cutstart.localeCompare(b.dataset.cutstart);
    if (currentSort === "cutoffend")
      return a.dataset.cutend.localeCompare(b.dataset.cutend);
    return String(a.dataset.empno).localeCompare(
      String(b.dataset.empno),
      undefined,
      {
        numeric: true,
      },
    );
  });

  let vis = 0,
    totG = 0,
    totD = 0,
    totN = 0;
  rows.forEach((r) => {
    const nameMatch = r.dataset.name.includes(q);
    const empnoMatch = String(r.dataset.empno).includes(q);
    const cutStartMatch = r.dataset.cutstart.includes(q);
    const cutEndMatch = r.dataset.cutend.includes(q);
    const fmtDate = (d) =>
      new Date(d)
        .toLocaleDateString("en-US", {
          month: "short",
          day: "2-digit",
          year: "numeric",
        })
        .toLowerCase();
    const cutStartFmt = fmtDate(r.dataset.cutstart);
    const cutEndFmt = fmtDate(r.dataset.cutend);

    const match =
      !q ||
      nameMatch ||
      empnoMatch ||
      cutStartMatch ||
      cutEndMatch ||
      cutStartFmt.includes(q) ||
      cutEndFmt.includes(q);

    r.style.display = match ? "" : "none";
    if (match) {
      vis++;
      totG += parseFloat(r.dataset.gross);
      totD += parseFloat(r.dataset.deduct);
      totN += parseFloat(r.dataset.net);
    }
    tbody.appendChild(r);
  });

  document.getElementById("countBadge").textContent =
    vis + " record" + (vis !== 1 ? "s" : "");
  document.getElementById("totGross").textContent = fmt(totG);
  document.getElementById("totDeduct").textContent = fmt(totD);
  document.getElementById("totNet").textContent = fmt(totN);
}

function toggleDropdown(e) {
  e.stopPropagation();
  document.getElementById("sortDropdown").classList.toggle("open");
  document.getElementById("sortTrigger").classList.toggle("active");
}

function setSort(el) {
  currentSort = el.dataset.sort;
  document.querySelectorAll(".dropdown-item").forEach((i) => {
    i.classList.remove("selected");
    i.querySelector(".check").style.opacity = "0";
  });
  el.classList.add("selected");
  el.querySelector(".check").style.opacity = "1";
  document.getElementById("sortDropdown").classList.remove("open");
  document.getElementById("sortTrigger").classList.remove("active");
  renderTable();
}

document.addEventListener("click", () => {
  document.getElementById("sortDropdown").classList.remove("open");
  document.getElementById("sortTrigger").classList.remove("active");
});

function exportCSV() {
  const visible = getVisibleRows();
  const rows = visible.map((r) => JSON.parse(r.dataset.export));

  const headers = [
    "empNo",
    "cut_off_start",
    "cut_off_end",
    "empName",
    "Monthly Salary",
    "basic_pay (half)",
    "Taxable",
    "Non-Taxable (Half)",
    "Non-Taxable (Others)",
    "gross_pay",
    "tax",
    "philhealth",
    "sss",
    "pagibig",
    "total_deductions",
    "net_pay",
  ];

  const totG = rows.reduce((s, r) => s + parseFloat(r.gross_pay || 0), 0);
  const totD = rows.reduce(
    (s, r) => s + parseFloat(r.total_deductions || 0),
    0,
  );
  const totN = rows.reduce((s, r) => s + parseFloat(r.net_pay || 0), 0);

  const escape = (v) => '"' + String(v).replace(/"/g, '""') + '"';

  const dataRows = rows.map((d) =>
    [
      escape(d.empNo),
      escape(d.cut_off_start),
      escape(d.cut_off_end),
      escape(d.empName),
      parseFloat(d.monthly),
      parseFloat(d.basic_pay),
      parseFloat(d.taxable),
      parseFloat(d.non_taxable_half),
      parseFloat(d.non_taxable_other),
      parseFloat(d.gross_pay),
      parseFloat(d.tax),
      parseFloat(d.philhealth),
      parseFloat(d.sss),
      parseFloat(d.pagibig),
      parseFloat(d.total_deductions),
      parseFloat(d.net_pay),
    ].join(","),
  );

  const totalRow = [
    escape(""),
    escape(""),
    escape(""),
    escape("TOTAL"),
    "",
    "",
    "",
    "",
    "",
    totG.toFixed(2),
    "0.00",
    "—",
    "—",
    totD.toFixed(2),
    "",
    totN.toFixed(2),
  ].join(",");

  // UTF-8 BOM (\uFEFF) — ito ang nagsasabi sa Excel na UTF-8 ang file
  const csv =
    "\uFEFF" + [headers.join(","), ...dataRows, totalRow].join("\r\n");

  const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download =
    "payroll_summary_" + new Date().toISOString().slice(0, 10) + ".csv";
  a.click();
  URL.revokeObjectURL(url);
}

renderTable();

// ── Sidebar toggle ────────────────────────────────────────────────────────────
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("collapsed");
}

// ── Logout confirmation ───────────────────────────────────────────────────────
function confirmLogout() {
  const overlay = document.getElementById("logoutOverlay");
  overlay.style.display = "flex"; // ✅ directly set display
}

function cancelLogout() {
  const overlay = document.getElementById("logoutOverlay");
  overlay.style.display = "none"; // ✅ hide it back
}

function doLogout() {
  window.location.href = "process/logout.php"; // adjust path if needed
}

// Close overlay on backdrop click
document.addEventListener("DOMContentLoaded", () => {
  const overlay = document.getElementById("logoutOverlay");
  if (overlay) {
    overlay.addEventListener("click", (e) => {
      if (e.target === overlay) cancelLogout();
    });
  }
});
