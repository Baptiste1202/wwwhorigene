document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('data-table');
    if (!table) {
        // La table n'est pas sur cette page, on stoppe tout
        return;
    }
    const rows = table.querySelectorAll('tbody tr');
    let previousPageLength = null;

    const dataTable = $('#data-table').DataTable({
        deferRender: true,
        paging: true,
        pageLength: 25,
        lengthMenu: [
            [10, 25, 50, 100, 250, 500, 1000, 5000, 10000],
            ['10','25','50','100','250','500','1000','5000','10000']
        ],
        stateSave: true,
        // <<<<< AJOUTE CES DEUX CALLBACKS ICI
        stateSaveParams: function (settings, data) {
            // Ne mémorise pas la longueur → ton défaut (25) reste actif au prochain chargement
            delete data.length;
        },
        stateLoadParams: function (settings, data) {
            // Si pas de ?highlight, on force 25 au chargement
            if (!new URLSearchParams(location.search).has('highlight')) {
            data.length = 25;
            }
        },
        // >>>>>

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
            { orderable: false, targets: [0,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22] },
        {
        targets: 3,  // convertion pour le trie sur date
            render: function (data, type) {
                if (type !== 'sort' && type !== 'type') return data;

                const s = (data == null ? '' : String(data)).trim();
                // vides / "--" => tout en bas
                if (!s || s === '--') return Number.POSITIVE_INFINITY;

                // parse dd/mm/yyyy (ou dd-mm-yyyy / dd.mm.yyyy)
                const m = s.match(/^(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{4})$/);
                if (!m) return Number.POSITIVE_INFINITY;

                const d  = parseInt(m[1], 10);
                const mo = parseInt(m[2], 10) - 1; // JS: mois 0..11
                const y  = parseInt(m[3], 10);

                return new Date(y, mo, d).getTime(); // clé numérique triable
            }
        }
        ],
        orderClasses: false,
        rowGroup: {
            dataSrc: 1,
            startRender(rows) {
                const [ checkbox, id, name ] = rows.data()[0];
                return 'ID : ' + id + ' – Souche : ' + name;
            }
        },
        
        initComplete: function () {
            const api = this.api();
            api.order([[1,'desc']]).draw();   // ⬅ impose le tri à chaque chargement sur ID de souche en DESC

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

            // 2c. Aprés modification d'une souche → trouve la ligne, la rend visible, centre le scroll, flash 3s. */
            const highlightId = new URLSearchParams(location.search).get('highlight');

            if (highlightId) {
                const selector = '#strain-' + (window.CSS && CSS.escape ? CSS.escape(String(highlightId)) : String(highlightId));
                const $box = $('#data-table-wrapper'); // conteneur scrollable du tableau
                const $lengthSelect = $('#data-table_wrapper .dataTables_length select');
                const wantLen = 10000;
                let done = false;

                function tryHighlight() {
                    if (done || !$box.length) return;

                    const $tr = $(selector);

                    // ligne présente ET visible ?
                    if (!$tr.length || !$tr.is(':visible')) return;

                    const boxH   = $box.height();
                    const trH    = $tr.outerHeight(true);

                    // position relative au conteneur, + fiable que offset() avec des rows masquées
                    const relTop = $tr.position().top; 
                    const target = $box.scrollTop() + relTop - (boxH / 2 - trH / 2);

                    const maxScroll = $box[0].scrollHeight - boxH;
                    const clamped   = Math.max(0, Math.min(target, maxScroll));

                    $box.stop(true).animate({ scrollTop: clamped }, 300);

                    // flash de surbrillance
                    $tr.addClass('dt-highlight');
                    setTimeout(() => $tr.removeClass('dt-highlight'), 3000);

                    done = true;
                    $('#data-table').off('draw.dt', onDraw);
                }

                function onDraw() {
                    // laisse le temps aux scripts de filtrage de finir (rowgroup/applyFilters)
                    setTimeout(tryHighlight, 250); // ⬅ délai ajouté
                }

                // essayer tout de suite avec un petit délai
                setTimeout(tryHighlight, 250);

                // sinon, attendre les prochains redraws
                if (!done) {
                    $('#data-table').on('draw.dt', onDraw);
                }

                // garantir qu'on affiche 10000 lignes
                if ($lengthSelect.length) {
                    if (parseInt($lengthSelect.val(), 10) !== wantLen) {
                        $lengthSelect.val(String(wantLen)).trigger('change'); // provoque un draw
                    } else if (!done) {
                        api.page.len(wantLen).draw(false);
                    }
                } else if (typeof api.page.len === 'function') {
                    if (api.page.len() !== wantLen) {
                        api.page.len(wantLen).draw(false);
                    } else if (!done) {
                        api.draw(false);
                    }
                }
            }         
        }
    });

    // --- Partie 2 : On deplace length, filter et buttons dans une div au dessus de la table pour la fixer
    $('#data-table_wrapper .dataTables_length, #data-table_wrapper .dataTables_filter, #data-table_wrapper .dt-buttons, .table-action-bar')
    .appendTo('#table-header-toolbar');

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
        phenotype: 'phenotype',
        drugResistanceOnStrain: 'drugs'
    };

    document.querySelectorAll('td[data-info]').forEach(cell => {
        cell.addEventListener('click', event => {
            const info = cell.dataset.info;
            const fileName = cell.dataset.file;
            const popup = document.getElementById('infoPopup');
            const downloadLink = document.getElementById('popupDownload');

            // AJOUTE cette ligne pour éviter l’erreur
            if (!popup || !downloadLink) return;

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
                downloadLink.href = `/documents/download/${fileType}/${fileName}`;
                downloadLink.style.display = 'inline-block';
                //test MR
                //test MR2

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

