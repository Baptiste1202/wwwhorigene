// assets/scriptJs/downloadFile.js - VERSION SÉCURISÉE
$(document).ready(function () {
    const $modal = $('#downloadModal');
    const $modalDialog = $modal.find('.modal-dialog');
    const $modalBody = $modal.find('.modal-body');
    const $openBtn = $('#multi-download-btn');
    const $closeBtns = $('#modal-close, #modal-cancel');
    const $confirmBtn = $('#confirm-download-btn');
    const $fileTypeAll = $('input[name="fileType"][value="all"]');
    const $fileTypes = $('input[name="fileType"]').not('[value="all"]');
    const $phenotypeOpt = $('input[name="fileType"][value="phenotype"]');
    const $extInput = $('#extension-input');

    // ============= SECURITY UTILITIES =============
    /**
     * Échappe les caractères HTML pour prévenir les XSS
     */
    function escapeHtml(unsafe) {
        if (typeof unsafe !== 'string') {
            return String(unsafe);
        }
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    /**
     * Nettoie les valeurs pour les attributs HTML
     */
    function sanitizeAttribute(value) {
        if (typeof value !== 'string') {
            return String(value);
        }
        // Supprimer les caractères dangereux dans les attributs
        return value.replace(/['"<>]/g, '');
    }

    /**
     * Valide que la valeur est un nombre entier positif
     */
    function sanitizeId(id) {
        const parsed = parseInt(id, 10);
        return (isNaN(parsed) || parsed <= 0) ? null : parsed;
    }

    /**
     * Nettoie les noms de fichiers pour éviter les injections
     */
    function sanitizeFilename(filename) {
        if (typeof filename !== 'string') {
            return null;
        }
        // Garder seulement les caractères alphanumériques, points, tirets et underscores
        const cleaned = filename.replace(/[^a-zA-Z0-9._-]/g, '_');
        // Éviter les noms vides ou dangereux
        if (!cleaned || cleaned === '--' || cleaned === '.' || cleaned === '..') {
            return null;
        }
        return cleaned;
    }

    // ============= CONFIGURATION =============
    const API_PT_PRIMARY = '/public/apiphenotypetype';
    const API_PT_FALLBACK = '/apiphenotypetype';
    const DOWNLOAD_URL = '/download-multiple';

    // ============= EXTENSION INPUT =============
    function ensureExtensionUI() {
        if (!$extInput.length) return;
        
        $extInput.attr('placeholder', '.csv, .tsv, .xlsx');
        
        if (!$('#extension-hint').length) {
            const $hint = $('<div></div>')
                .attr('id', 'extension-hint')
                .css({
                    'font-size': '.9em',
                    'color': '#555',
                    'margin-top': '.25rem'
                });
            
            $hint.text('Exemples : .csv, .tsv, .xlsx. Le point est ajouté automatiquement si oublié.');
            $hint.insertAfter($extInput);
        }
        
        $extInput.off('blur.ext').on('blur.ext', function () {
            let v = $(this).val().trim();
            // Nettoyer l'extension
            v = v.replace(/[^a-zA-Z0-9.]/g, '');
            if (v && !v.startsWith('.')) {
                v = '.' + v;
            }
            $(this).val(v);
        });
    }

    // ============= API PHENOTYPE TYPES =============
    let phenotypeTypesCache = null;

    async function fetchJsonWithFallback(urlPrimary, urlFallback) {
        try {
            const r1 = await fetch(urlPrimary, { 
                headers: { 'Accept': 'application/json' } 
            });
            if (r1.ok) return r1.json();
            if (r1.status !== 404) {
                throw new Error(`HTTP ${r1.status} ${r1.statusText}`);
            }
        } catch (e) {
            console.warn('[PT] Primary API failed, trying fallback:', e);
        }
        
        const r2 = await fetch(urlFallback, { 
            headers: { 'Accept': 'application/json' } 
        });
        if (!r2.ok) {
            throw new Error(`HTTP ${r2.status} ${r2.statusText}`);
        }
        return r2.json();
    }

    async function getPhenotypeTypes() {
        if (phenotypeTypesCache) {
            return phenotypeTypesCache;
        }
        try {
            const json = await fetchJsonWithFallback(API_PT_PRIMARY, API_PT_FALLBACK);
            phenotypeTypesCache = Array.isArray(json) ? json : [];
            return phenotypeTypesCache;
        } catch (e) {
            console.error('[PT] Failed to load phenotype types:', e);
            throw new Error('Impossible de charger les types de phénotype.');
        }
    }

    // ============= UI ERROR HANDLING =============
    function showError(msg) {
        let $err = $modalDialog.find('.download-error');
        if (!$err.length) {
            $err = $('<div class="download-error"></div>')
                .css({
                    color: '#C00',
                    padding: '0.5em 1em',
                    'margin-bottom': '1em',
                    'background-color': '#fdf2f2',
                    'border': '1px solid #fcc',
                    'border-radius': '4px'
                });
            $modalDialog.prepend($err);
        }
        console.error('[Download Error]:', msg);
        // Utiliser .text() au lieu de .html() pour éviter XSS
        $err.text(msg).show();
    }

    function hideError() {
        $modalDialog.find('.download-error').hide();
    }

    // ============= PHENOTYPE TYPES BLOCK =============
    const $ptBlock = $('<div></div>')
        .attr('id', 'phenotype-type-block')
        .css('display', 'none')
        .css('margin', '.5rem 0 0 2rem');
    
    const $ptTitle = $('<div></div>')
        .css({
            'font-weight': '600',
            'margin-bottom': '.35rem'
        })
        .text('Types de phénotype');
    
    const $ptList = $('<div></div>').attr('id', 'phenotype-type-list');
    
    const $ptHint = $('<div></div>')
        .css({
            'font-size': '.9em',
            'color': '#555',
            'margin-top': '.25rem'
        })
        .text('Sélectionnez les types de phénotype à inclure.');
    
    $ptBlock.append($ptTitle, $ptList, $ptHint);

    (function insertPtBlockUnderPhenotype() {
        const $anchor = $phenotypeOpt.closest('.form-check, .option, .form-group, label, div').first();
        if ($anchor.length) {
            $ptBlock.insertAfter($anchor);
        } else {
            ($modalBody.length ? $modalBody : $modalDialog).append($ptBlock);
        }
    })();

    /**
     * SÉCURISÉ : Utilisation de méthodes DOM sûres au lieu de template strings
     */
    function renderPhenotypeTypeCheckboxes(types) {
        const $ptList = $('#phenotype-type-list').empty();
        
        types.forEach(t => {
            // Valider et nettoyer l'ID
            const rawId = sanitizeId(t.id);
            if (!rawId) {
                console.warn('[PT] Invalid phenotype type ID:', t.id);
                return;
            }
            
            const id = `pt-${rawId}`;
            const label = escapeHtml(t.type ?? t.name ?? t.label ?? 'Inconnu');
            
            // Créer les éléments DOM de manière sécurisée
            const $label = $('<label></label>')
                .attr('for', id)
                .css({
                    'display': 'flex',
                    'gap': '.5rem',
                    'align-items': 'center',
                    'margin': '.25rem 0',
                    'cursor': 'pointer'
                });
            
            const $input = $('<input>')
                .attr('type', 'checkbox')
                .attr('id', id)
                .attr('name', 'phenotypeType[]')
                .attr('value', rawId)
                .prop('checked', true);
            
            const $span = $('<span></span>').text(label);
            
            $label.append($input, $span);
            $ptList.append($label);
        });
    }

    async function ensurePtBlockReadyIfNeeded() {
        const hasPhenotype = $phenotypeOpt.is(':checked') || $fileTypeAll.is(':checked');
        
        if (hasPhenotype) {
            $('#phenotype-type-block').show();
            if (!$('#phenotype-type-list').children().length) {
                try {
                    const types = await getPhenotypeTypes();
                    renderPhenotypeTypeCheckboxes(types);
                } catch (e) {
                    showError(e.message);
                }
            }
        } else {
            $('#phenotype-type-block').hide();
        }
    }

    // ============= FILE TYPE INTERACTIONS =============
    $fileTypeAll.on('change', () => {
        if ($fileTypeAll.is(':checked')) {
            $fileTypes.prop('checked', false);
        }
        ensurePtBlockReadyIfNeeded();
    });

    $fileTypes.on('change', function() {
        if ($(this).is(':checked')) {
            $fileTypeAll.prop('checked', false);
        }
        ensurePtBlockReadyIfNeeded();
    });

    // ============= MODAL CONTROLS =============
    $openBtn.on('click', () => {
        hideError();
        const selectedCount = $('input[name="selected_strain[]"]:checked')
            .filter(function() { return $(this).is(':visible'); })
            .length;
        
        if (selectedCount === 0) {
            alert('Veuillez sélectionner au moins une souche.');
            return;
        }
        
        ensurePtBlockReadyIfNeeded();
        ensureExtensionUI();
        $modal.css('display', 'flex');
    });

    $closeBtns.on('click', () => { 
        $modal.hide(); 
    });

    $modal.on('click', e => { 
        if (e.target === $modal[0]) { 
            $modal.hide(); 
        } 
    });

    // ============= SELECT ALL FUNCTIONALITY =============
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => {
            const visibleCheckboxes = Array.from(
                document.querySelectorAll('input.select-checkbox')
            ).filter(cb => cb.offsetParent !== null);
            
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
                    const visibleCheckboxes = Array.from(
                        document.querySelectorAll('input.select-checkbox')
                    ).filter(cb => cb.offsetParent !== null);
                    selectAllCheckbox.checked = visibleCheckboxes.every(chk => chk.checked);
                }
            });
        });
    }

    // ============= EXTRACT FILE INFO FROM DATA-INFO =============
    function extractFileFromDataInfo(dataInfo) {
        if (!dataInfo || typeof dataInfo !== 'string') return null;
        
        const match = dataInfo.match(/file\s*:\s*([^\n\r]+)/i);
        if (match && match[1]) {
            const filename = match[1].trim();
            return sanitizeFilename(filename);
        }
        return null;
    }

    // ============= EXTRACT FILE DATA FROM DOM =============
    function extractFileDataFromDOM(strainIds, types) {
        const fileEntries = [];
        const dedup = new Set();

        console.log('[Extract] Processing strain IDs:', strainIds);

        strainIds.forEach(strainId => {
            // Valider le strain ID
            const validStrainId = sanitizeId(strainId);
            if (!validStrainId) {
                console.warn(`[Extract] Invalid strain ID: ${strainId}`);
                return;
            }

            const $row = $(`#strain-${validStrainId}`);
            
            if (!$row.length || !$row.is(':visible')) {
                console.warn(`[Extract] Row not found or hidden for strain ${validStrainId}`);
                return;
            }

            if (types.includes('sequencing')) {
                extractSequencingFiles($row, validStrainId, fileEntries, dedup);
            }

            if (types.includes('drugs')) {
                extractDrugFiles($row, validStrainId, fileEntries, dedup);
            }
        });

        console.log('[Extract] Total files extracted:', fileEntries.length);
        return fileEntries;
    }

    function extractSequencingFiles($row, strainId, fileEntries, dedup) {
        const $seqCells = $row.find('.sequencing[data-file], .sub-cell.sequencing[data-file]');

        $seqCells.each(function() {
            const $cell = $(this);
            let filename = $cell.attr('data-file');
            
            if (!filename || filename === '--') {
                const dataInfo = $cell.attr('data-info');
                filename = extractFileFromDataInfo(dataInfo);
            }

            // Nettoyer le nom de fichier
            filename = sanitizeFilename(filename);
            if (!filename) {
                return;
            }

            const key = `${strainId}|sequencing|${filename}`;
            
            if (!dedup.has(key)) {
                dedup.add(key);
                fileEntries.push({
                    id: strainId,
                    type: 'sequencing',
                    name: filename,
                    downloadName: filename,
                    strainId: strainId
                });
                console.log(`[Extract] Added sequencing: ${filename}`);
            }
        });
    }

    function extractDrugFiles($row, strainId, fileEntries, dedup) {
        const $drugCells = $row.find('.drugResistanceOnStrain[data-file], .sub-cell.drugResistanceOnStrain[data-file]');

        $drugCells.each(function() {
            const $cell = $(this);
            let filename = $cell.attr('data-file');
            
            if (!filename || filename === '--') {
                const dataInfo = $cell.attr('data-info');
                filename = extractFileFromDataInfo(dataInfo);
            }

            // Nettoyer le nom de fichier
            filename = sanitizeFilename(filename);
            if (!filename) {
                return;
            }

            const key = `${strainId}|drugs|${filename}`;
            
            if (!dedup.has(key)) {
                dedup.add(key);
                fileEntries.push({
                    id: strainId,
                    type: 'drugs',
                    name: filename,
                    downloadName: filename,
                    strainId: strainId
                });
                console.log(`[Extract] Added drug file: ${filename}`);
            }
        });
    }

    // ============= DOWNLOAD HANDLER =============
    $confirmBtn.on('click', async () => {
        hideError();
        console.log('========== DOWNLOAD START ==========');

        try {
            // 1. Récupérer et valider les strain IDs
            let strainIds = $('input[name="selected_strain[]"]:checked')
                .filter(function() { return $(this).is(':visible'); })
                .map((_, el) => sanitizeId($(el).val()))
                .get()
                .filter(id => id !== null); // Supprimer les IDs invalides
            
            strainIds = [...new Set(strainIds)];

            if (!strainIds.length) {
                showError('Veuillez sélectionner au moins une souche valide.');
                return;
            }

            // 2. Récupérer et valider les types
            let types = $('input[name="fileType"]:checked')
                .map((_, el) => {
                    const val = $(el).val();
                    // Whitelist des types autorisés
                    if (['all', 'sequencing', 'phenotype', 'drugs', 'drugResistance'].includes(val)) {
                        return val;
                    }
                    return null;
                })
                .get()
                .filter(t => t !== null);
            
            types = types.map(t => (t === 'drugResistance' ? 'drugs' : t));

            if (!types.length) {
                showError('Veuillez sélectionner au moins un type de fichier.');
                return;
            }

            if (types.includes('all')) {
                types = ['sequencing', 'phenotype', 'drugs'];
            }

            // 3. Récupérer et valider les phenotype types
            let phenotypeTypeIds = [];
            if (types.includes('phenotype')) {
                phenotypeTypeIds = $('input[name="phenotypeType[]"]:checked')
                    .map((_, el) => sanitizeId($(el).val()))
                    .get()
                    .filter(id => id !== null);
            }

            // 4. Extraire les fichiers
            const fileEntries = extractFileDataFromDOM(strainIds, types);

            if (!fileEntries.length && !types.includes('phenotype')) {
                showError('Aucun fichier disponible pour votre sélection.');
                return;
            }

            // 5. Nettoyer l'extension
            let extension = $extInput.val().trim().replace(/[^a-zA-Z0-9.]/g, '');
            if (extension && !extension.startsWith('.')) {
                extension = '.' + extension;
            }

            // 6. Payload
            const payload = {
                strainIds,
                entries: fileEntries,
                extension,
                types,
                phenotypeTypeIds
            };

            console.log('[Download] Final payload:', payload);

            // 7. Requête
            $confirmBtn.prop('disabled', true).text('Téléchargement...');

            const res = await fetch(DOWNLOAD_URL, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' // Protection CSRF basique
                },
                body: JSON.stringify(payload),
            });

            if (!res.ok) {
                const errorText = await res.text();
                // Échapper le message d'erreur avant affichage
                showError('Erreur serveur : ' + errorText.substring(0, 200));
                return;
            }

            // 8. Téléchargement
            const blob = await res.blob();
            const cd = res.headers.get('Content-Disposition') || '';
            const m = /filename\*?=(?:UTF-8''|")?([^\";]+)/i.exec(cd);
            let filename = m ? decodeURIComponent(m[1]) : `export_${Date.now()}.zip`;
            
            // Nettoyer le nom de fichier
            filename = sanitizeFilename(filename) || `export_${Date.now()}.zip`;

            const downloadUrl = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(downloadUrl);

            $modal.hide();
            console.log('========== DOWNLOAD SUCCESS ==========');

        } catch (err) {
            console.error('[Download] Exception:', err);
            showError('Une erreur inattendue s\'est produite.');
        } finally {
            $confirmBtn.prop('disabled', false).text('Télécharger');
        }
    });
});