// assets/scriptJs/downloadFile.js
$(document).ready(function () {
    const $modal        = $('#downloadModal');
    const $modalDialog  = $modal.find('.modal-dialog');
    const $openBtn      = $('#multi-download-btn');
    const $closeBtns    = $('#modal-close, #modal-cancel');
    const $confirmBtn   = $('#confirm-download-btn');
    const $fileTypeAll  = $('input[name="fileType"][value="all"]');
    const $fileTypes    = $('input[name="fileType"]').not('[value="all"]');

    // --- Fonctions d’affichage d’erreur dans la modal ---
    function showError(msg) {
        let $err = $modalDialog.find('.download-error');
        if (!$err.length) {
            $err = $('<div class="download-error"></div>')
                .css({
                    color:    '#C00',
                    padding:  '0.5em 1em',
                    'margin-bottom': '1em',
                    'background-color': '#fdf2f2',
                    'border-radius': '4px'
                });
            $modalDialog.prepend($err);
        }
        $err.text(msg).show();
    }
    function hideError() {
        $modalDialog.find('.download-error').hide();
    }

    // --- Gestion des interactions entre “all” et les autres types ---
    $fileTypeAll.on('change', () => {
        if ($fileTypeAll.is(':checked')) {
            $fileTypes.prop('checked', false);
            console.log('[Download] "all" checked → autres décochées');
        }
    });
    $fileTypes.on('change', function() {
        if ($(this).is(':checked')) {
            $fileTypeAll.prop('checked', false);
            console.log('[Download] Option individuelle cochée → "all" décochée');
        }
    });

    // 1) Ouvrir la modal
    $openBtn.on('click', () => {
        hideError();
        console.log('[Download] Open button clicked');
        // On ne compte que les cases visibles cochées
        const selectedCount = $('input[name="selected_strain[]"]:checked').filter(function() {
            return $(this).is(':visible');
        }).length;
        console.log('[Download] Number of visible strains selected:', selectedCount);
        if (selectedCount === 0) {
            alert('Please select at least one strain first.');
            return;
        }
        console.log('[Download] Showing modal');
        $modal.css('display', 'flex');
    });

    // 2) Fermer la modal
    $closeBtns.on('click', () => {
        console.log('[Download] Close button clicked, hiding modal');
        $modal.hide();
    });

    // 3) Fermer si clic hors du dialogue
    $modal.on('click', e => {
        if (e.target === $modal[0]) {
            console.log('[Download] Clicked outside modal-dialog, hiding modal');
            $modal.hide();
        }
    });

    // --- Select All qui ne coche que les cases visibles ---
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => {
            const visibleCheckboxes = Array.from(document.querySelectorAll('input.select-checkbox'))
                .filter(cb => cb.offsetParent !== null);
            visibleCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
        });

        const childCheckboxes = document.querySelectorAll('input.select-checkbox');
        childCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                if (!cb.checked) {
                    selectAllCheckbox.checked = false;
                } else {
                    // Vérifie si toutes les visibles sont cochées
                    const visibleCheckboxes = Array.from(document.querySelectorAll('input.select-checkbox'))
                        .filter(cb => cb.offsetParent !== null);
                    const allChecked = visibleCheckboxes.every(chk => chk.checked);
                    selectAllCheckbox.checked = allChecked;
                }
            });
        });
    }

    // 4) Handler du bouton "Télécharger"
    $confirmBtn.on('click', async () => {
        hideError();
        console.log('[Download] Confirm download clicked');

        // 4.1) IDs de souches cochées (uniquement cases visibles)
        let strainIds = $('input[name="selected_strain[]"]:checked').filter(function() {
            return $(this).is(':visible');
        }).map((_, el) => el.value).get();

        console.log('[Download] Raw visible Strain IDs:', strainIds);
        strainIds = [...new Set(strainIds)];
        console.log('[Download] De-duplicated Strain IDs:', strainIds);
        if (!strainIds.length) {
            showError('Please select at least one strain.');
            return;
        }

        // 4.2) Types cochés
        let types = $('input[name="fileType"]:checked')
            .map((_, el) => el.value)
            .get();
        console.log('[Download] File types selected:', types);
        if (!types.length) {
            showError('Please select at least one file type.');
            return;
        }
        if (types.includes('all')) {
            types = ['sequencing','transformability','drugs'];
            console.log('[Download] "all" detected, expanded types to:', types);
        }

        // 4.3) Construire fileEntries avec { id, type, name }
        const fileEntries = [];
        strainIds.forEach(strainId => {
            const $rows = $('#data-table tbody tr').filter(function () {
                return $(this).find('td.id').text().trim() === strainId && $(this).is(':visible');
            });
            console.log(`[Download] Rows for strain ${strainId}:`, $rows.length);
            $rows.each(function (rowIndex) {
                const $row = $(this);
                types.forEach(type => {
                    const selector = {
                        sequencing:       'td.sequencing',
                        transformability: 'td.transformability',
                        drugs:            'td.drugResistanceOnStrain'
                    }[type];
                    const $cell = $row.find(selector);
                    if (!$cell.length) return;
                    const fn = $cell.data('file');
                    if (fn && fn !== '--') {
                        fileEntries.push({ id: strainId, type, name: fn });
                        console.log('[Download] Added entry:', { id: strainId, type, name: fn });
                    }
                });
            });
        });
        console.log('[Download] All file entries:', fileEntries);
        if (!fileEntries.length) {
            showError('Pas de fichier disponible pour votre sélection.');
            return;
        }

        // 4.4) Préparer payload
        const extension = $('#extension-input').val().trim();
        const payload   = { entries: fileEntries, extension };
        console.log('[Download] Payload to send:', payload);

        // 4.5) Envoi via fetch
        const url = '/public/download-multiple';
        console.log('[Download] Fetch POST to', url);
        try {
            const res = await fetch(url, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify(payload),
            });

            if (!res.ok) {
                const txt = await res.text();
                console.error('[Download] Server error response:', txt);
                showError('Server error: ' + txt);
                return;
            }

            const blob = await res.blob();
            console.log('[Download] Fetch success, received blob');
            const downloadUrl = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href     = downloadUrl;
            a.download = `files_${Date.now()}.zip`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(downloadUrl);
            console.log('[Download] Download triggered, hiding modal');
            $modal.hide();

        } catch (err) {
            console.error('[Download] Fetch exception:', err);
            showError('An unexpected error occurred: ' + err.message);
        }
    });
});
