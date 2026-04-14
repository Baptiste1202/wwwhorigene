document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('data-table');
    if (!table) {
        return;
    }

    const $table = $('#data-table');
    const searchParams = new URLSearchParams(location.search);
    const highlightId = searchParams.get('highlight');

    const exportOptions = {
        columns: ':visible',
        rows: (i, d, n) => n.style.display !== 'none'
    };

    // 🔹 AJOUT FILTRES (AVANT DataTable)
    if ($table.find('thead tr.filters').length === 0) {

        const filterRow = $('<tr class="filters"></tr>').appendTo($table.find('thead'));

        const total = $table.find('thead th').length;

        $table.find('thead th').each(function (index) {

            if (index < 3 || index >= total - 3) {
                // ❌ pas de filtre
                filterRow.append('<th></th>');
            } else {
                // ✔ filtre actif
                const title = $(this).text().trim();

                filterRow.append(`
                    <th>
                        <input type="text" placeholder="${title}" style="width:100%" />
                    </th>
                `);
            }
        });
    }


    const dataTable = $table.DataTable({
        deferRender: true,
        paging: true,
        pageLength: 50,
        lengthMenu: [
            [10, 25, 50, 100, 250, 500, 1000, 5000, 10000],
            ['10', '25', '50', '100', '250', '500', '1000', '5000', '10000']
        ],

        searching: true,
        ordering: true,
        info: true,
        dom: 'lfBtip',

        buttons: [
            { extend: 'copy', exportOptions },
            { extend: 'csv', exportOptions },
            { extend: 'excel', exportOptions },
            {
                extend: 'pdf',
                orientation: 'landscape',
                pageSize: 'A3',
                exportOptions
            },
            { extend: 'print', exportOptions }
        ],

        order: [],

        columnDefs: [
            {
                orderable: false,
                targets: [0, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]
            },
            {
                targets: 3,
                render: function (data, type) {
                    if (type !== 'sort' && type !== 'type') {
                        return data;
                    }

                    const s = (data == null ? '' : String(data)).trim();

                    if (!s || s === '--') {
                        return Number.POSITIVE_INFINITY;
                    }

                    const m = s.match(/^(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{4})$/);
                    if (!m) {
                        return Number.POSITIVE_INFINITY;
                    }

                    const d = parseInt(m[1], 10);
                    const mo = parseInt(m[2], 10) - 1;
                    const y = parseInt(m[3], 10);

                    return new Date(y, mo, d).getTime();
                }
            }
        ],

        orderClasses: false,
        orderCellsTop: true,
        rowGroup: false,

        initComplete: function () {
            const api = this.api();

            // Tri forcé au chargement sur ID de souche en DESC
            api.order([[1, 'desc']]).draw();

            // Affiche la table une fois prête
            table.style.visibility = 'visible';

            // Gestion des checkboxes pour afficher/masquer les colonnes
            document.querySelectorAll('input.toggle-column').forEach((checkbox) => {
                const colIdx = parseInt(checkbox.getAttribute('data-column'), 10);

                api.column(colIdx).visible(checkbox.checked);

                checkbox.addEventListener('change', function () {
                    api.column(colIdx).visible(this.checked);
                });
            });

            // AACTIVER FILTRES PAR COLONNE
            $table.find('thead tr.filters input').on('keyup change', function () {
                const colIndex = $(this).closest('th').index();
                dataTable.column(colIndex).search(this.value).draw();
            });

            // Highlight après modification d'une souche
            if (highlightId) {
                const selector = '#strain-' + (
                    window.CSS && CSS.escape
                        ? CSS.escape(String(highlightId))
                        : String(highlightId)
                );

                const $box = $('#data-table-wrapper');
                const $lengthSelect = $('#data-table_wrapper .dataTables_length select');
                const wantLen = 10000;
                let done = false;

                function tryHighlight() {
                    if (done || !$box.length) {
                        return;
                    }

                    const $tr = $(selector);
                    if (!$tr.length || !$tr.is(':visible')) {
                        return;
                    }

                    const boxH = $box.height();
                    const elH = $tr.outerHeight(true);
                    const relTop = $tr.position().top;
                    const target = $box.scrollTop() + relTop - (boxH / 2 - elH / 2);

                    const maxScroll = $box[0].scrollHeight - boxH;
                    const clamped = Math.max(0, Math.min(target, maxScroll));

                    $box.stop(true).animate({ scrollTop: clamped }, 300);

                    $tr.addClass('dt-highlight');
                    setTimeout(() => $tr.removeClass('dt-highlight'), 3000);

                    done = true;
                    $table.off('draw.dt', onDraw);
                }

                function onDraw() {
                    setTimeout(tryHighlight, 250);
                }

                setTimeout(tryHighlight, 250);

                if (!done) {
                    $table.on('draw.dt', onDraw);
                }

                // Force l'affichage d'un grand nombre de lignes pour retrouver la souche
                if ($lengthSelect.length) {
                    if (parseInt($lengthSelect.val(), 10) !== wantLen) {
                        $lengthSelect.val(String(wantLen)).trigger('change');
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

    $('#data-table_wrapper .dataTables_length, #data-table_wrapper .dt-buttons, .table-action-bar')
    .appendTo('#table-header-toolbar');

    $('#data-table_wrapper .dataTables_filter')
    .appendTo('.table-header-search');

    // Déplace info + pagination dans le footer
    $('#data-table_wrapper .dataTables_info, #data-table_wrapper .dataTables_paginate')
        .appendTo('#table-controls-footer');

    // Select all
    const selectAllCheckbox = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('input.select-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => {
            rowCheckboxes.forEach((cb) => {
                cb.checked = selectAllCheckbox.checked;
            });
        });

        rowCheckboxes.forEach((cb) => {
            cb.addEventListener('change', () => {
                if (!cb.checked) {
                    selectAllCheckbox.checked = false;
                    return;
                }

                const allChecked = Array.from(rowCheckboxes).every((chk) => chk.checked);
                selectAllCheckbox.checked = allChecked;
            });
        });
    }

    // Popups info/fichier
    const typeMap = {
        sequencing: 'sequencing',
        phenotype: 'phenotype',
        drugResistanceOnStrain: 'drugs'
    };

    let lastTriggerCell = null;

    document.querySelectorAll('td div[data-info]').forEach((cell) => {
        cell.addEventListener('click', (event) => {
            event.stopPropagation();

            const info = cell.dataset.info;
            const fileName = cell.dataset.file;
            const popup = document.getElementById('infoPopup');
            const downloadLink = document.getElementById('popupDownload');
            const popupTitle = document.getElementById('popupTitle');
            const popupDetails = document.getElementById('popupDetails');

            if (!popup || !downloadLink || !popupTitle || !popupDetails) {
                return;
            }

            const isSameCell = lastTriggerCell === cell;
            const isVisible = popup.style.display !== 'none' && popup.style.display !== '';

            if (isSameCell && isVisible) {
                popup.style.display = 'none';
                lastTriggerCell = null;
                return;
            }

            let fileType = null;
            for (const cls in typeMap) {
                if (cell.classList.contains(cls)) {
                    fileType = typeMap[cls];
                    break;
                }
            }

            popupTitle.innerText = 'Détails';
            popupDetails.innerText = info;

            const canDownload =
                fileName &&
                fileName !== '--' &&
                fileType &&
                !cell.classList.contains('description') &&
                !cell.classList.contains('comment');

            if (canDownload) {
                downloadLink.href = `/documents/download/${fileType}/${fileName}`;
                downloadLink.style.display = 'inline-block';
                downloadLink.setAttribute('download', fileName);
            } else {
                downloadLink.style.display = 'none';
                downloadLink.removeAttribute('download');
                downloadLink.removeAttribute('href');
            }

            popup.style.display = 'block';
            popup.style.left = `${event.pageX + 10}px`;
            popup.style.top = `${event.pageY + 10}px`;

            lastTriggerCell = cell;
        });
    });

    // Ferme le popup si clic ailleurs
    document.addEventListener('click', (event) => {
        const popup = document.getElementById('infoPopup');
        if (!popup) {
            return;
        }

        if (!popup.contains(event.target) && !event.target.closest('td div[data-info]')) {
            popup.style.display = 'none';
            lastTriggerCell = null;
        }
    });
});