// assets/scriptJS/formFloatingClear.js

document.addEventListener('DOMContentLoaded', function () {
    // ----- OUVERTURE du formulaire flottant -----
    const openFormBtn = document.getElementById('openFormBtn');
    const floatingForm = document.getElementById('floatingForm');

    if (openFormBtn && floatingForm) {
        openFormBtn.addEventListener('click', function(event) {
            floatingForm.style.display = 'block';
            floatingForm.style.left = `${event.pageX + 10}px`;
            floatingForm.style.top = `${event.pageY + 10}px`;
        });

        // Ferme le formulaire si clic en dehors
        document.addEventListener('click', function(event) {
            if (!floatingForm.contains(event.target) && event.target.id !== 'openFormBtn') {
                floatingForm.style.display = 'none';
            }
        });
    }

    // ----- BOUTON CLEAR -----
    const btnClear = document.getElementById('btn-clear');
    if (btnClear && floatingForm) {
        btnClear.addEventListener('click', function(e) {
            e.preventDefault();

            // Le formulaire dans la popin flottante
            const form = floatingForm.querySelector('form');
            if (!form) return;

            // Reset natif (pour tous les input, textarea, select de base)
            form.reset();

            // Forcer le reset des champs Autocomplete TomSelect/Symfony UX
            form.querySelectorAll('select[data-controller*="autocomplete"]').forEach(function(select) {
                if (select.tomselect) {
                    select.tomselect.clear();
                } else {
                    select.value = '';
                }
            });

            // Forcer "placeholder" (option vide) pour Résistance
            const resistance = form.querySelector('select[name="resistant"]');
            if (resistance) {
                resistance.value = '';
                if (resistance.tomselect) {
                    resistance.tomselect.setValue('');
                }
                // Pour Select2 (optionnel)
                // if (window.$) $(resistance).val('').trigger('change');
            }
        });
    }
});
