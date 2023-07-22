//On vérifie que le document soit bien prêt à recevoir des instructions
$(document).ready(function(){

    $('.min-price-range, .max-price-range, .form-check input[type=checkbox]').on('change', function(){

        //Méthode pour que l'utilisateur ne selectionne qu'un seul vendeur dans le filtre
        if (this.checked) {
            $('.form_filtre_vendeur input[type=checkbox]').not(this).prop('checked', false);
        }

        // On récupère les valeurs des barres de prix et les cases cochées
        const min = $('.min-price-range').val();
        const max = $('.max-price-range').val();
        let vendeur = $('.form-check input[type=checkbox]:checked').val();
        let tri = $('#select-tri').val();

        // On récupère le nom de la page pour différencier la page recherche de catégorie
        let currentPage = location.pathname;

        // On crée un objet avec toutes les variables à passer pour les requêtes
        let data= {};
        let page_holder = document.getElementById('page-holder');
        let page = JSON.parse(page_holder.getAttribute('data-page'));
        
        if (currentPage === '/php/categorie.php'){
            let idcat_holder = document.getElementById('idcat-holder');
            let idcat = JSON.parse(idcat_holder.getAttribute('data-id'));
            data = {min: min, max: max, vendeur: vendeur,tri : tri, idcat: idcat, page : page};
        }
        else if(currentPage === '/php/recherche.php'){
            let keyword_holder = document.getElementById('keyword-holder');
            let keyword = JSON.parse(keyword_holder.getAttribute('data-keyword'));
            data = {min: min, max: max, vendeur: vendeur,tri : tri, keyword: keyword, page : page};
        }
        
        // On effectue la requête AJAX pour récupérer les produits filtrés
        $.ajax({
            type: 'GET',
            url: './requetes_filtres.php',
            data: data,
            success: function(data) {
                if (data == 0) {
                    $('#carte_produit_recherche').html("<div class='text-center text-black m-auto' style='font-size: 30px;'>Aucun produits trouvés</div>");
                } else{
                    $('#carte_produit_recherche').html(data);
                }
            }
        });
        
    });
});
