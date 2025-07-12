// assets/scriptJs/toggleColumns.js

document.addEventListener('DOMContentLoaded', function() {
    const toggleAll = document.getElementById('toggle-all-columns');
    console.log('toggleColumns.js chargé !');


    function setAllCheckboxes(state) {
        document.querySelectorAll('.toggle-column').forEach(cb => {
            if (cb.checked !== state) {
                cb.checked = state;
                cb.dispatchEvent(new Event('change'));
            }
        });
    }

    if (!toggleAll) return; // Sécurité

    // Clic sur "Toutes les colonnes" => coche/décoche toutes
    toggleAll.addEventListener('change', function() {
        setAllCheckboxes(toggleAll.checked);
    });

    // Synchronise la case "Toutes les colonnes" selon l'état de chaque colonne
    document.querySelectorAll('.toggle-column').forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = Array.from(document.querySelectorAll('.toggle-column')).every(c => c.checked);
            toggleAll.checked = allChecked;
        });
    });
});
