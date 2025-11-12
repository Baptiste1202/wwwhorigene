// TITRE : Auto-fix overflow (Phenotype / Sequencing / Storage / Plasmid) — version robuste
// OBJET : Au premier rendu, certains blocs débordent tant que la liste (ul) est cachée (.hidden-tags).
//         On mime 1 clic sur “+” pour dévoiler et recalculer le layout, puis on supprime l’item ajouté
//         et on restaure l’index. Exécution unique + suppression fiable (ignore create-plasmid-li).

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // Empêche une exécution multiple
  if (window.__GRIDFIX_ONCE__) return;
  window.__GRIDFIX_ONCE__ = true;

  // Mapping bouton "add" → conteneur principal
  const MAP = {
    '#phenotype-add-btn':   '#phenotype-add',
    '#sequencing-add-btn':  '#sequencing-add',
    '#storage-add-btn':     '#storage-add',
    '#publication-add-btn': '#publication-add',
    '#project-add-btn':     '#project-add',
    '#plasmyd-add-btn':     '#plasmyd-add' // corrige si c'est "plasmid"
  };

  function removeLastRealItem(ul) {
    // Supprime le dernier <li> réel (hors .create-plasmid-li)
    const items = ul.querySelectorAll('li:not(.create-plasmid-li)');
    if (!items.length) return;
    items[items.length - 1].remove();
  }

  function mimicOne(btnWrapSel, blockSel) {
    const btn   = document.querySelector(`${btnWrapSel} .add_item_link`);
    const block = document.querySelector(blockSel);
    const ul    = block && block.querySelector('ul');
    if (!btn || !ul) return;

    // Exécuter seulement si la liste est cachée
    const hiddenWrapper = ul.closest('.hidden-tags');
    if (!hiddenWrapper) return;

    // Ne pas traiter deux fois le même bloc
    if (ul.dataset.gridfixApplied === '1') return;
    ul.dataset.gridfixApplied = '1';

    const beforeIndex = parseInt(ul.dataset.index || ul.children.length, 10) || 0;

    // Simule un clic sur "add" → recalcul du layout
    btn.click();

    // Supprime l'élément ajouté et restaure l’état initial
    setTimeout(() => {
      removeLastRealItem(ul);
      ul.dataset.index = String(beforeIndex);

      const prevDisplay = block.style.display;
      block.style.display = 'none';
      void block.offsetHeight; // reflow forcé
      block.style.display = prevDisplay || '';
    }, 0);
  }

  function runAllOnce() {
    Object.entries(MAP).forEach(([btnSel, blockSel]) => mimicOne(btnSel, blockSel));
  }

  runAllOnce();
});
