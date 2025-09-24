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

            const form = floatingForm.querySelector('form');
            if (!form) return;

            // Reset natif
            form.reset();

            // Clear tous les select (TomSelect ou natifs)
            form.querySelectorAll('select').forEach(select => {
                if (select.tomselect) {
                    select.tomselect.clear();
                } else {
                    select.value = '';
                }
            });

            // Clear tous les champs autocomplete Symfony UX
            form.querySelectorAll('input[data-controller*="autocomplete"]').forEach(input => {
                input.value = '';
            });

            // Clear tous les champs text/textarea
            form.querySelectorAll('input[type="text"], input[type="search"], textarea').forEach(input => {
                input.value = '';
            });

            // Forcer le trigger 'change' pour que les composants JS réagissent
            form.querySelectorAll('select, input, textarea').forEach(el => {
                el.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    }
});
