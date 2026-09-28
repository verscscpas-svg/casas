document.getElementById("print").addEventListener("click", function () {
  const payslip = document.querySelector(".payslip-container");

  // PRINT ONLY CSS
  const printCSS = `
   body {
        font-family: "Courier New", Courier, monospace;
        font-size: 13px;
        background: #ffffff;
        margin: 0;
        padding: 20px;
        text-transform: uppercase;
      }

      .payslip-container {
        width: 850px;
        margin: auto;
        padding: 30px;

        background: #fff;
        box-sizing: border-box;
      }

      /* HEADER */
      .COMPANY {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
      }

      .IMGLOGO img {
        width: 90px;
        height: auto;
      }

      .COMP {
        font-size: 22px;
        font-weight: bold;
        margin: 0;
        color: #000;
      }

      /* INFO GRID */
      .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 15px;
      }

      .info-group {
        display: grid;
        grid-template-columns: 150px 1fr;
        margin-bottom: 5px;
      }

      .label {
        font-weight: bold;
      }

      /* CUSTOM INFO */
      .custom-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        padding: 15px 0;
        border-top: 1px dashed #999;
        border-bottom: 1px dashed #999;
        margin-bottom: 20px;
      }

      /* TABLE SECTION */
      .calculation-section {
        display: flex;
        justify-content: space-between;

        margin-top: 20px;
      }

      .column-table {
        width: 50%;
        border-collapse: collapse;
        table-layout: fixed;
      }

      .column-table th {
        border-bottom: 1px solid black;
        padding-bottom: 5px;
        text-align: left;
      }

      .column-table td {
        word-break: break-word;
      }

      .text-right {
        text-align: right;
      }

      /* TOTALS */
      .totals-section {
        margin-top: 40px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
      }

      .total-row {
        width: 250px;
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
      }

      .under {
        border-bottom: 2px solid black;
      }

      /* FOOTER */
      .print-footer {
        margin-top: 30px;
        text-align: center;
      }

      .info {
        font-size: 10px;
        font-style: italic;
        color: #555;
      }
          td {
        font-size: 12px;
      }
  `;

  // OPEN CLEAN PRINT WINDOW
  const printWindow = window.open("", "", "width=1200,height=900");

  printWindow.document.write(`
    <html>
      <head>
        <title>Payslip Print</title>

        <style>
          ${printCSS}
        </style>
      </head>

      <body>

        ${payslip.outerHTML}

        <div class="print-footer">
          <p class="info">
            *This payslip is system generated. No signature required.*
          </p>
        </div>

      </body>
    </html>
  `);

  printWindow.document.close();

  printWindow.onload = function () {
    printWindow.focus();
    printWindow.print();
    printWindow.close();
  };
});
