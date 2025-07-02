document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('data-table');
    const rows = table.querySelectorAll('tbody tr');

    let currentStrainId = '', currentStrainName = '';

    // 1. Prétraitement : remplir cellules vides et propager ID/Souche
    for (const row of rows) {
        const idCell = row.querySelector('td.id');
        const nameCell = row.querySelector('td.name');

        if (idCell?.textContent.trim()) {
            currentStrainId = idCell.textContent.trim();
            currentStrainName = nameCell.textContent.trim();
        } else {
            idCell.textContent = currentStrainId;
            nameCell.textContent = currentStrainName;
        }
        
        const cells = row.querySelectorAll('td');
        const lastEditable = cells.length - 3;

        for (let i = 1; i < lastEditable; i++) {
            if (!cells[i].classList.contains('id') && !cells[i].textContent.trim()) {
                cells[i].textContent = '--';
            }
        }
    }
    
    const dataTable = $('#data-table').DataTable({
        deferRender: true,
        paging: true,
        searching: false, //on utilisera notre recherche personalise par groupe
        ordering: true,
        info: true,
        dom: '<"datatable-header d-flex justify-content-between align-items-center"lfr>t<"datatable-footer d-flex justify-content-between align-items-center"ip>',
        order: [],
        columnDefs: [
            { orderable: false, targets: [0,20, 21, 22] }
        ],
        rowGroup: {
            dataSrc: 1,
            startRender: function (rows, group) {
                const firstRow = rows.data()[0];
                const checkbox = firstRow[0];
                const id = firstRow[1];
                const name = firstRow[2];

                return checkbox + ' ID : ' + id + ' - Souche : ' + name;
            }
        },
        
        initComplete: function () {
            const api = this.api();

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

            // 2c. Gestion tri à 3 états : ascendant descendant et retour initial
            $('#data-table thead th').off('click.DT').on('click', function () {
                const colIdx = $(this).index();

                // ⚠️ Empêche le tri si la colonne est désactivée dans columnDefs
                const nonTriables = [0, 20, 21, 22];
                if (nonTriables.includes(colIdx)) return;

                const currentOrder = api.order();
                const currentColOrder = currentOrder.find(o => o[0] === colIdx);

                $('#data-table thead th').removeClass('sorting_asc sorting_desc').addClass('sorting');

                if (!currentColOrder) {
                    api.order([[colIdx, 'asc']]).draw();
                    $(this).removeClass('sorting').addClass('sorting_asc');
                } else if (currentColOrder[1] === 'asc') {
                    api.order([[colIdx, 'desc']]).draw();
                    $(this).removeClass('sorting').addClass('sorting_desc');
                } else {
                    api.order([]).draw();
                    $(this).removeClass('sorting_asc sorting_desc').addClass('sorting');
                }
            });
        }
    });

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
    

    // 4. Recherche personnalisée par groupe 
    const searchInput = document.getElementById('customSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();

            // Si le champ est vide, réactiver la pagination à 25
            if (term === '') {
                dataTable.page.len(25).draw();

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

    // 5. Popups (info/fichier) pour le téléchargement et les bonnes directions
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