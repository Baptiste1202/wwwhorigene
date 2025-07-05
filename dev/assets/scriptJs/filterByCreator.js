document.addEventListener('DOMContentLoaded', function () {
    const table = document.querySelector('#data-table');
    const btn = document.getElementById('filterByUserButton');

    if (!btn) {
        console.error("Bouton #filterByUserButton introuvable !");
        return;
    }
    if (!table) {
        console.error("Table #data-table introuvable !");
        return;
    }

    // Get the name from data attributes
    const userFirstname = btn.dataset.firstname;
    const userLastname = btn.dataset.lastname;
    const currentUserName = `${userFirstname} ${userLastname}`.trim();
    
    //console.log("Nom complet récupéré côté JS :", currentUserName);

    btn.dataset.active = "false";  // état initial
    btn.textContent = `🔍 Filter by: ${currentUserName}`;

    btn.addEventListener('click', function() {
        const active = btn.dataset.active === "true";

        if (!active) {
            //console.log(`Activation du filtre : affichage uniquement des lignes créées par ${currentUserName}.`);
            filterByCreator(true);
            btn.dataset.active = "true";
            btn.classList.add('active');
            btn.textContent = `Show all`;
        } else {
            //console.log("Désactivation du filtre : affichage de toutes les lignes.");
            filterByCreator(false);
            btn.dataset.active = "false";
            btn.classList.remove('active');
            btn.textContent = `🔍 Filter by : ${currentUserName}`;
        }
    });

    function filterByCreator(onlyWithCreator) {
        const headers = table.querySelectorAll('thead th');
        let creatorIndex = -1;
        headers.forEach((th, idx) => {
            if (th.classList.contains('creator')) {
                creatorIndex = idx;
            }
        });

        if (creatorIndex === -1) {
            console.error("Colonne 'Creator' introuvable !");
            return;
        }

        const rows = table.querySelectorAll('tbody tr');
        let countVisible = 0;
        rows.forEach(row => {
            const creatorCell = row.children[creatorIndex];
            if (!creatorCell) return;

            const text = creatorCell.textContent.trim();

            if (onlyWithCreator) {
                if (text !== '' && text !== '--') { // Assuming '--' is a placeholder for empty creator
                    row.style.display = '';
                    countVisible++;
                } else {
                    row.style.display = 'none';
                }
            } else {
                row.style.display = '';
                countVisible++;
            }
        });
        //console.log(`Nombre de lignes visibles : ${countVisible}`);
    }
});