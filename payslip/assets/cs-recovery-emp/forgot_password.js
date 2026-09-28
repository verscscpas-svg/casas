/* ===================================================================
   forgot_password.js
   Place in:  assets/forindex/forgot_password.js
   Add before </body> in index.php  (after jquery.js):
   <script src="assets/forindex/forgot_password.js"></script>
   =================================================================== */

$(function () {
  /* ── Open modal when "Forgot password?" is clicked ── */
  $(document).on("click", "#forgotPasswordLink", function (e) {
    e.preventDefault();
    openModal();
  });

  function openModal() {
    resetModal();
    $("#fpOverlay").addClass("fp-visible");
    setTimeout(function () {
      $("#fpIdentifier").focus();
    }, 360);
  }

  function closeModal() {
    $("#fpOverlay").removeClass("fp-visible");
  }

  /* ── Close triggers ── */
  $("#fpClose").on("click", closeModal);
  $("#fpOverlay").on("click", function (e) {
    if (e.target === this) closeModal();
  });
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") closeModal();
  });
  $("#fpDoneBtn").on("click", closeModal);

  /* ── Debounce helper ── */
  var debounceTimer;
  function debounce(fn, ms) {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fn, ms);
  }

  /* ── Live check while typing ── */
  $("#fpIdentifier").on("input", function () {
    var val = $(this).val().trim();

    clearFeedback();

    if (!val) return;

    // Show spinner while waiting
    setFeedback("info", '<span class="fp-spin"></span> Checking...');

    debounce(function () {
      $.ajax({
        url: "../cs-recovery-emp/check_employee.php",
        method: "POST",
        data: { identifier: val },
        dataType: "json",
        success: function (res) {
          if (res.found) {
            setFeedback("success", "✓ Account found — " + res.email_hint);
            $("#fpIdentifier")
              .removeClass("fp-input-error")
              .addClass("fp-input-success");
          } else {
            setFeedback("error", "✗ No record found in the system.");
            $("#fpIdentifier")
              .removeClass("fp-input-success")
              .addClass("fp-input-error");
          }
        },
        error: function () {
          setFeedback("info", "Could not verify — please try again.");
          $("#fpIdentifier").removeClass("fp-input-error fp-input-success");
        },
      });
    }, 600);
  });

  /* ── Send button ── */
  $("#fpSendBtn").on("click", function () {
    var val = $("#fpIdentifier").val().trim();

    if (!val) {
      setFeedback("error", "Please enter your Employee ID or email.");
      $("#fpIdentifier").addClass("fp-input-error").focus();
      return;
    }

    // Block if input is still in error state
    if ($("#fpIdentifier").hasClass("fp-input-error")) {
      setFeedback("error", "No account found. Please check your input.");
      return;
    }

    var $btn = $(this);
    $btn.addClass("fp-loading").prop("disabled", true);

    $.ajax({
      url: "../cs-recovery-emp/forgot_password.php",
      method: "POST",
      data: { identifier: val },
      dataType: "json",
      success: function (res) {
        $btn.removeClass("fp-loading").prop("disabled", false);
        if (res.success) {
          showStep2();
        } else {
          setFeedback(
            "error",
            res.message || "Something went wrong. Please try again.",
          );
          $("#fpIdentifier").addClass("fp-input-error");
        }
      },
      error: function () {
        $btn.removeClass("fp-loading").prop("disabled", false);
        setFeedback("error", "Server error. Please try again later.");
      },
    });
  });

  /* ── Allow Enter key on input ── */
  $("#fpIdentifier").on("keydown", function (e) {
    if (e.key === "Enter") $("#fpSendBtn").trigger("click");
  });

  /* ── Helpers ── */
  function setFeedback(type, html) {
    $("#fpFeedback")
      .removeClass("fp-error fp-success fp-info")
      .addClass("fp-" + type)
      .html(html);
  }

  function clearFeedback() {
    $("#fpFeedback").removeClass("fp-error fp-success fp-info").html("");
    $("#fpIdentifier").removeClass("fp-input-error fp-input-success");
  }

  function showStep2() {
    $("#fpStep1").addClass("fp-step-hidden");
    $("#fpStep2").removeClass("fp-step-hidden");
  }

  function resetModal() {
    $("#fpIdentifier").val("").removeClass("fp-input-error fp-input-success");
    clearFeedback();
    $("#fpStep1").removeClass("fp-step-hidden");
    $("#fpStep2").addClass("fp-step-hidden");
  }
});
