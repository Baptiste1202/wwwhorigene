document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("downloadModal");
  const btnClose = document.getElementById("modal-close");
  const btnCancel = document.getElementById("modal-cancel");

  function isModalOpen() {
    return modal.classList.contains("open") || getComputedStyle(modal).display !== "none";
  }

  function setTableZFix(apply) {
    const targets = document.querySelectorAll("thead, thead th, tbody td");
    targets.forEach(el => {
      el.style.zIndex = apply ? "0" : "";
      el.style.position = apply ? "static" : "";
    });
  }

  function applyStateFromModal() {
    setTableZFix(isModalOpen());
  }

  applyStateFromModal();

  const observer = new MutationObserver(applyStateFromModal);
  observer.observe(modal, { attributes: true, attributeFilter: ["class", "style"] });

  [btnClose, btnCancel].forEach(btn => {
    if (btn) {
      btn.addEventListener("click", () => {
        modal.classList.remove("open");
        modal.style.display = "none";
        applyStateFromModal();
      });
    }
  });

  window.openDownloadModal = () => {
    modal.classList.add("open");
    modal.style.display = "flex";
    applyStateFromModal();
  };
});