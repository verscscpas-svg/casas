document.getElementById("print").addEventListener("click", function () {
  const payslip = document.querySelector(".payslip-container");

  // Get all existing CSS from the page
  const styles = Array.from(document.styleSheets)
    .map((sheet) => {
      try {
        return Array.from(sheet.cssRules || [])
          .map((rule) => rule.cssText)
          .join("\n");
      } catch (e) {
        return ""; // ignore cross-origin stylesheets
      }
    })
    .join("\n");

  // Extra print-specific CSS
  const printCSS = `
        @media print {
            @page {
                margin: 0; /* remove default headers/footers */
            }
            body {
                margin: 0; 
                padding: 0;
      font-family: 'Courier New', Courier, monospace;
            }
            .payslip-container {
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
                transform: scale(0.95);
                transform-origin: top left;
                border: none !important;
                outline : none !important;
            }
            table.column-table {
                width: 100% !important;
                table-layout: fixed;
                word-break: break-word;
            }
            .info-grid, .custom-info {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
            .liitan, .reghol {
                font-size: smaller;
                margin-top: 190px;
            }
            /* Hide elements you don't want to appear in print */
            .date, .about-footer {
                display: none !important;
            }
                .print-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.print-footer {
    margin-top: auto;
    text-align: center;
    font-size: 12px;
}
        }
    `;

  // Open new print window
  const printWindow = window.open("", "", "width=1300,height=1000");
  printWindow.document.write(`
        <html>
        <head>
            <title>Payslip</title>
            <style>
                ${styles}
                ${printCSS}
            </style>
        </head>
       <body>
    <div class="print-wrapper">
        ${payslip.outerHTML}

        <div class="print-footer">
            <p class="info">*This payslip is system generated. No signature required.*</p>
        </div>
    </div>
</body>
        </html>
    `);
  printWindow.document.close();
  printWindow.focus();
  printWindow.print();
  printWindow.close();
});
