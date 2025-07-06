$(document).ready(function() {
    var table = $('#data-table').DataTable({
        paging: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: ajaxUrlStrains, // Utilise la variable définie dans le HTML
            type: 'GET',
            dataSrc: 'data',
            // *** C'est la partie CRUCIALE pour passer les filtres du formulaire ***
            data: function (d) {
                // Récupère les valeurs de chaque champ du formulaire flottant
                // Assurez-vous que les IDs ou noms correspondent à ceux générés par Twig
                d.search_query = $('#search_form_query').val(); // C'est le champ "query" de votre form
                d.search_plasmyd = $('#search_form_plasmyd').val();
                d.search_drug = $('#search_form_drug').val();
                d.search_genotype = $('#search_form_genotype').val();
                d.search_project = document.querySelector('#project').value;
    console.log("search_project here:", d.search_project);
                d.search_sample = $('#search_form_sample').val();
                d.search_user = $('#search_form_user').val();
                d.search_specie = $('#search_form_specie').val();
                d.search_gender = $('#search_form_gender').val();
                d.search_sequencing = $('#search_form_sequencing').val();
                d.search_resistant = $('#search_form_resistant').val(); // Peut être null, true, false

                return d;
            }
        },
        columns: [
            { data: 0, orderable: false, searchable: false }, // Checkbox
            { data: 1, name: 'id' },
            { data: 2, name: 'name' },
            { data: 3, name: 'date' },
            { data: 4, name: 'specie' },
            { data: 5, name: 'gender' },
            { data: 6, orderable: false, searchable: true }, // Sample
            { data: 7, orderable: false, searchable: true }, // Genotype
            { data: 8, orderable: false, searchable: true }, // Plasmyd
            { data: 9, orderable: false, searchable: true }, // Storage
            { data: 10, name: 'parent' },
            { data: 11, orderable: false, searchable: true }, // Project
            { data: 12, orderable: false, searchable: true }, // Collection
            { data: 13, orderable: false, searchable: true }, // Drug Resistance
            { data: 14, orderable: false, searchable: true }, // Transformability
            { data: 15, orderable: false, searchable: true }, // File Sequencing
            { data: 16, orderable: false, searchable: true }, // Publication
            { data: 17, name: 'creator' },
            { data: 18, orderable: false, searchable: true }, // Description
            { data: 19, orderable: false, searchable: true }, // Comment
            { data: 20, orderable: false, searchable: false },  // Dupp
            { data: 21, orderable: false, searchable: false }, // modif
            { data: 22, orderable: false, searchable: false }  // supp
        ],
        order: [[1, 'desc']],
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All']
        ],
        dom: 'lfrtip', // 'B' (pour les boutons) est supprimé ici
        rowGroup: {
            dataSrc: 1,
            startRender: function (rows, group) {
                const firstRow = rows.data()[0];
                const checkbox = firstRow[0];
                const id = firstRow[1];
                const name = firstRow[2];

                return checkbox + ' ID : ' + id + ' - Souche : ' + name;
            }
        },
        language: {
            url: "https://cdn.datatables.net/plug-ins/2.0.8/i18n/en-GB.json"
        },
        initComplete: function () {
            // Affiche la table quand elle est prête
            document.getElementById('data-table').style.visibility = 'visible';
        }
    });

    // Gestion des cases à cocher (Tout sélectionner / Désélectionner)
    $('#select-all').on('click', function(){
        var isChecked = this.checked;
        $('.select-checkbox').each(function(){
            this.checked = isChecked;
        });
    });

    $('#data-table tbody').on('change', '.select-checkbox', function(){
        if(!this.checked){
            $('#select-all').prop('checked', false);
        } else {
            var allChecked = true;
            $('.select-checkbox').each(function(){
                if(!this.checked){
                    allChecked = false;
                    return false;
                }
            });
            $('#select-all').prop('checked', allChecked);
        }
    });

    // Exemple: Récupérer les IDs des souches sélectionnées (pour un bouton d'action externe si tu en as un)
    $('#your-action-button-id').on('click', function() {
        var selectedIds = [];
        $('.select-checkbox:checked').each(function(){
            selectedIds.push($(this).val());
        });
        console.log("Selected Strain IDs:", selectedIds);
    });
});
