document.addEventListener('DOMContentLoaded', function () {
    const table       = document.querySelector('#data-table');
    const btn         = document.getElementById('filterByUserButton');
    const searchInput = document.getElementById('customSearch');

    if (!btn || !table) return;

    // --- utilitaires URL/href ---
    function setQueryParamOnLinks(param, value, selector = 'a.button-crud') {
        document.querySelectorAll(selector).forEach(a => {
            const rawHref = a.getAttribute('href') || '';
            if (!rawHref) return;
            const url = new URL(rawHref, window.location.origin);
            url.searchParams.set(param, value);
            a.setAttribute('href', url.pathname + url.search + url.hash);
        });
    }

    function setQueryParamInLocation(param, value) {
        const url = new URL(window.location.href);
        url.searchParams.set(param, value);
        history.replaceState(null, '', url.pathname + url.search + url.hash);
    }

    // récupère l’instance DataTables si présente
    let dt = null;
    if (window.jQuery && window.jQuery.fn.dataTable.isDataTable(table)) {
        dt = window.jQuery(table).DataTable();
    }

    // nom à filtrer
    const userFirstname = btn.dataset.firstname || '';
    const userLastname  = btn.dataset.lastname  || '';
    const swappedName   = `${userLastname} ${userFirstname}`.trim();

    // état initial du filtre selon l’URL (si filter != 'all', on l’active)
    const urlInit = new URL(window.location.href);
    const initialFilterParam = urlInit.searchParams.get('filter') || 'all';
    let filterActive = (initialFilterParam && initialFilterParam !== 'all');
    let searchTerm   = '';

    // init bouton
    btn.dataset.active = String(filterActive);
    btn.classList.toggle('active', filterActive);
    btn.textContent = filterActive ? 'Show all' : `🔍 Filter by: ${userFirstname} ${userLastname}`;

    btn.addEventListener('click', function() {
        filterActive = !filterActive;
        btn.dataset.active = String(filterActive);
        btn.classList.toggle('active', filterActive);
        btn.textContent = filterActive ? 'Show all' : `🔍 Filter by: ${userFirstname} ${userLastname}`;
        applyFilters(); // met aussi à jour les liens & l’URL
        if (dt) dt.draw(false); // retrigger un redraw “léger”
    });

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            searchTerm = this.value.trim().toLowerCase();
            applyFilters();
            if (dt) dt.draw(false);
        });
    }

    // ré-applique filtre à chaque redraw DataTables (pagination, tri, etc.)
    if (dt) {
        dt.on('draw', function () {
            applyFilters();
        });
    }

    function applyFilters() {
        // 1) logique d’affichage (groupes + lignes)
        const headers = table.querySelectorAll('thead th');
        let creatorIndex = -1;
        headers.forEach((th, idx) => {
            if (th.classList.contains('creator')) creatorIndex = idx;
        });
        if (creatorIndex === -1) {
            const filterValueFallback = filterActive ? swappedName : 'all';
            setQueryParamOnLinks('filter', filterValueFallback, '#data-table a.button-crud');
            setQueryParamInLocation('filter', filterValueFallback);
            return;
        }

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            // Si jamais il reste des lignes de groupement (RowGroup)
            if (row.classList.contains('dtrg-group')) {
                return;
            }

            let match = true;

            // 1) Filtre par utilisateur (colonne creatorIndex)
            if (filterActive && creatorIndex !== -1) {
                const txt = row.children[creatorIndex]?.textContent.trim() || '';
                match = (txt === swappedName);
            }

            // 2) Filtre texte (#customSearch)
            if (match && searchTerm !== '') {
                // On ignore les 3 dernières colonnes (actions)
                const limit = Math.max(0, row.cells.length - 3);
                const cells = Array.from(row.cells).slice(0, limit);

                match = cells.some(cell =>
                    cell.textContent.toLowerCase().includes(searchTerm)
                );
            }

            // 3) Afficher / cacher la ligne
            row.style.display = match ? '' : 'none';
        });

        // 2) pousser la valeur du filtre dans TOUS les liens "Modifier"
        const filterValue = filterActive ? swappedName : 'all';
        setQueryParamOnLinks('filter', filterValue, '#data-table a.button-crud');

        // 3) et refléter dans l’URL de la page
        setQueryParamInLocation('filter', filterValue);
    }

    // premier passage : appliquer l’état initial (y compris URLs des liens)
    applyFilters();
});
