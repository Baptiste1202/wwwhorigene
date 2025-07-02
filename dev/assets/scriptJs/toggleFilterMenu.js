document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.getElementById('menu-button');
    const filterMenu = document.getElementById('filter-menu');

    // Bascule la visibilité du menu au clic sur le bouton
    menuButton.addEventListener('click', function() {
        filterMenu.classList.toggle('visible');
    });

    // Ferme le menu lorsque l'utilisateur clique en dehors du bouton ou du menu
    document.addEventListener('click', function(event) {
        if (!menuButton.contains(event.target) && !filterMenu.contains(event.target)) {
            filterMenu.classList.remove('visible');
        }
    });
});