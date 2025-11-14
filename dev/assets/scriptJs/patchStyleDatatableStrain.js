// patchdatatblestarin.js
// Applique des rowspans sur des colonnes données pour des blocs de lignes
// identifiés par la classe .list-strain (première ligne de chaque bloc).
// Gère DataTables + RowGroup (enlève 1 au span visible).

(function ($) {
  'use strict';

  let _styleInjected = false;

  function injectStyleOnce() {
    if (_styleInjected) return;
    const css = `
      /* Centrage vertical + horizontal pour les cellules fusionnées */
      td.spanned-cell {
        vertical-align: middle;
        display: flex !important;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        gap: 6px;
        height: 100%;
      }
    `;
    const s = document.createElement('style');
    s.type = 'text/css';
    s.appendChild(document.createTextNode(css));
    document.head.appendChild(s);
    _styleInjected = true;
  }

  /**
   * Fusionne (rowspan) des colonnes par index 0-based sur chaque bloc .list-strain
   * et retire 1 au span visible (cas DataTables RowGroup : une ligne "groupe" à ignorer).
   * @param {string} tableSelector - sélecteur du tableau (ex: '#data-table')
   * @param {number[]} colIdxs - indices 0-based des colonnes à fusionner
   */
  function applyRowspanOnColumns(tableSelector, colIdxs) {
    const $tbody = $(tableSelector).find('tbody');
    if ($tbody.length === 0) return;

    const rows = $tbody.children('tr');

    for (let i = 0; i < rows.length; i++) {
      const $first = $(rows[i]);
      if (!$first.hasClass('list-strain')) continue;

      // Déjà traité ? (évite double-application si redraw)
      if ($first.data('rowspanApplied')) {
        // Sauter proprement toutes les lignes du bloc courant
        let jj = i + 1;
        while (jj < rows.length && !$(rows[jj]).hasClass('list-strain')) jj++;
        i = jj - 1;
        continue;
      }

      // Collecte du bloc jusqu'à la prochaine .list-strain
      const block = [$first];
      let j = i + 1;
      while (j < rows.length && !$(rows[j]).hasClass('list-strain')) {
        block.push($(rows[j]));
        j++;
      }

      // Longueur "visible" : on enlève 1 pour ignorer la ligne RowGroup
      const visibleSpan = Math.max(1, block.length - 1);

      // 1) Mettre rowspan sur les colonnes cibles de la 1ère ligne
      const $cells = $first.children('td');
      colIdxs.forEach((idx) => {
        const $c = $cells.eq(idx);
        if ($c.length) $c.attr('rowspan', visibleSpan).addClass('spanned-cell');
      });

      // 2) Supprimer les mêmes colonnes dans les lignes suivantes du bloc
      //    On parcourt uniquement 'visibleSpan' lignes (skip la ligne "groupe")
      for (let k = 1; k < 1 + visibleSpan; k++) {
        const $r = block[k];
        if (!$r || !$r.length) break;

        // IMPORTANT : supprimer en ordre décroissant pour éviter le décalage d'index
        [...colIdxs].sort((a, b) => b - a).forEach((idx) => {
          const $tds = $r.children('td');
          if (idx < $tds.length) $tds.eq(idx).remove();
        });
      }

      // Flag pour ne pas retraiter ce bloc au prochain passage
      $first.data('rowspanApplied', true);

      // Sauter au prochain bloc
      i = j - 1;
    }
  }

  function initPatch(options) {
    injectStyleOnce();

    const tableSel = options?.tableSelector || '#data-table';
    const colIdxs = options?.colIdxs || [3, 4, 5, 6, 7, 18, 19]; // par défaut : 3..7 + 18,19

    if (!$(tableSel).length) return;

    // Application initiale (après le 1er rendu)
    applyRowspanOnColumns(tableSel, colIdxs);

    // Ré-application à chaque redraw DataTables
    const dt = $(tableSel).data('DataTable') || ($(tableSel).DataTable ? $(tableSel).DataTable() : null);
    if (dt && dt.on) {
      dt.on('draw', function () {
        applyRowspanOnColumns(tableSel, colIdxs);
      });
    }
  }

  // Auto-init sur DOM ready avec les valeurs par défaut
  $(document).ready(function () {
    initPatch();
  });

  // Expose quelques helpers si besoin
  window.patchDataTableStrain = {
    applyRowspanOnColumns,
    init: initPatch
  };

})(jQuery);
