// scriptJs/confirmMultiDelete.js
// Confirme les suppressions multiples en listant: ID — libellé.
// Heuristique du libellé : name → title → (type + country) → type → 3e colonne.

document.addEventListener('DOMContentLoaded', function () {
  const forms = document.querySelectorAll('form[id^="multi-action-form"]');
  if (!forms.length) return;

  const colCache = new WeakMap();

  function findColumns(table) {
    let cfg = colCache.get(table);
    if (cfg) return cfg;

    const ths = table && table.tHead ? Array.from(table.tHead.rows[0].cells) : [];

    const findIdx = (selectors, texts = []) => {
      // par data-column / class
      for (const sel of selectors) {
        const i = ths.findIndex(th => th.matches(sel));
        if (i !== -1) return i;
      }
      // par libellé exact (insensible à la casse)
      const lower = t => t.textContent.trim().toLowerCase();
      for (const t of texts) {
        const i = ths.findIndex(th => lower(th) === t);
        if (i !== -1) return i;
      }
      return -1;
    };

    cfg = {
      // ID utile si jamais tu veux le récupérer ailleurs (pas nécessaire ici)
      id:      findIdx(['[data-column="id"]', '.id'], ['id']),
      name:    findIdx(['[data-column="name"]', '.name'], ['name']),
      title:   findIdx(['[data-column="title"]', '.title'], ['title']),
      type:    findIdx(['[data-column="type"]', '.type'], ['type']),
      country: findIdx(['[data-column="country"]', '.country'], ['country']),
      // fallback: 3e colonne (index 2)
      fallback: 2
    };

    // Cas connus où les th n'ont pas data-column mais un texte :
    if (cfg.type === -1)    cfg.type    = findIdx([], ['type']);
    if (cfg.country === -1) cfg.country = findIdx([], ['country']);

    colCache.set(table, cfg);
    return cfg;
  }

  function cellText(cells, idx) {
    return (cells && cells[idx] && cells[idx].textContent.trim()) || '';
  }

  function getDisplayText(tr, table) {
    if (!tr || !table) return '--';
    const cols = findColumns(table);
    const cells = tr.cells;

    // Priorité : name → title → (type + country) → type → fallback → query CSS
    if (cols.name !== -1)  return cellText(cells, cols.name);
    if (cols.title !== -1) return cellText(cells, cols.title);

    if (cols.type !== -1 && cols.country !== -1) {
      const t = cellText(cells, cols.type);
      const c = cellText(cells, cols.country);
      if (t && c) return `${t} — ${c}`;
      if (t) return t;
      if (c) return c;
    }
    if (cols.type !== -1) return cellText(cells, cols.type);

    // fallback (3e colonne), sinon 1er td.name/title/type/country trouvé
    return (
      cellText(cells, cols.fallback) ||
      (tr.querySelector('td.name, td.title, td.type, td.country')?.textContent.trim()) ||
      '--'
    );
  }

  function guessEntityName(form) {
    if (form.dataset.entity) return form.dataset.entity; // ex: data-entity="samples"

    const tableId = form.querySelector('table')?.id || '';
    const id = tableId.toLowerCase();
    if (id.includes('strain'))         return 'strains';
    if (id.includes('project'))        return 'projects';
    if (id.includes('plasmyd'))        return 'plasmids';
    if (id.includes('collect'))        return 'collections';
    if (id.includes('drug'))           return 'drugs';
    if (id.includes('publication'))    return 'publications';
    if (id.includes('phenotype'))      return 'phenotype types';
    if (id.includes('sample'))         return 'samples';
    return 'items';
  }

  forms.forEach(form => {
    form.addEventListener('submit', function (e) {
      const checked = Array.from(form.querySelectorAll('.select-checkbox:checked'));
      if (!checked.length) {
        e.preventDefault();
        alert('No item selected.');
        return;
      }

      // Déduplique par ID (si plusieurs lignes pour le même item)
      const selected = new Map();
      for (const cb of checked) {
        const id = cb.value;
        if (selected.has(id)) continue;

        const tr = cb.closest('tr');
        const table = cb.closest('table');
        const label = getDisplayText(tr, table);
        selected.set(id, { id, label });
      }

      const entity = guessEntityName(form);
      const lines = Array.from(selected.values()).map(x => `- ${x.id} — ${x.label}`);
      const message = `Are you sure you want to delete the following ${entity}?\n\n${lines.join('\n')}`;

      if (!confirm(message)) e.preventDefault();
    });
  });
});
