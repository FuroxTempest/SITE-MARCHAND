<?php
    //On inclut les paramètres de la bdd
    include_once('./connect_params.php');
    try{
        $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
        [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
        );
    }catch (PDOException $e){
        print "Erreur ! : " . $e->getMessage() . "<br/>";
        die();
    }

    // Récupération des valeurs du prix minimum et maximum à partir des champs de formulaire
    $minPrice = ($_GET['min']);
    $maxPrice = ($_GET['max']);

    //Récupération d'id de catégorie de la catégorie
    $idcat = $_GET['idcat'];

    //On récupère mot rentré dans la barre de recherche 
    $keyword = $_GET['keyword'];
    
    //On récupere aussi l'url de la page
    $page = $_GET['page'];

    //On récupère l'id du vendeur
    $vendeur = $_GET['vendeur'];

    $tri = $_GET['tri'];
    
    // On fait un switch pour gérer tout les cas possibles de tris.
    // On distingue deux parties, une pour la page recherche avec le keyword qui est défini (ce sont les 3 premiers cas)
    // Et une autres pour la page catégorie ou on remplcae le keyword par l'idcat
    switch (true) {
        //Première partie pour la page recherche
        // Cas ou tout filtres sont activés et que l'on est sur la page recherche :
        case (isset($keyword) && (isset($vendeur)) && (isset($minPrice))):
            // Exécution de la requête SQL pour sélectionner les produits avec un prix compris entre les valeurs de prix minimum et maximum et avec un vendeur
            $produit_filtres = $bdd->prepare("SELECT id_produit, id_categorie, nomprod as nom, prix_ttc as prix_art, descriptif, stock, id_vendeur from alizonbdd._produit WHERE (nomprod like '%".$keyword."%') AND (prix_ttc BETWEEN ".$minPrice." AND ".$maxPrice.") AND (id_vendeur = ".$vendeur.")");
            $produit_filtres->setFetchMode(PDO::FETCH_ASSOC);
            $produit_filtres->execute();
            break;
        //Cas ou le filtre vendeur n'est pas activé mais que celui du prix est appliqué
        case (isset($keyword) && (!isset($vendeur)) && (isset($minPrice))):
            // Exécution de la requête SQL pour sélectionner les produits avec un prix compris entre les valeurs de prix minimum et maximum et avec un vendeur
            $produit_filtres = $bdd->prepare("SELECT id_produit, id_categorie, nomprod as nom, prix_ttc as prix_art, descriptif, stock, id_vendeur from alizonbdd._produit WHERE (nomprod like '%".$keyword."%') AND (prix_ttc BETWEEN ".$minPrice." AND ".$maxPrice.")");
            $produit_filtres->setFetchMode(PDO::FETCH_ASSOC);
            $produit_filtres->execute();
            break;

        //Deuxième partiepour la page catégorie
        //Cas ou les deux filtres sont activés.
        case (isset($idcat) && (isset($vendeur)) && (isset($minPrice))):
            // Exécution de la requête SQL pour sélectionner les produits avec un prix compris entre les valeurs de prix minimum et maximum et qui correspondent aà l'id de catégorie 
            $produit_filtres = $bdd->prepare("SELECT _produit.stock,_produit.id_produit,_produit.id_categorie,_produit.nomprod as nom,_produit.prix_ttc as prix_art ,_produit.descriptif from alizonbdd._produit where (_produit.id_categorie = ".$idcat.") AND (prix_ttc BETWEEN ".$minPrice." AND ".$maxPrice.") AND (id_vendeur = ".$vendeur.")");
            $produit_filtres->setFetchMode(PDO::FETCH_ASSOC);
            $produit_filtres->execute();
        break;
        //Cas ou il n'y a que le filtre des prix qui est activé
        case (isset($idcat) && (!isset($vendeur)) && (isset($minPrice))):
            // Exécution de la requête SQL pour sélectionner les produits avec un prix compris entre les valeurs de prix minimum et maximum et qui correspondent aà l'id de catégorie 
            $produit_filtres = $bdd->prepare("SELECT _produit.stock,_produit.id_produit,_produit.id_categorie,_produit.nomprod as nom,_produit.prix_ttc as prix_art ,_produit.descriptif from alizonbdd._produit where (_produit.id_categorie = ".$idcat.") AND (prix_ttc BETWEEN ".$minPrice." AND ".$maxPrice.")");
            $produit_filtres->setFetchMode(PDO::FETCH_ASSOC);
            $produit_filtres->execute();
        break;

    }
    //On créé un tableau temporaire pour pouvoir le trier en fonction de celui selecionner.
    $tab = array();

    foreach($produit_filtres as $row){
        array_push($tab, $row);
    }

    switch($tri){
        case "default":
            usort($tab, function($a, $b) {
                return $a['id_produit'] - $b['id_produit'];
            });
            break;
        case "prix_croiss":
            usort($tab, function($a, $b) {
                return $a['prix_art'] <=> $b['prix_art'];
            });
            break;
        case "prix_decroiss":
            usort($tab, function($a, $b) {
                return $b['prix_art'] <=> $a['prix_art'];
            });
            break;
        case "nouveau":
            usort($tab, function($a, $b) {
                return $b['id_produit'] - $a['id_produit'];
            });
            break;               
    }
    

    //Puis on les affichent avec 'carte_produit'
    foreach($tab as $key){
        $key['nom']=ucfirst($key['nom']);
        include("./carte_produit.php");
    }
?>
