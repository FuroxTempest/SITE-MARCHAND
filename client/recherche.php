<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet">
    <!-- Ajout de l'icône Alizon à côté du nom de la page -->
    <link rel="icon" href="../images/favicon.ico" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


</head>
<body >    
    <?php
        //On inclut le header ainsi que la notification pour le feedback visuel
        include("./header.php");

        include("./notification.php");

        //On récupère l'url de page
        $page= "..$_SERVER[PHP_SELF]";
        //On récupère la recherche rentrée dans la barre de recherche puis on la met en miniscule
        $keyword=$_GET['keyword'];
        $keyword=strtolower($keyword);

        //Si le keyword n'est pas vide on l'encode en JSON pour le récuperer plus tard en JS
        if(!empty($keyword)){
            $keyword_json = json_encode($keyword);
        }

        //Pareil pour l'url de la page 
        $page_json = json_encode($page);

        ?>
        <!-- On a les div qui contiennent des data, c'est ce qui va nous permettrent aussi de récuperer ces valeurs en JS -->
        <div id="keyword-holder" data-keyword='<?php echo $keyword_json; ?>'></div>
        <div id="page-holder" data-page='<?php echo $page_json; ?>'></div>

        <?php

        //Si le mot clé n'est pas vide on enlève les espaces et on rentre dans la recherche
        if(!empty(trim($keyword))){

            //On décompose le mot pour que si l'utilisateur rentre plusieurs produits dans la barre de recherches on puisse tous les récupérer
            $word=explode(" ",trim($keyword));
                for($i=0; $i<count($word); $i++){
                    $kw[$i]="nomprod like '%".$word[$i]."%'";
                }

                // Ici c'est le cas de base sans mode ni prix
                $tab = array();

                // La requête pour récuperer les produits
                $prod=$bdd->prepare("SELECT id_produit, id_categorie, nomprod as nom, prix_ttc as prix_art, descriptif, stock from alizonbdd._produit where ".implode(" or ",$kw)." order by id_produit asc");            
                // on l'éxecute 
                $prod->setFetchMode(PDO::FETCH_ASSOC);
                $prod->execute();
                //Et on les met dans le tableau $tab
                foreach($prod as $row){
                    array_push($tab, $row);
                }  

                //On reccupère le prix max des produits puis on l'arrondie afin d'avoir une valeur logique.
                $prix_max = ceil(max(array_column($tab, "prix_art")));

                $vend = array();

                $vendeur=$bdd->prepare("SELECT id_vendeur, raison_sociale from alizonbdd._vendeur order by raison_sociale asc");
                // on l'éxecute 
                $vendeur->setFetchMode(PDO::FETCH_ASSOC);
                $vendeur->execute();
                //Et on les met dans le tableau $vend
                foreach($vendeur as $row){
                    array_push($vend, $row);
                }  
            
            $afficher="oui";
        }
    ?>    

    <!-- Div pour les erreurs (feedback visuel)-->
    <div id="erreur"></div>

    <!-- Section avec tout les résultats de la recherche ainsi que les filtres et les tris -->
    <section id="section_res_recherche">

        <!-- On affiche les résultats seuleument si l'utilisateur à rentré un mot dans la barre de recherche -->
        <?php if($afficher=="oui" && count($tab!=0)){ ?>
            <div class="nbr_res">
                <!-- On affiche ce que l'utilisateur à rentré dans la barre de recherche -->
                <h3>Votre recherche : "<?php echo $keyword ?>"</h3>

                <!-- Dropdown menu avec tous les modes -->
                <div class="select-tri-div">
                    <label for="select-tri">Trier par :</label>
                    <select id="select-tri">
                        <option value="default" selected>Valeur par défaut&nbsp;</option>
                        <option value="prix_croiss">Prix croissant&nbsp;</option>
                        <option value="prix_decroiss">Prix décroissant&nbsp;</option>
                        <option value="nouveau">Nouveautés&nbsp;</option>
                    </select>
                </div>
            </div>
            <?php include("./filtres_tel.php"); ?>

                <!-- On encode le tableau avec tout les produits pour le récupérer dans le JS après -->
                <!-- Pour améiliorer cette méthode il faudrait séparer cette partie et la faire dans un autre fichier (pour ne pas avoir de script dans la page) -->
                <?php $tab_json = json_encode($tab); ?>
                
                <?php
                //On inclut le fichier avec tous les filtres (prix et vendeur)
                include("./filtres.php");
             
                //On affiche chaque produit qui correspond à la recherche avec la page carte_produit   
                echo "<div id=\"carte_produit_recherche\" class=\"row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-3 g-4\" >";

                    foreach ($tab as $key) {
                        include("./carte_produit.php");
                    } 
                    
                ?>
                </div>
            </div>
        <?php }?> 
    </section>
    
    <!-- On include le footer -->
    <?php include("./footer.php"); ?>

    <script>
        //On récupère le tableau avec tout les produits
        var tab = <?php echo $tab_json ?>;
        $('#select-tri').on('change', function(){
            if(($('.form-check input[type=checkbox]:checked').val() != "" ) || ((min != undefined) || (max != undefined))){
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
                $.ajax({
                    url: 'requetes_filtres.php',
                    type: 'GET',
                    data: data,
                    success: function(data){
                        $('#carte_produit_recherche').html(data);
                    }
                });

            }else{
                tri = $(this).val();
        
                switch(tri){
                case "default":
                    tab.sort(function(a, b) {
                        return a.id_produit - b.id_produit;
                    });
                    break;
                case "prix_croiss":
                    tab.sort(function(a, b) {
                        return a.prix_art - b.prix_art;
                    });
                    break;
                case "prix_decroiss":
                    tab.sort(function(a, b) {
                        return b.prix_art - a.prix_art;
                    });
                    break;
                case "nouveau":
                    tab.sort(function(a, b) {
                        return b.id_produit - a.id_produit;
                    });
                    break;
                }
                $.ajax({
                    url: 'requetes_tri.php',
                    type: 'POST',
                    data:  {tab : tab},
                    success: function(data) {
                        if(data){
                            $('#carte_produit_recherche').html(data);
                        }
                    }
                });
            }
        });
    </script>
    
    <!-- Ici on a le script pour gérer le filtre des produits par le vendeur  -->
    <script src="../javascript/res_filtre.js"></script>
    
    <!-- Ici on a le script pour récupérer les valeurs du filtre prix et gérer les erreurs sur la version ordinateur -->
    <script src="../javascript/filtres.js"></script> 

    <!-- Ici on a le script pour récupérer les valeurs du filtre prix et gérer les erreurs sur la version téléphone -->
    <script src="../javascript/filtres_tel.js"></script>

</body>
    
