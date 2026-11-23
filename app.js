(function () {
  document.querySelectorAll("[data-count]").forEach((element) => {
    const target = Number(element.dataset.count) || 0;
    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (reducedMotion) {
      element.textContent = String(target);
      return;
    }

    const duration = 700;
    const start = performance.now();

    function update(now) {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      element.textContent = String(Math.round(target * eased));

      if (progress < 1) {
        requestAnimationFrame(update);
      }
    }

    requestAnimationFrame(update);
  });

  document.querySelectorAll("tbody tr").forEach((row, index) => {
    row.style.animationDelay = `${Math.min(index * 45, 450)}ms`;
  });

  const toast = document.querySelector(".toast");
  if (toast) {
    window.setTimeout(() => {
      toast.style.transition = "opacity 0.25s ease, transform 0.25s ease";
      toast.style.opacity = "0";
      toast.style.transform = "translateY(-8px)";
      window.setTimeout(() => toast.remove(), 250);
    }, 4000);
  }
})();
