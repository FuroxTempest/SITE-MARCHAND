<?php
session_start();
include('./connect_params.php');
try{
    $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
    [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
}catch (PDOException $e){
    print "Erreur ! : " . $e->getMessage() . "<br/>";
    die();
}
  $page= "..$_SERVER[PHP_SELF]";
  $idcat = $_GET['idcategorie'];
  $categorie = array();
  foreach($bdd->query("SELECT _categorie.id_cat,_categorie.nom from alizonbdd._categorie where _categorie.id_cat=$idcat") as $row) {
    array_push($categorie,$row);
  }
  $idcat_json = json_encode($idcat);
  $page_json = json_encode($page);


  ?>
  <div id="idcat-holder" data-id='<?php echo $idcat_json; ?>'></div>
  <div id="page-holder" data-page='<?php echo $page_json; ?>'></div>
  <?php
  
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/favicon.ico" />    

    <?php echo"<title>". ucfirst(strtolower($categorie[0]['nom']))." | ALIZON</title>";?>


</head>
<body onload="restoreScrollPos()">   

  <?php
  include("header.php");
  include("./notification.php");
  
    $tab = array();
    foreach($bdd->query("SELECT _produit.stock,_produit.id_produit,_produit.id_categorie,_produit.nomprod as nom,_produit.prix_ttc as prix_art ,_produit.descriptif from alizonbdd._produit where  _produit.id_categorie = $idcat ;") as $row) {
        array_push($tab,$row);
    }


  //On reccupère le prix max des produits puis on l'arrondie à l'unité au dessus afin d'avoir une valeur logique.
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
        
  ?>

    <div class="titre" style="margin: 30px;">
        <?php
        $search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
        $replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'É', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'È', 'É', 'Ê', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');
        $MaChaine = strtoupper($categorie[0]['nom']);
        $MaChaine = str_replace($search, $replace, $MaChaine);
        echo "<h1 class=\"text-center\" style=\"font-size: 50px;\">$MaChaine</h1>";
        ?>
    </div>
    <div id="erreur"></div>
    <!-- Section avec tout les résultats de la recherche ainsi que les filtres et les tris -->
    <section id="section_res_recherche">
        <div class="nbr_res">
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
    </section>


    <?php
    include("./filtres_tel.php");
    include("footer.php");
    ?>


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
    <script>
    function setScroll() {
        let scroll = window.scrollY;
        let scrollString = scroll.toString();
        localStorage.setItem("scrollPosition", scrollString);
    }

    function restoreScrollPos() {
        let posYString = localStorage.getItem("scrollPosition");
        let posY = parseInt(posYString);
        window.scroll(0, posY);
        localStorage.clear();

        return true;
    }
    </script>
</body>
</html>
