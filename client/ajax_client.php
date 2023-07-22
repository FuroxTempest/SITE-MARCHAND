<?php
    include("../php/connect_params.php");
    $bdd = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass,
    [PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
    $id=$_POST['id'];
    $mtn =time();

    $req1=$bdd->prepare("SELECT date_commande,statut_commande from alizonbdd._commande WHERE id_commande=?");
    $req1->execute([$id]);
    $info_prod=$req1->fetch();
    $date=$info_prod['date_commande'];
    $statut_com=$info_prod['statut_commande'];

    $mtn=time();
    $date_com=strtotime($date);

    $Ec_prepa_livrai=30;
    $Ec_livrai_livré=45;

    $ecart=$mtn-$date_com;

    if(($ecart>$Ec_prepa_livrai && $ecart< $Ec_livrai_livré) && $statut_com="en cours"){
        $update=$bdd->prepare("UPDATE alizonbdd._commande SET statut_commande='livraison' WHERE id_commande=?;");
        $update->execute([$id]);

        $req2=$bdd->prepare("SELECT statut_commande from alizonbdd._commande WHERE id_commande=?;");
        $req2->execute([$id]);
        $resu=$req2->fetch();
        $statut_com=$resu['statut_commande'];
    }else if(($ecart > $Ec_livrai_livré) && $statut_com="livraison"){
        $update2=$bdd->prepare("UPDATE alizonbdd._commande SET statut_commande='colis livrée' WHERE id_commande=?;");
        $update2->execute([$id]);

        $req3=$bdd->prepare("SELECT statut_commande from alizonbdd._commande WHERE id_commande=?;");
        $req3->execute([$id]);
        $resu=$req3->fetch();
        $statut_com=$resu['statut_commande'];
    }

    
    echo $statut_com;
    unset($statut_com);
    

?>
