// Récupération des valeurs de l'input et de la barre de range minimale
const rangeMin = $('.min-price-range');
const valeurMin = $('.min-price-input');

// Récupération des valeurs de l'input et de la barre de range maximale
const rangeMax = $('.max-price-range');
const valeurMax = $('.max-price-input');

// Récupération de la div erreur pour afficher les erreurs
const errorDiv = $('#erreur');

// Modification de la div lorsqu'il y a un erreur dans la mise à jour du prix
const erreur_min = "<div id=\"notification_non\">La valeur minimale doit être inférieur à celle maximale </div>";
const erreur_max = "<div id=\"notification_non\">La valeur maximale doit être supérieur à celle minimale</div>"; 

// On ajoute un événement dès qu'il y un changement sur la barre du prix minimale
rangeMin.on("change", function() {
    // Si la valeur minimale dépasse celle maximale alors on affiche l'erreur et revient à la valeur du prix maximal
    if(parseFloat(rangeMin.val()) > parseFloat(rangeMax.val())) {
        errorDiv.html(erreur_min);
        rangeMin.val(rangeMax.val());
    }
    valeurMin.val(rangeMin.val()); // assignation de la valeur de rangeMin à valeurMin
});

// On ajoute un événement dès qu'il y un changement sur la barre du prix maximale
rangeMax.on("change", function() {
    // Si la valeur maximale est en dessous de celle minmale alors on affiche l'erreur et revient à la valeur du prix minimal
    if(parseFloat(rangeMax.val()) < parseFloat(rangeMin.val())) {
        errorDiv.html(erreur_max);
        rangeMax.val(rangeMin.val());
    }
    valeurMax.val(rangeMax.val()); // assignation de la valeur de rangeMax à valeurMax
});