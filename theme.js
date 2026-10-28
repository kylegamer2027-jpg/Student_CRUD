function applyTheme(theme) {
  document.documentElement.dataset.theme = theme;

  document.querySelectorAll(".theme-icon").forEach((icon) => {
    icon.textContent = theme === "dark" ? "☀️" : "🌙";
  });
}

function toggleTheme() {
  const current = document.documentElement.dataset.theme || "light";

  const next = current === "dark" ? "light" : "dark";

  localStorage.setItem("studentCrudTheme", next);
  applyTheme(next);
}

(function () {
  const saved = localStorage.getItem("studentCrudTheme") || "light";
  applyTheme(saved);
})();
