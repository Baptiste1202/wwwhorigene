document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('table[id^="data-table-"]').forEach(table => {
        initDataTableWithFilters(table.id);
    });
});


function initDataTableWithFilters(tableId) {
        const table = $('#' + tableId);
        const totalColumns = table.find('thead th').length;

        // Si la table est déjà initialisée, on ne fait rien (ou on la détruit avant)
        if ( $.fn.DataTable.isDataTable(table) ) {
            // Option 1: ne rien faire, juste retourner
            return
        }

        // Ajout d'une ligne de filtres sous le header (si pas déjà fait)
        if (table.find('thead tr.filters').length === 0) {
            const filterRow = $('<tr class="filters"></tr>').appendTo(table.find('thead'));
            table.find('thead th').each(function(index) {
                // Ne pas ajouter d'input pour la 1ère colonne (checkbox) ou les 3 dernières colonnes
                if ($(this).find('input[type=checkbox]').length > 0 || index >= totalColumns - 3) {
                    filterRow.append('<th></th>');
                } else {
                    filterRow.append('<th><input type="text" placeholder="Filtrer" style="width: 100%; box-sizing: border-box;" /></th>');
                }
            });
        }

        const dataTable = table.DataTable({
            orderCellsTop: true,
            fixedHeader: true,
            paging: true,
            searching: true,
            info: true,
            lengthChange: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            columnDefs: [
                // Désactive le tri pour la première colonne et les 3 dernières en class no-sort
                { orderable: false, targets: 'no-sort' }
            ],
            // Affiche la table uniquement quand DataTables est complètement initialisé
            initComplete: function() {
                table.css('visibility', 'visible');
                $('#btn-delete-multiple').show(); // Affiche le bouton
            }
        });

        // Applique le filtre sur chaque colonne (si input présent)
        table.find('thead tr.filters input').on('keyup change clear', function() {
            const colIndex = $(this).parent().index();
            dataTable.column(colIndex).search(this.value).draw();
        });

        // Gestion de la checkbox "Tout sélectionner"
        $('#select-all').on('click', function() {
            const isChecked = $(this).prop('checked');
            table.find('tbody input.select-checkbox').prop('checked', isChecked);
        });

        // Synchronisation des checkboxes individuelles avec "Tout sélectionner"
        table.find('tbody').on('change', 'input.select-checkbox', function() {
            const allChecked = table.find('tbody input.select-checkbox').length === table.find('tbody input.select-checkbox:checked').length;
            $('#select-all').prop('checked', allChecked);
        });

        // Gestion du menu de colonnes masquées/visibles (si checkboxes présentes)
        document.querySelectorAll('.toggle-column').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const colIndex = parseInt(this.getAttribute('data-column'), 10);
                const col = dataTable.column(colIndex);
                col.visible(this.checked);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDataTableWithFilters('data-table-collect');
});