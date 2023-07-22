//Récupération des valeurs de l'input et de la barre de range minimale
const rangeMinTel = document.querySelector('.min-price-range-tel');
const valeurMinTel = document.querySelector('.min-price-input-tel');

//Récupération des valeurs de l'input et de la barre de range maximale
const rangeMaxTel = document.querySelector('.max-price-range-tel');
const valeurMaxTel = document.querySelector('.max-price-input-tel');

//On ajout un évenement dès qu'il y un changement sur la la barre du prix minimale
rangeMinTel.addEventListener("change", function() {
    
    //Si la valeur minimale dépasse celle maximale alors on affiche l'erreur et revient à la valeur du prix maximal
    if((rangeMinTel.value>rangeMaxTel.value) && rangeMaxTel.value!=40){//on met le 100 car il y a un bug au début sinon
        errorDiv.innerHTML = erreur_min;
        rangeMinTel.value=rangeMaxTel.value;
    }

    valeurMinTel.value = rangeMinTel.value;// assignation de la valeur de rangeMinTel à valeurMinTel
});

//On ajout un évenement dès qu'il y un changement sur la la barre du prix maximale
rangeMaxTel.addEventListener("change", function() {

    //Si la valeur maximale est en dessous de celle minmale alors on affiche l'erreur et revient à la valeur du prix minimal
    if((rangeMaxTel.value<rangeMinTel.value)){
        errorDiv.innerHTML = erreur_max;
        rangeMaxTel.value=rangeMinTel.value;
    }

    valeurMaxTel.value = rangeMaxTel.value; // assignation de la valeur de rangeMaxTel à valeurMaxTel
});