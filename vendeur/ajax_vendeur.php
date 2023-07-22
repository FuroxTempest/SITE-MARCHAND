<?php
// connexion à la base de données 
    include("../php/connect_params.php");
    $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
    [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
    $id=$_POST['id']; // on récupère la variable passé avec ajax
    
    
    $req1=$bdd->prepare("SELECT date_commande,statut_commande from alizonbdd._commande WHERE id_commande=?"); //on sélectionne la date et le statut de la commande 
    $req1->execute([$id]);
    $info_prod=$req1->fetch();
    $date=$info_prod['date_commande'];
    $statut_com=$info_prod['statut_commande'];

    $mtn=time(); // on set une variable avec la date atuelle
    $date_com=strtotime($date); // on crée une autre variable avec la date de la commande convertie en timestamp 

    $Ec_prepa_livrai=25; // durée pour l'intervalle de temps entre le statut préparation et livraison
    $Ec_livrai_livré=45; // durée pour l'intervalle de temps entre le statut préparation et et livrée

    $ecart=$mtn-$date_com; // on calcule la durée depuis laquelle la commande à été passé

    if(($ecart>$Ec_prepa_livrai && $ecart< $Ec_livrai_livré) && $statut_com="en cours"){ // on regarde le statut de la commande et si la durée depuis laquelle la commande a été passé correspond pour passé au statut "livraison"
        $update=$bdd->prepare("UPDATE alizonbdd._commande SET statut_commande='livraison' WHERE id_commande=?;"); // on met à jour le statut de la commande
        $update->execute([$id]);

        $req2=$bdd->prepare("SELECT statut_commande from alizonbdd._commande WHERE id_commande=?;"); // on récupère le statut de la commande
        $req2->execute([$id]);
        $resu=$req2->fetch();
        $statut_com=$resu['statut_commande']; // on récupère le nouveau statu de la commande pour le renvoyer via ajax après
    }else if(($ecart > $Ec_livrai_livré) && $statut_com="livraison"){ // on regarde le statut de la commande et si la durée depuis laquelle la commande a été passé correspond pour passé au statut "livrée"
        $update2=$bdd->prepare("UPDATE alizonbdd._commande SET statut_commande='colis livrée' WHERE id_commande=?;"); // on met à jour le statut de la commande
        $update2->execute([$id]);
        $update2->execute([$id]);

        $req3=$bdd->prepare("SELECT statut_commande from alizonbdd._commande WHERE id_commande=?;");
        $req3->execute([$id]);
        $resu=$req3->fetch();
        $statut_com=$resu['statut_commande'];
    }

    
    echo $statut_com; // on renvoie le satut de la commande pour le mettre à jour via ajax
    unset($statut_com);
    

?>
