//On vérifie que le document soit bien prêt à reçevoir des instructions
$(document).ready(function(){
    //Ici à chaque fois que l'on entre une valeur au clavier on entre dans la fonction
    ($('#search').keyup(function(){
        //On met à jour l'affichage des résutaltats pour n'avoir que ceux qui correspondent à la dernière entrée
        $('#resultat_dynamique').html('');
        //On met le mot clé dans une variable, puis on le met en miniscule
        var produit = $(this).val();
        produit = produit.toLowerCase();

        //On met en place un timer pour ne pas faire une requête à chaque entrée au clavier (trop lourd, plus cause de nombreux bug d'affichage).
        //Ici on clear d'abord le timer à chaque frappe 
        clearTimeout(window.myTimeout);
        //Puis on en crée un nouveau 
        window.myTimeout = setTimeout(function() {
        //Et donc toute les 0.5 secondes on appel l'Ajax pour afficher les produits qui correspondent à la recherche
            if(produit != ""){
                $.ajax({
                    type: 'GET',
                    //On fait appel à recherche_produit qui fait la requête permettant d'avoir les produits qui correspondent
                    url: './recherche_produit.php',
                    data: 'produit=' + encodeURIComponent(produit), 
                    success: function(data){
                        console.log(data);
                        if(data == 0){
                            $('#resultat_dynamique').html("<div class='text-start text-black m-3'>Aucun produits trouvés</div>");
                        }
                        else{
                            //On affiche les résultats s'il y a au moins un résultat
                            $('#resultat_dynamique').append(data);
                        }
                    }
                })      
            }  
        }, 500  );                 
    })); 
});
