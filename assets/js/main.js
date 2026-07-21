/* ==========================================================================
   Nordö Förvaltning - main.js
   Navigering, scroll-effekter, reveal-animationer. Vanilla JS, inga beroenden.
   ========================================================================== */
(function () {
  "use strict";

  var body = document.body;
  var navbar = document.querySelector(".navbar");

  /* ----- Navbar: solid bakgrund vid scroll ----- */
  function onScroll() {
    if (!navbar) return;
    navbar.classList.toggle("scrolled", window.scrollY > 40);
  }
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  /* ----- Guld scroll-progress-linje ----- */
  var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  if (!reduceMotion) {
    var progress = document.createElement("div");
    progress.className = "scroll-progress";
    progress.setAttribute("aria-hidden", "true");
    document.body.appendChild(progress);
    var updateProgress = function () {
      var doc = document.documentElement;
      var max = doc.scrollHeight - doc.clientHeight;
      var ratio = max > 0 ? Math.min(1, Math.max(0, window.scrollY / max)) : 0;
      progress.style.transform = "scaleX(" + ratio + ")";
    };
    window.addEventListener("scroll", updateProgress, { passive: true });
    window.addEventListener("resize", updateProgress, { passive: true });
    updateProgress();
  }

  /* ----- Mobilmeny ----- */
  var toggle = document.querySelector(".nav-toggle");
  var backdrop = document.querySelector(".nav-backdrop");

  function closeMenu() {
    body.classList.remove("nav-open");
    if (toggle) toggle.setAttribute("aria-expanded", "false");
  }
  function toggleMenu() {
    var open = body.classList.toggle("nav-open");
    if (toggle) toggle.setAttribute("aria-expanded", open ? "true" : "false");
  }
  if (toggle) toggle.addEventListener("click", toggleMenu);
  if (backdrop) backdrop.addEventListener("click", closeMenu);
  document.querySelectorAll(".nav-links a").forEach(function (link) {
    link.addEventListener("click", closeMenu);
  });
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeMenu();
  });

  /* ----- Reveal vid scroll ----- */
  var revealEls = document.querySelectorAll(".reveal");
  if ("IntersectionObserver" in window && revealEls.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("in");
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -40px 0px" });
    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add("in"); });
  }

  /* ----- "Till toppen"-knapp ----- */
  var toTop = document.createElement("button");
  toTop.className = "to-top";
  toTop.type = "button";
  toTop.setAttribute("aria-label", "Till toppen");
  toTop.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>';
  document.body.appendChild(toTop);
  toTop.addEventListener("click", function () {
    window.scrollTo({ top: 0, behavior: reduceMotion ? "auto" : "smooth" });
  });
  var toggleToTop = function () { toTop.classList.toggle("show", window.scrollY > 600); };
  window.addEventListener("scroll", toggleToTop, { passive: true });
  toggleToTop();

  /* ----- Subtil hero-parallax ----- */
  var heroContent = document.querySelector(".hero-content");
  if (heroContent && !reduceMotion) {
    window.addEventListener("scroll", function () {
      var y = window.scrollY;
      if (y <= window.innerHeight) {
        heroContent.style.transform = "translateY(" + (y * 0.18).toFixed(1) + "px)";
        heroContent.style.opacity = String(Math.max(0, 1 - y / (window.innerHeight * 0.75)));
      }
    }, { passive: true });
  }

  /* ----- Aktuellt år i footer ----- */
  var yearEl = document.getElementById("year");
  if (yearEl) yearEl.textContent = new Date().getFullYear();
})();
