document.addEventListener('DOMContentLoaded', function () {
    // DEV: sélection des éléments
    const table       = document.querySelector('#data-table');
    const btn         = document.getElementById('filterByUserButton');
    const searchInput = document.getElementById('customSearch');

    if (!btn || !table) {
        if (!btn)   console.warn("⚠️ Bouton #filterByUserButton introuvable !");
        if (!table) console.warn("⚠️ Table #data-table introuvable !");
        return;
    }

    // Récupère l’instance DataTables SI elle existe déjà (on n’en crée pas)
    let dt = null;
    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.dataTable && window.jQuery.fn.dataTable.isDataTable(table)) {
        dt = window.jQuery(table).DataTable();
    }

    // DEV: récupère prénom/nom
    const userFirstname = btn.dataset.firstname || '';
    const userLastname  = btn.dataset.lastname  || '';
    const swappedName   = `${userLastname} ${userFirstname}`.trim();

    // état des filtres
    let filterActive = false;
    let searchTerm   = '';

    // DEV: init bouton
    btn.dataset.active = "false";
    btn.textContent   = `🔍 Filter by: ${userFirstname} ${userLastname}`;
    btn.addEventListener('click', function() {
        filterActive = !filterActive;
        if (filterActive) {
            btn.classList.add('active');
            btn.textContent = 'Show all';
        } else {
            btn.classList.remove('active');
            btn.textContent = `🔍 Filter by: ${userFirstname} ${userLastname}`;
        }
        applyFilters();
    });

    // DEV: écoute de la recherche
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            searchTerm = this.value.trim().toLowerCase();
            applyFilters();
        });
    }

    // ⤵️ Ré-appliquer ton filtre quand l’utilisateur change de page dans DataTables
    if (dt) {
        window.jQuery(table).on('page.dt', function () {
            // attendre le rendu de la nouvelle page
            setTimeout(applyFilters, 0);
        });
    }

    function applyFilters() {
        // 1️⃣ trouver l'index de la colonne creator
        const headers = table.querySelectorAll('thead th');
        let creatorIndex = -1;
        headers.forEach((th, idx) => {
            if (th.classList.contains('creator')) creatorIndex = idx;
        });
        if (creatorIndex === -1) return;

        // 2️⃣ regrouper toutes les lignes en objets { header, items[] }
        const groups = [];
        let currentGroup = null;
        table.querySelectorAll('tbody tr').forEach(row => {
            if (row.classList.contains('dtrg-group')) {
                currentGroup = { header: row, items: [] };
                groups.push(currentGroup);
            } else if (currentGroup) {
                currentGroup.items.push(row);
            }
        });

        // 3️⃣ appliquer filtre + recherche AU NIVEAU DU GROUPE
        groups.forEach(group => {
            // DEV: tester si ce groupe doit être conservé
            const keepGroup = group.items.some(row => {
                // a) filtre utilisateur
                let match = true;
                if (filterActive) {
                    const txt = row.children[creatorIndex]?.textContent.trim() || '';
                    match = (txt === swappedName);
                }
                // b) filtre recherche
                if (match && searchTerm !== '') {
                    const cells = Array.from(row.cells).slice(0, row.cells.length - 3);
                    match = cells.some(cell =>
                        cell.textContent.toLowerCase().includes(searchTerm)
                    );
                }
                return match;
            });

            // DEV: afficher le header et toutes les lignes du groupe si matched
            group.header.style.display = keepGroup ? '' : 'none';
            group.items.forEach(row => {
                row.style.display = keepGroup ? '' : 'none';
            });
        });

        // DEV: si DataTable est utilisé et réaffecte l'affichage après draw
        if (window.dataTable) {
            window.dataTable.rows().invalidate().draw(false);
        }
    }
});
