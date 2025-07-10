document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('data-table');
    const rows = table.querySelectorAll('tbody tr');
    let previousPageLength = null;

    const dataTable = $('#data-table').DataTable({
        deferRender: true,
        paging: true,
        searching: false, //on utilisera notre recherche personalise par groupe
        ordering: true,
        info: true,
        dom:'lfBtip',   
        buttons: [
            { extend: 'copy',  exportOptions: { columns: ':visible', rows: (i,d,n) => n.style.display!=='none' } },
            { extend: 'csv',   exportOptions: { columns: ':visible', rows: (i,d,n) => n.style.display!=='none' } },
            { extend: 'excel', exportOptions: { columns: ':visible', rows: (i,d,n) => n.style.display!=='none' } },
            { extend: 'pdf',   exportOptions: { columns: ':visible', rows: (i,d,n) => n.style.display!=='none' } },
            { extend: 'print', exportOptions: { columns: ':visible', rows: (i,d,n) => n.style.display!=='none' } }
        ],
        order: [],
        columnDefs: [
            { orderable: false, targets: [0,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20, 21, 22] }
        ],
        rowGroup: {
            dataSrc: 1,
            startRender(rows) {
                const [ checkbox, id, name ] = rows.data()[0];
                return checkbox + ' ID : ' + id + ' – Souche : ' + name;
            }
        },
        
        initComplete: function () {
            const api = this.api();

            // 1a. On récupère la pagination actuelle choisie par l'utilisateur
            const lengthSelect = document.querySelector('.dataTables_length select');
            if (lengthSelect) {
                previousPageLength = parseInt(lengthSelect.value);

                lengthSelect.addEventListener('change', function () {
                    previousPageLength = parseInt(this.value);
                });
            }

            // 2a. Affichage table une fois prête et reorganisation de la structure
            table.style.visibility = 'visible';

            // 2b. Checkboxes pour colonne : application + événements
            document.querySelectorAll('input.toggle-column').forEach(checkbox => {
                const colIdx = +checkbox.getAttribute('data-column');
                api.column(colIdx).visible(checkbox.checked);

                checkbox.addEventListener('change', function () {
                    api.column(colIdx).visible(this.checked);
                });
            });
        }
    });

    // --- Partie 2 : On deplace length, filter et buttons dans une div au dessus de la table pour la fixer
    $('#data-table_wrapper .dataTables_length, #data-table_wrapper .dataTables_filter, #data-table_wrapper .dt-buttons')
    .appendTo('#table-controls-header');

    // --- Partie 3 : Select All Checkbox ---
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => {
            const checkboxes = document.querySelectorAll('input.select-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
        });

        const childCheckboxes = document.querySelectorAll('input.select-checkbox');
        childCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                if (!cb.checked) {
                    selectAllCheckbox.checked = false;
                } else {
                    const allChecked = Array.from(childCheckboxes).every(chk => chk.checked);
                    selectAllCheckbox.checked = allChecked;
                }
            });
        });
    }
    
    // --- Partie 4. Recherche personnalisée par groupe 
    const searchInput = document.getElementById('customSearch');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();

            if (term === '') {
                // Décoche toutes les cases de lignes visibles
                const selectAllCheckbox = document.getElementById('select-all');
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = false;
                }
                document.querySelectorAll('input.select-checkbox').forEach(cb => {
                    cb.checked = false;
                    cb.dispatchEvent(new Event('change'));
                });
                // Reviens a la pagination initiale
                if (previousPageLength !== null) {
                    dataTable.page.len(previousPageLength).draw();
                }

                // Réafficher toutes les lignes
                dataTable.rows().nodes().toArray().forEach(row => {
                    row.style.display = '';
                    const groupHeader = row.previousElementSibling;
                    if (groupHeader?.classList.contains('dtrg-group')) {
                        groupHeader.style.display = '';
                    }
                });
                return;
            }

            // Sinon, désactiver la pagination pour afficher toutes les lignes
            dataTable.page.len(-1).draw();

            const allRows = dataTable.rows().nodes().toArray();
            const groups = {};

            for (const row of allRows) {
                const idCell = row.querySelector('td.id');
                if (!idCell) continue;

                const groupId = idCell.textContent.trim();
                groups[groupId] ??= [];
                groups[groupId].push(row);
            }

            for (const groupId in groups) {
                const groupRows = groups[groupId];
                const match = groupRows.some(row => {
                    return Array.from(row.querySelectorAll('td')).some((cell, idx) => {
                        if (idx >= cell.parentNode.cells.length - 3) return false;
                        return cell.textContent.toLowerCase().includes(term);
                    });
                });

                for (const row of groupRows) {
                    row.style.display = match ? '' : 'none';
                }

                const firstRow = groupRows[0];
                if (firstRow) {
                    const groupHeader = firstRow.previousElementSibling;
                    if (groupHeader?.classList.contains('dtrg-group') && groupHeader.textContent.includes(groupId)) {
                        groupHeader.style.display = match ? '' : 'none';
                    }
                }
            }
        });
    }

    // Partie 5. Popups (info/fichier) pour le téléchargement et les bonnes directions
    const typeMap = {
        sequencing: 'sequencing',
        transformability: 'transformability',
        drugResistanceOnStrain: 'drugs'
    };

    document.querySelectorAll('td[data-info]').forEach(cell => {
        cell.addEventListener('click', event => {
            const info = cell.dataset.info;
            const fileName = cell.dataset.file;
            const popup = document.getElementById('infoPopup');
            const downloadLink = document.getElementById('popupDownload');

            let fileType = null;
            for (const cls in typeMap) {
                if (cell.classList.contains(cls)) {
                    fileType = typeMap[cls];
                    break;
                }
            }

            // Remplir les détails dans le popup
            document.getElementById('popupTitle').innerText = 'Détails';
            document.getElementById('popupDetails').innerText = info;

            if (fileName && fileName !== '--' && fileType) {
                downloadLink.href = `/public/docs/${fileType}/${fileName}`;
                downloadLink.style.display = 'inline-block';

                // Forcer le téléchargement du fichier
                downloadLink.setAttribute('download', fileName);
            } else {
                downloadLink.style.display = 'none';
                downloadLink.removeAttribute('download');
                downloadLink.removeAttribute('href');
            }

            popup.style.display = 'block';
            popup.style.left = `${event.pageX + 10}px`;
            popup.style.top = `${event.pageY + 10}px`;
        });
    });

    document.addEventListener('click', event => {
        const popup = document.getElementById('infoPopup');
        if (!popup.contains(event.target) && !event.target.closest('[data-info]')) {
            popup.style.display = 'none';
        }
    });
});

