<?php 

    //On récupère les paramètres de la bdd
   require_once('./connect_params.php');

    //On se connecte à la bdd
    try{
        $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
        [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
        );
        }catch (PDOException $e){
            print "Erreur ! : " . $e->getMessage() . "<br/>";
            die();
        }

    //On test si l'utilisateur recherche bien un produit
    if(isset($_GET['produit'])){
        //On enlève les espaces de la recherche et on la met dans la variable $user en la convertissant en String
        $user = (String) trim($_GET['produit']);

        //On fait la requête pour récuperer les produits qui contiennent la chaîne $user
        $produit=$bdd->prepare("SELECT id_produit, id_categorie, nomprod as nom, prix_ttc as prix_art, descriptif, stock 
        FROM alizonbdd._produit 
        WHERE nomprod LIKE '%".$user."%' LIMIT 8");            
        $produit->execute();
        
        //Et On affiche un lien qui redirige vers le détail du produit pour chaque résultat
        foreach($produit as $prod){
            //On met une majuscule au nom du produit (Car on met un strtolower lors de l'ajout des produits dans la bdd)
            $prod['nom']=ucfirst($prod['nom']);
            ?>
            
            <div id="div_rech_res" class="border-bottom border-2 m-3">
                <?php
                        echo "<a id=\"lien_rech_res\" class=\"text-black text-start m-2 p-2\" href=\"./detail_produit.php?idprod=".$prod['id_produit']."\">".$prod['nom']."</a>";
                ?>
           </div>
        <?php
        }
    }

?>