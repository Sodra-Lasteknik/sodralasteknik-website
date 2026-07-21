/* ==========================================================================
   Södra Låsteknik - contact.js
   Klientvalidering + inskick av offertformulär till PHP-endpoint.
   ========================================================================== */
(function () {
  "use strict";

  var form = document.getElementById("contact-form");
  if (!form) return;

  var status = document.getElementById("form-status");
  var submitBtn = form.querySelector('button[type="submit"]');
  var ENDPOINT = form.getAttribute("action") || "offert.php";

  function setError(field, on) {
    var wrap = field.closest(".field");
    if (wrap) wrap.classList.toggle("invalid", on);
  }

  // Filbilagor (offertförfrågan). Måste matcha gränserna i offert.php.
  var MAX_FILE = 5 * 1024 * 1024;
  var MAX_TOTAL = 15 * 1024 * 1024;

  function validateFiles() {
    var input = form.querySelector('input[type="file"]');
    if (!input || !input.files || !input.files.length) return true;

    var total = 0;
    for (var i = 0; i < input.files.length; i++) {
      total += input.files[i].size;
      if (input.files[i].size > MAX_FILE) {
        setError(input, true);
        showStatus("err", "“" + input.files[i].name + "” är större än 5 MB. Välj en mindre fil.");
        return false;
      }
    }
    if (total > MAX_TOTAL) {
      setError(input, true);
      showStatus("err", "Bilagorna är tillsammans större än 15 MB. Ta bort någon fil och försök igen.");
      return false;
    }
    setError(input, false);
    return true;
  }

  function validate() {
    var ok = true;
    form.querySelectorAll("[required]").forEach(function (field) {
      var empty = !field.value.trim();
      var badEmail = field.type === "email" && field.value &&
        !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value);
      var bad = empty || badEmail;
      setError(field, bad);
      if (bad && ok) field.focus();
      if (bad) ok = false;
    });
    return ok;
  }

  // Rensa fel medan användaren skriver
  form.querySelectorAll("[required]").forEach(function (field) {
    field.addEventListener("input", function () { setError(field, false); });
  });

  function showStatus(type, msg) {
    if (!status) return;
    status.className = "form-status show " + type;
    status.textContent = msg;
  }

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    if (status) status.className = "form-status";
    if (!validate()) return;
    if (!validateFiles()) return;

    var data = new FormData(form);
    if (submitBtn) { submitBtn.disabled = true; submitBtn.dataset.label = submitBtn.textContent; submitBtn.textContent = "Skickar..."; }

    fetch(ENDPOINT, { method: "POST", body: data, headers: { "Accept": "application/json" } })
      .then(function (res) {
        return res.json().catch(function () { return { ok: res.ok }; });
      })
      .then(function (json) {
        // Web3Forms svarar med { success: true }, PHP-varianten med { ok: true }.
        if (json && (json.success || json.ok)) {
          form.reset();
          showStatus("ok", "Tack! Din offertförfrågan är mottagen. Vi återkommer så snart vi kan.");
        } else {
          showStatus("err", (json && (json.message || json.error)) || "Något gick fel. Försök igen eller mejla oss direkt på info@sodralasteknik.se.");
        }
      })
      .catch(function () {
        showStatus("err", "Kunde inte skicka just nu. Mejla oss gärna direkt på info@sodralasteknik.se.");
      })
      .finally(function () {
        if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.label || "Skicka offertförfrågan"; }
      });
  });
})();
