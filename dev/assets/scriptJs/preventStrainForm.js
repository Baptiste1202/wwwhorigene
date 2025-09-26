document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("form");
    const specieInput = document.getElementById("strain_form_specie");
    const genderInput = document.getElementById("strain_form_gender");
    const nameInput = document.getElementById("strain_form_nameStrain");

    form.addEventListener("submit", function (event) {
        
        // Réinitialise les messages de validité
        specieInput.setCustomValidity("");
        genderInput.setCustomValidity("");
        nameInput.setCustomValidity("");

        let hasError = false;

        // Vérif Name
        if (nameInput.value.trim() === "") {
            nameInput.value = "";
            nameInput.setCustomValidity("Name must not be empty");
            nameInput.reportValidity();
            hasError = true;
        }

        // Vérif Specie
        if (specieInput.value.trim() === "") {
            specieInput.value = "";
            specieInput.setCustomValidity("Specie must not be empty");
            specieInput.reportValidity();
            hasError = true;
        }

        // Vérif Gender
        if (genderInput.value.trim() === "") {
            genderInput.value = "";
            genderInput.setCustomValidity("Gender must not be empty!");
            genderInput.reportValidity();
            hasError = true;
        }

        // Si erreur, on bloque l'envoi
        if (hasError) {
            event.preventDefault();
        }
        // Sinon le formulaire s'envoie normalement
    });

    // Retire la custom validity dès que l'utilisateur tape
    [specieInput, genderInput, nameInput].forEach(input => {
        input.addEventListener("input", function () {
            input.setCustomValidity("");
        });
    });
});
