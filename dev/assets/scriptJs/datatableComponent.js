document.addEventListener('DOMContentLoaded', function () {
    // Initialiser toutes les tables dont l'ID commence par "data-table-"
    document.querySelectorAll('table[id^="data-table-"]').forEach(table => {
        initDataTableWithFilters(table.id);
    });

    // Gestion des popups "data-info"
    document.querySelectorAll('td[data-info]').forEach(cell => {
        cell.addEventListener('click', function (event) {
            event.stopPropagation(); // Empêche la propagation du clic

            const info = this.dataset.info || '--';
            const popup = document.getElementById('infoPopup');
            const popupDetails = document.getElementById('popupDetails');

            // Remplir et positionner le popup
            popupDetails.textContent = info;
            popup.style.display = 'block';
            popup.style.left = `${event.pageX + 10}px`;
            popup.style.top = `${event.pageY + 10}px`;
        });
    });

    // Cacher le popup si on clique ailleurs
    document.addEventListener('click', function (event) {
        const popup = document.getElementById('infoPopup');
        if (!popup) return; // ← SECU ici AVANT d'appeler contains !
        if (!popup.contains(event.target) && !event.target.closest('td[data-info]')) {
            popup.style.display = 'none';
        }
    });
});

function initDataTableWithFilters(tableId) {
    const table = $('#' + tableId);
    const totalColumns = table.find('thead th').length;

    // Ne pas réinitialiser une table déjà traitée
    if ($.fn.DataTable.isDataTable(table)) {
        return;
    }

    // Ajouter une ligne de filtres si elle n'existe pas
    if (table.find('thead tr.filters').length === 0) {
        const filterRow = $('<tr class="filters"></tr>').appendTo(table.find('thead'));
        table.find('thead th').each(function (index) {
            // Ne pas ajouter de filtre pour la 1re colonne (checkbox) ou les 3 dernières
            if ($(this).find('input[type=checkbox]').length > 0 || index >= totalColumns - 3) {
                filterRow.append('<th></th>');
            } else {
                filterRow.append('<th><input type="text" placeholder="Filtrer" style="width: 100%; box-sizing: border-box;" /></th>');
            }
        });
    }

    // Initialisation DataTable
    const dataTable = table.DataTable({
        orderCellsTop: true,
        fixedHeader: true,
        paging: true,
        searching: true,
        info: true,
        lengthChange: true,
        dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><"dt-buttons-top"B>t<"datatable-footer d-flex justify-content-between align-items-center"ip>',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/en-GB.json"
        },
        columnDefs: [
            { orderable: false, targets: 'no-sort' }
        ],
        initComplete: function () {
            table.css('visibility', 'visible');
            $('#btn-delete-multiple').show(); // Si bouton présent
        }
    });

    // Appliquer le filtre à chaque colonne
    table.find('thead tr.filters input').on('keyup change clear', function () {
        const colIndex = $(this).parent().index();
        dataTable.column(colIndex).search(this.value).draw();
    });

    // Gestion du "Tout sélectionner"
    $('#select-all').on('click', function () {
        const isChecked = $(this).prop('checked');
        table.find('tbody input.select-checkbox').prop('checked', isChecked);
    });

    // Synchroniser les cases à cocher individuelles
    table.find('tbody').on('change', 'input.select-checkbox', function () {
        const allChecked = table.find('tbody input.select-checkbox').length === table.find('tbody input.select-checkbox:checked').length;
        $('#select-all').prop('checked', allChecked);
    });

    // Gestion de la visibilité des colonnes
    document.querySelectorAll('.toggle-column').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const colIndex = parseInt(this.getAttribute('data-column'), 10);
            const col = dataTable.column(colIndex);
            col.visible(this.checked);
        });
    });
}