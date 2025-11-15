// assets/scriptJs/downloadFile.js
$(document).ready(function () {
    const $modal        = $('#downloadModal');
    const $modalDialog  = $modal.find('.modal-dialog');
    const $modalBody    = $modal.find('.modal-body');
    const $openBtn      = $('#multi-download-btn');
    const $closeBtns    = $('#modal-close, #modal-cancel');
    const $confirmBtn   = $('#confirm-download-btn');
    const $fileTypeAll  = $('input[name="fileType"][value="all"]');
    const $fileTypes    = $('input[name="fileType"]').not('[value="all"]');
    const $phenotypeOpt = $('input[name="fileType"][value="phenotype"]');
    
    // >>> AJOUT : gestion du champ d'extension <<<
    const $extInput = $('#extension-input');
    function ensureExtensionUI() {
        if (!$extInput.length) return;
        // Placeholder de suggestion
        $extInput.attr('placeholder', '.csv, .tsv, .xlsx');
        // Petit hint sous le champ, si absent
        if (!$('#extension-hint').length) {
            $('<div id="extension-hint" style="font-size:.9em;color:#555;margin-top:.25rem;">' +
              'Exemples&nbsp;: <code>.csv</code>, <code>.tsv</code>, <code>.xlsx</code>. ' +
              'The dot is added automatically if you forget it.' +
              '</div>').insertAfter($extInput);
        }
        // Auto-préfixer "." si oublié
        $extInput.off('blur.ext').on('blur.ext', function () {
            const v = $(this).val().trim();
            if (v && !v.startsWith('.')) $(this).val('.' + v);
        });
    }
    // <<< FIN AJOUT

    // --- API to load phenotype types (without Twig) ---
    const API_PT_PRIMARY   = '/public/apiphenotypetype';
    const API_PT_FALLBACK  = '/apiphenotypetype';

    async function fetchJsonWithFallback(urlPrimary, urlFallback) {
        // //console.log('[PT] fetchJsonWithFallback -> try primary:', urlPrimary);
        try {
            const r1 = await fetch(urlPrimary, { headers: { 'Accept': 'application/json' } });
            // //console.log('[PT] primary status:', r1.status);
            if (r1.ok) return r1.json();
            if (r1.status !== 404) {
                const t = await r1.text().catch(()=> '');
                throw new Error(`HTTP ${r1.status} ${r1.statusText} - ${t}`);
            }
        } catch (e) {
            // //console.warn('[PT] primary failed, will try fallback. Reason:', e);
        }
        // //console.log('[PT] trying fallback:', urlFallback);
        const r2 = await fetch(urlFallback, { headers: { 'Accept': 'application/json' } });
        // //console.log('[PT] fallback status:', r2.status);
        if (r2.ok) return r2.json();
        const t = await r2.text().catch(()=> '');
        throw new Error(`HTTP ${r2.status} ${r2.statusText} - ${t}`);
    }

    // --- Error display in modal ---
    function showError(msg) {
        let $err = $modalDialog.find('.download-error');
        if (!$err.length) {
            $err = $('<div class="download-error"></div>')
                .css({
                    color: '#C00',
                    padding: '0.5em 1em',
                    'margin-bottom': '1em',
                    'background-color': '#fdf2f2',
                    'border-radius': '4px'
                });
            $modalDialog.prepend($err);
        }
        // //console.error('[Download][UI-Error]:', msg);
        $err.text(msg).show();
    }
    function hideError() {
        $modalDialog.find('.download-error').hide();
    }

    // === Phenotype types block (UI + data) ===
    let phenotypeTypesCache = null;

    // UI block inserted just under the phenotype option (no need to modify existing HTML)
    const $ptBlock = $(`
      <div id="phenotype-type-block" style="display:none; margin:.5rem 0 0 2rem;">
        <div style="font-weight:600; margin-bottom:.35rem;">Phenotype types</div>
        <div id="phenotype-type-list"></div>
        <div style="font-size:.9em; color:#555; margin-top:.25rem;">
          Tick the phenotype types to include.
        </div>
      </div>
    `);
    (function insertPtBlockUnderPhenotype() {
        const $anchor = $phenotypeOpt.closest('.form-check, .option, .form-group, label, div').first();
        if ($anchor.length) {
            // //console.log('[PT] Insert block after phenotype option anchor');
            $ptBlock.insertAfter($anchor);
        } else {
            // //console.log('[PT] Anchor not found, appending block to modal');
            ($modalBody.length ? $modalBody : $modalDialog).append($ptBlock);
        }
    })();

    async function getPhenotypeTypes() {
        if (phenotypeTypesCache) {
            // //console.log('[PT] using cached phenotype types:', phenotypeTypesCache);
            return phenotypeTypesCache;
        }
        let json;
        try {
            json = await fetchJsonWithFallback(API_PT_PRIMARY, API_PT_FALLBACK);
        } catch (e) {
            // //console.error('[PT] load error:', e);
            throw new Error('Unable to load phenotype types.');
        }
        phenotypeTypesCache = Array.isArray(json) ? json : [];
        // //console.log('[PT] loaded phenotype types:', phenotypeTypesCache);
        return phenotypeTypesCache;
    }

    function renderPhenotypeTypeCheckboxes(types) {
        const $ptList = $('#phenotype-type-list').empty();
        // //console.log('[PT] rendering phenotype type checkboxes, count =', types.length);
        types.forEach(t => {
            const id = `pt-${t.id}`;
            const label = (t.type ?? t.name ?? t.label ?? '').toString();
            $ptList.append(`
              <label for="${id}" style="display:flex; gap:.5rem; align-items:center; margin:.25rem 0;">
                <input type="checkbox" id="${id}" name="phenotypeType[]" value="${t.id}">
                <span>${label}</span>
              </label>
            `);
        });
    }

    async function ensurePtBlockReadyIfNeeded() {
        const hasPhenotype = $phenotypeOpt.is(':checked') || $fileTypeAll.is(':checked');
        // //console.log('[PT] ensurePtBlockReadyIfNeeded -> hasPhenotype?', hasPhenotype);
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

    // --- Interactions between “all” and other types ---
    $fileTypeAll.on('change', () => {
        // //console.log('[UI] changed -> ALL checked?', $fileTypeAll.is(':checked'));
        if ($fileTypeAll.is(':checked')) {
            $fileTypes.prop('checked', false);
        }
        ensurePtBlockReadyIfNeeded();
    });
    $fileTypes.on('change', function() {
        // //console.log('[UI] fileType changed:', this.value, 'checked?', $(this).is(':checked'));
        if ($(this).is(':checked')) {
            $fileTypeAll.prop('checked', false);
        }
        ensurePtBlockReadyIfNeeded();
    });

    // 1) Open modal
    $openBtn.on('click', () => {
        hideError();
        const selectedCount = $('input[name="selected_strain[]"]:checked')
            .filter(function(){ return $(this).is(':visible'); }).length;
        // //console.log('[OpenModal] selected visible strains count =', selectedCount);
        if (selectedCount === 0) {
            alert('Please select at least one strain first.');
            return;
        }
        ensurePtBlockReadyIfNeeded();
        ensureExtensionUI();
        $modal.css('display', 'flex');
        // //console.log('[OpenModal] modal opened');
    });

    // 2) Close modal
    $closeBtns.on('click', () => { 
        // //console.log('[Modal] close clicked');
        $modal.hide(); 
    });

    // 3) Close when clicking outside the dialog
    $modal.on('click', e => { 
        if (e.target === $modal[0]) { 
            // //console.log('[Modal] click outside -> hide'); 
            $modal.hide(); 
        } 
    });

    // --- Select All that only checks visible checkboxes ---
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => {
            const visibleCheckboxes = Array.from(document.querySelectorAll('input.select-checkbox'))
                .filter(cb => cb.offsetParent !== null);
            visibleCheckboxes.forEach(cb => { cb.checked = selectAllCheckbox.checked; });
            // //console.log('[SelectAll] master:', selectAllCheckbox.checked, '-> visible children count:', visibleCheckboxes.length);
        });
        const childCheckboxes = document.querySelectorAll('input.select-checkbox');
        childCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                if (!cb.checked) selectAllCheckbox.checked = false;
                else {
                    const visibleCheckboxes = Array.from(document.querySelectorAll('input.select-checkbox'))
                        .filter(cb => cb.offsetParent !== null);
                    selectAllCheckbox.checked = visibleCheckboxes.every(chk => chk.checked);
                }
                // //console.log('[SelectAll] child changed -> master now:', selectAllCheckbox.checked);
            });
        });
    }

    // 4) "Download" button handler
    $confirmBtn.on('click', async () => {
        hideError();
        // //console.log('================= [Download] START =================');

        // 4.1) Checked strain IDs (only visible checkboxes)
        let strainIds = $('input[name="selected_strain[]"]:checked')
            .filter(function(){ return $(this).is(':visible'); })
            .map((_, el) => el.value).get();
        strainIds = [...new Set(strainIds)];
        // //console.log('[Download] strainIds (visible, unique) =', strainIds);
        if (!strainIds.length) {
            showError('Please select at least one strain.');
            // //console.log('================= [Download] ABORT: no strains =================');
            return;
        }

        // 4.2) Checked types
        let types = $('input[name="fileType"]:checked').map((_, el) => el.value).get();
        // //console.log('[Download] file types selected (raw) =', types);

        // normalization: map 'drugResistance' -> 'drugs' (without changing HTML)
        types = types.map(t => (t === 'drugResistance' ? 'drugs' : t));

        if (!types.length) { 
            showError('Please select at least one file type.'); 
            // //console.log('================= [Download] ABORT: no types =================');
            return; 
        }
        if (types.includes('all')) {
            types = ['sequencing','phenotype','drugs'];
        }
        // //console.log('[Download] file types after normalize/expand =', types);

        // 4.3) If phenotype requested → IDs of checked phenotype types (for the server)
        let phenotypeTypeIds = [];
        if (types.includes('phenotype')) {
            phenotypeTypeIds = $('input[name="phenotypeType[]"]:checked')
                .map((_, el) => parseInt(el.value, 10)).get();
        }
        // //console.log('[Download] phenotypeTypeIds =', phenotypeTypeIds);

        // 4.3 bis) Build fileEntries for sequencing/drugs ONLY
        const fileEntries = [];
        const dedup = new Set(); // dedup by "id|type|name"

        strainIds.forEach(strainId => {
            const $rows = $('#data-table tbody tr').filter(function () {
                return $(this).find('td.id').text().trim() === strainId && $(this).is(':visible');
            });
            // //console.log(`[Download] rows for strain ${strainId} =`, $rows.length);

            $rows.each(function (idx) {
                const $row = $(this);

                // sequencing
                if (types.includes('sequencing')) {
                    const $cellsS = $row.find('td.sequencing [data-file]');
                    $cellsS.each(function () {
                        const fnS = $(this).data('file');
                        if (fnS && fnS !== '--') {
                            const key = `${strainId}|sequencing|${fnS}`;
                            if (!dedup.has(key)) {
                                dedup.add(key);
                                fileEntries.push({
                                    id: strainId,
                                    type: 'sequencing',
                                    name: fnS,
                                    downloadName: fnS
                                });
                            }
                        }
                    });
                }

                if (types.includes('drugs')) {
                    $row.find('td.drugResistanceOnStrain [data-file]').each(function() {
                        const fnD = $(this).data('file');
                        if (fnD && fnD !== '--') {
                            const key = `${strainId}|drugs|${fnD}`;
                            if (!dedup.has(key)) {
                                dedup.add(key);
                                fileEntries.push({ id: strainId, type: 'drugs', name: fnD, downloadName: fnD });
                            }
                        }
                    });
                }
            });
        }
        );


        if (!fileEntries.length && !types.includes('phenotype')) {
            showError('No file available for your selection.');
            // //console.log('================= [Download] ABORT: no entries and no phenotype =================');
            return;
        }

        // 4.4) Prepare payload
        const extension = $('#extension-input').val().trim();
        const payload   = {
            strainIds,
            entries: fileEntries,
            extension,
            types,
            phenotypeTypeIds
        };
        // //console.log('[Download] Final payload =', payload);

        // 4.5) Send via fetch
        const url = '/download-multiple';
        // //console.log('[Download] POST =>', url);
        try {
            const res = await fetch(url, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify(payload),
            });

            // //console.log('[Download] Response status =', res.status);
            if (!res.ok) {
                const txt = await res.text();
                // //console.error('[Download] Server error response:', txt);
                showError('Server error: ' + txt);
                // //console.log('================= [Download] END (server error) =================');
                return;
            }

            const blob = await res.blob();
            const cd = res.headers.get('Content-Disposition') || '';
            const m  = /filename\*?=(?:UTF-8''|")?([^\";]+)/i.exec(cd);
            const filename = m ? decodeURIComponent(m[1]) : `files_${Date.now()}.zip`;
            // //console.log('[Download] Content-Disposition:', cd, '-> filename =', filename);

            const downloadUrl = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href     = downloadUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(downloadUrl);
            $modal.hide();
            // //console.log('================= [Download] END (success) =================');

        } catch (err) {
            // //console.error('[Download] Fetch exception:', err);
            showError('An unexpected error occurred: ' + err.message);
            // //console.log('================= [Download] END (exception) =================');
        }
    });
});
